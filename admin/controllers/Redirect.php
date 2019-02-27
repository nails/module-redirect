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

namespace Nails\Admin\Redirect;

use Nails\Admin\Controller\DefaultController;
use Nails\Admin\Helper;
use Nails\Common\Exception\ValidationException;
use Nails\Factory;

class Redirect extends DefaultController
{
    const CONFIG_MODEL_NAME           = 'Redirect';
    const CONFIG_MODEL_PROVIDER       = 'nails/module-redirect';
    const CONFIG_SORT_OPTIONS         = [
        'Created'  => 'created',
        'Modified' => 'modified',
        'Old URL'  => 'old_url',
        'New URL'  => 'new_url',
    ];
    const CONFIG_INDEX_FIELDS         = [
        'Old URL'  => 'old_url',
        'New URL'  => 'new_url',
        'Created'  => 'created',
        'Modified' => 'modified',
    ];
    const CONFIG_INDEX_HEADER_BUTTONS = [
        ['admin/redirect/redirect/batch', 'Batch Edit', 'default'],
        ['admin/redirect/redirect/download', 'Download as CSV', 'default'],
    ];

    // --------------------------------------------------------------------------

    /**
     * Allows for batch editing of the Redirects database
     */
    public function batch(): void
    {
        $oInput = Factory::service('Input');
        if ($oInput->post()) {
            try {

                $aFile = getFromArray('upload', $_FILES);
                if (empty($aFile)) {
                    throw new ValidationException('No CSV was uploaded.');
                } elseif ($aFile['error'] !== UPLOAD_ERR_OK) {
                    $oCdn = Factory::service('Cdn', 'nails/module-cdn');
                    throw new ValidationException('CSV failed to upload: ' . $oCdn::getUploadError($aFile['error']));
                }

                $sAction = $oInput->post('action');
                if (!in_array($sAction, ['APPEND', 'REPLACE', 'REMOVE'])) {
                    throw new ValidationException('Invalid Action');
                }

                // --------------------------------------------------------------------------

                $rFile = fopen($aFile['tmp_name'], 'r');
                if (empty($rFile)) {
                    throw new \Exception('Failed to open CSV for reading.');
                }

                //  Validate the contents
                $iCounter         = 0;
                $sValidUrlPattern = '/^(https?:\/\/.+|\/.*)$/i';
                while (($aData = fgetcsv($rFile)) !== false) {
                    $iCounter++;
                    if (!empty($aData)) {

                        $sOldUrl = trim(getFromArray(0, $aData));
                        $sNewUrl = trim(getFromArray(1, $aData));
                        $sType   = trim(getFromArray(2, $aData));

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

                $oDb    = Factory::service('Database');
                $oModel = Factory::model('Redirect', 'nails/module-redirect');
                $sTable = $oModel->getTableName();

                if ($sAction === 'REPLACE') {
                    $oDb->truncate($sTable);
                }

                $iCounter = 0;
                while (($aData = fgetcsv($rFile)) !== false) {
                    $iCounter++;
                    if (!empty($aData)) {

                        $sOldUrl = trim(getFromArray(0, $aData));
                        $sNewUrl = trim(getFromArray(1, $aData));
                        $sType   = trim(getFromArray(2, $aData));

                        if ($sOldUrl == 'old_url' || $sNewUrl == 'new_url' || $sType == 'type') {
                            continue;
                        }

                        $sOldUrl = $oModel::normaliseUrl($sOldUrl);
                        $sNewUrl = $oModel::normaliseUrl($sNewUrl);

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

                $oSession = Factory::service('Session', 'nails/module-auth');
                $oSession->setFlashData('success', 'Redirects processed successfully.');
                redirect('admin/redirect/redirect/batch');

            } catch (\Exception $e) {
                $this->data['error'] = $e->getMessage();
            }
        }

        $this->data['page']->title = 'Redirects &rsaquo; Batch Edit';
        Helper::loadView('batch');
    }

    // --------------------------------------------------------------------------

    /**
     * Download all redirects as a CSV
     */
    public function download(): void
    {
        $oModel = Factory::model('Redirect', 'nails/module-redirect');
        $oQuery = $oModel->getAllRawQuery([
            'select' => ['old_url', 'new_url', 'type'],
        ]);
        Helper::loadCsv($oQuery, 'redirects.csv', true);
    }
}
