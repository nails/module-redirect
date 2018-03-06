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
use Nails\Factory;

class Redirect extends DefaultController
{
    const CONFIG_MODEL_NAME           = 'Redirect';
    const CONFIG_MODEL_PROVIDER       = 'nailsapp/module-redirect';
    const CONFIG_SORT_OPTIONS         = [
        'created'  => 'Created',
        'modified' => 'Modified',
        'old_url'  => 'Old URL',
        'new_url'  => 'New URL',
    ];
    const CONFIG_INDEX_FIELDS         = [
        'old_url'  => 'Old URL',
        'new_url'  => 'New URL',
        'created'  => 'Created',
        'modified' => 'Modified',
    ];
    const CONFIG_INDEX_HEADER_BUTTONS = [
        ['admin/redirect/redirect/batch', 'Batch Edit', 'default'],
    ];

    // --------------------------------------------------------------------------

    public function batch()
    {
        $oInput = Factory::service('Input');
        $oModel = Factory::model('Redirect', 'nailsapp/module-redirect');

        if ($oInput->post()) {
            try {

                $oFormValidation = Factory::service('FormValidation');
                $oFormValidation->set_rules('old', '', 'required');
                $oFormValidation->set_rules('new', '', 'required');
                $oFormValidation->set_rules('type', '', 'required');
                if (!$oFormValidation->run()) {
                    throw new \Exception('All fields are required.');
                }

                $aOldUrls      = explode("\n", trim($oInput->post('old')));
                $iCountOldUrls = count($aOldUrls);
                $aNewUrls      = explode("\n", trim($oInput->post('new')));
                $iCountNewUrls = count($aNewUrls);
                $aType         = explode("\n", trim($oInput->post('type')));
                $iCountType    = count($aType);

                if ($iCountOldUrls !== $iCountNewUrls && $iCountOldUrls !== $iCountType) {
                    throw new \Exception('There must be an equal number of lines in each field.');
                }

                $aAllowedTypes = [301, 302];
                $aInvalidTypes = array_diff(array_unique($aType), $aAllowedTypes);
                if (!empty($aInvalidTypes)) {
                    throw new \Exception('The following redirect types are invalid: ' . implode(', ', $aInvalidTypes));
                }

                $aRedirects = [];
                for ($i = 0; $i < $iCountOldUrls; $i++) {
                    $aRedirects[$aOldUrls[$i]] = [$aNewUrls[$i], $aType[$i]];
                }

                $oModel->truncate();
                foreach ($aRedirects as $sOldUrl => $aNewUrl) {
                    $oModel->create(['old_url' => $sOldUrl, 'new_url' => $aNewUrl[0], 'type' => $aNewUrl[1]]);
                }

                $oRoutesModel = Factory::model('Routes');
                $oRoutesModel->update();

                $oSession = Factory::service('Session', 'nailsapp/module-auth');
                $oSession->setFlashData('success', 'Redirects updated successfully.');
                redirect('admin/redirect/redirect/batch');

            } catch (\Exception $e) {
                $this->data['error'] = $e->getMessage();
            }
        }

        $aRedirects = $oModel->getAll();
        $aOldUrls   = [];
        $aNewUrls   = [];
        $aTypes     = [];
        foreach ($aRedirects as $oRedirect) {
            $aOldUrls[] = $oRedirect->old_url;
            $aNewUrls[] = $oRedirect->new_url;
            $aTypes[]   = $oRedirect->type;
        }

        $this->data['sOldUrls'] = implode("\n", $aOldUrls);
        $this->data['sNewUrls'] = implode("\n", $aNewUrls);
        $this->data['sTypes']   = implode("\n", $aTypes);

        $oAsset = Factory::service('Asset');
        $oAsset->load('jquery.textareaLinesNumbers.js', 'nailsapp/module-redirect');
        $oAsset->load('jquery.textareaLinesNumbers.css', 'nailsapp/module-redirect');
        $oAsset->inline('$("textarea").textareaLinesNumbers()', 'JS');

        $this->data['page']->title = 'Redirects &rsaquo; Batch Edit';
        Helper::loadView('batch');
    }

    // --------------------------------------------------------------------------

    protected function afterCreateAndEdit($sMode, \stdClass $oNewItem, \stdClass $oOldItem = null)
    {
        parent::afterCreateAndEdit($sMode, $oNewItem, $oOldItem);
        //  @todo (Pablo - 2018-02-23) - rewrite routes
    }
}
