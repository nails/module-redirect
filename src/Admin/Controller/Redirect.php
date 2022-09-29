<?php

/**
 * Manage redirects
 *
 * @package     Nails
 * @subpackage  module-redirect
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Redirect\Admin\Controller;

use Nails\Admin\Controller\DefaultController;
use Nails\Admin\Helper;
use Nails\Cdn;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\NailsException;
use Nails\Common\Exception\ValidationException;
use Nails\Common\Helper\ArrayHelper;
use Nails\Common\Service\Database;
use Nails\Common\Service\Input;
use Nails\Factory;
use Nails\Redirect\Admin\Permission;
use Nails\Redirect\Constants;

/**
 * Class Redirect
 *
 * @package Nails\Admin\Redirect
 */
class Redirect extends DefaultController
{
    const CONFIG_MODEL_NAME        = 'Redirect';
    const CONFIG_MODEL_PROVIDER    = Constants::MODULE_SLUG;
    const CONFIG_SIDEBAR_GROUP     = 'Utilities';
    const CONFIG_SIDEBAR_FORMAT    = '%s';
    const CONFIG_SORT_OPTIONS      = [
        'Created'  => 'created',
        'Modified' => 'modified',
        'Old URL'  => 'old_url',
        'New URL'  => 'new_url',
    ];
    const CONFIG_INDEX_FIELDS      = [
        'Old URL'  => 'old_url',
        'New URL'  => 'new_url',
        'Type'     => 'type',
        'Created'  => 'created',
        'Modified' => 'modified',
    ];
    const CONFIG_PERMISSION_BROWSE = Permission\Browse::class;
    const CONFIG_PERMISSION_CREATE = Permission\Create::class;
    const CONFIG_PERMISSION_EDIT   = Permission\Edit::class;
    const CONFIG_PERMISSION_DELETE = Permission\Delete::class;

    // --------------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();

        $this->addIndexHeaderButton(self::url('batch'), 'Batch Edit', 'default');

        if (userHasPermission(Permission\Download::class)) {
            $this->addIndexHeaderButton(self::url('download'), 'Download as CSV', 'default');
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Allows for batch editing of the Redirects database
     *
     * @throws FactoryException
     */
    public function batch(): void
    {
        if (!self::isEditButtonEnabled()) {
            unauthorised();
        }

        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        if ($oInput->post()) {
            try {

                $aFile = ArrayHelper::get('upload', $_FILES);
                if (empty($aFile)) {
                    throw new ValidationException('No CSV was uploaded.');
                } elseif ($aFile['error'] !== UPLOAD_ERR_OK) {
                    $oCdn = Factory::service('Cdn', Cdn\Constants::MODULE_SLUG);
                    throw new ValidationException('CSV failed to upload: ' . $oCdn::getUploadError($aFile['error']));
                }

                $sAction = $oInput->post('action');
                if (!in_array($sAction, ['APPEND', 'REPLACE', 'REMOVE'])) {
                    throw new ValidationException('Invalid Action');
                }

                // --------------------------------------------------------------------------

                $rFile = fopen($aFile['tmp_name'], 'r');
                if (empty($rFile)) {
                    throw new NailsException('Failed to open CSV for reading.');
                }

                //  Validate the contents
                $iCounter         = 0;
                $sValidUrlPattern = '/^(https?:\/\/.+|\/.*)$/i';
                while (($aData = fgetcsv($rFile)) !== false) {
                    $iCounter++;
                    if (!empty($aData)) {

                        $sOldUrl = trim(ArrayHelper::get(0, $aData));
                        $sNewUrl = trim(ArrayHelper::get(1, $aData));
                        $sType   = trim(ArrayHelper::get(2, $aData));

                        if ($sOldUrl == 'old_url' || $sNewUrl == 'new_url' || $sType == 'type') {
                            continue;
                        }

                        if (empty($sOldUrl)) {
                            throw new ValidationException('Old URL empty at item #' . $iCounter);
                        } elseif (!preg_match($sValidUrlPattern, $sOldUrl)) {
                            throw new ValidationException('Old URL invalid at item #' . $iCounter . ' (' . $sOldUrl . ')');
                        } elseif (empty($sNewUrl)) {
                            throw new ValidationException('New URL empty at item #' . $iCounter);
                        } elseif (!preg_match($sValidUrlPattern, $sNewUrl)) {
                            throw new ValidationException('New URL invalid at item #' . $iCounter . ' (' . $sNewUrl . ')');
                        } elseif (!in_array($sType, ['301', '302'])) {
                            throw new ValidationException('Invalid redirect type (' . $sType . ') at item #' . $iCounter);
                        }
                    }
                }

                rewind($rFile);

                /** @var Database $oDb */
                $oDb = Factory::service('Database');
                /** @var \Nails\Redirect\Model\Redirect $oModel */
                $oModel = Factory::model('Redirect', Constants::MODULE_SLUG);

                $sTable = $oModel->getTableName();

                if ($sAction === 'REPLACE') {
                    $oDb->truncate($sTable);
                }

                $iCounter = 0;
                while (($aData = fgetcsv($rFile)) !== false) {
                    $iCounter++;
                    if (!empty($aData)) {

                        $sOldUrl = trim(ArrayHelper::get(0, $aData));
                        $sNewUrl = trim(ArrayHelper::get(1, $aData));
                        $sType   = trim(ArrayHelper::get(2, $aData));

                        if ($sOldUrl == 'old_url' || $sNewUrl == 'new_url' || $sType == 'type') {
                            continue;
                        }

                        $sOldUrl = \Nails\Redirect\Helper\Redirect::normaliseUrl($sOldUrl);
                        $sNewUrl = \Nails\Redirect\Helper\Redirect::normaliseUrl($sNewUrl);

                        $oDb->where('old_url', $sOldUrl);
                        $bExists = (bool) $oDb->count_all_results($sTable);

                        switch ($sAction) {
                            case 'APPEND':
                                $oDb->where('old_url', $sOldUrl);
                                if (!(bool) $oDb->count_all_results($sTable)) {
                                    $oModel->create([
                                        'old_url' => $sOldUrl,
                                        'new_url' => $sNewUrl,
                                        'type'    => $sType,
                                    ]);
                                }
                                break;
                            case 'REPLACE':
                                $oModel->create([
                                    'old_url' => $sOldUrl,
                                    'new_url' => $sNewUrl,
                                    'type'    => $sType,
                                ]);
                                break;
                            case 'REMOVE':
                                $oDb->where('old_url', $sOldUrl);
                                $oDb->delete($sTable);
                                break;
                        }

                        $oDb->flushCache();
                    }
                }

                // --------------------------------------------------------------------------

                $this->oUserFeedback->success('Redirects processed successfully.');
                redirect(Redirect::url('batch'));

            } catch (\Exception $e) {
                $this->oUserFeedback->error($e->getMessage());
            }
        }

        $this
            ->setTitles(['Redirects', 'Batch Edit'])
            ->loadView('batch');
    }

    // --------------------------------------------------------------------------

    /**
     * Download all redirects as a CSV
     */
    public function download(): void
    {
        if (!userHasPermission(Permission\Download::class)) {
            unauthorised();
        }

        /** @var \Nails\Redirect\Model\Redirect $oModel */
        $oModel = Factory::model('Redirect', Constants::MODULE_SLUG);
        $oQuery = $oModel->getAllRawQuery([
            'select' => ['old_url', 'new_url', 'type'],
        ]);
        Helper::loadCsv($oQuery, 'redirects.csv', true);
    }
}
