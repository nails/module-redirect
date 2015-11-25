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

use Nails\Factory;
use Nails\Admin\Helper;
use Nails\Redirect\Controller\BaseAdmin;

class Redirect extends BaseAdmin
{
    /**
     * Announces this controller's navGroups
     * @return stdClass
     */
    public static function announce()
    {
        if (userHasPermission('admin:redirect:redirect:manage')) {

            $oNavGroup = Factory::factory('Nav', 'nailsapp/module-admin');
            $oNavGroup->setLabel('Redirects');
            $oNavGroup->setIcon('fa-arrow-circle-o-right');
            $oNavGroup->addAction('Manage Redirects');

            return $oNavGroup;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an array of extra permissions for this controller
     * @return array
     */
    public static function permissions()
    {
        $permissions = parent::permissions();

        $permissions['manage'] = 'Can manage redirects';

        return $permissions;
    }

    // --------------------------------------------------------------------------

    /**
     * Constructs the controller
     */
    public function __construct()
    {
        parent::__construct();

        // --------------------------------------------------------------------------

        $this->lang->load('admin_redirect');
    }

    // --------------------------------------------------------------------------

    /**
     * Browse redirects
     * @return void
     */
    public function index()
    {
        if (!userHasPermission('admin:redirect:redirect:manage')) {

            unauthorised();
        }

        // --------------------------------------------------------------------------

        $oRoutesModel   = Factory::model('Routes');
        $oRedirectModel = Factory::model('Redirect', 'nailsapp/module-redirect');

        // --------------------------------------------------------------------------

        if ($this->input->post()) {

            $aOld  = $this->input->post('old_url');
            $aNew  = $this->input->post('new_url');
            $aType = $this->input->post('type');

            $aCombined = array();
            for ($i = 0; $i < count($aOld); $i++) {

                if ($aOld[$i] || $aNew[$i] || $aType[$i]) {
                    $aCombined[] = array(
                        'old_url' => trim($aOld[$i]),
                        'new_url' => trim($aNew[$i]),
                        'type'    => $aType[$i]
                    );
                }
            }

            //  Check we don't have any blank entries in our post arrays, then update if so
            if (!$aOld && !$aNew && !$aType) {

                if ($oRedirectModel->truncateAll()) {

                    $this->data['success'] = lang('redirects_edit_ok');
                    $oRoutesModel->update();

                } else {

                    $this->data['error'] = 'Failed to remove redirects. ' . $oRedirectModel->lastError();
                }

            } else {

                $bOkOld  = count($aOld) == count(array_filter($aOld));
                $bOkNew  = count($aNew) == count(array_filter($aNew));
                $bOkType = count($aType) == count(array_filter($aType));

                if ($bOkOld && $bOkNew && $bOkType) {

                    if ($oRedirectModel->truncateAll() && $oRedirectModel->insertBatch($aCombined)) {

                        $this->data['success'] = lang('redirects_edit_ok');
                        $oRoutesModel->update();

                    } else {

                        $this->data['error'] = 'Failed to save redirects. ' . $oRedirectModel->lastError();
                    }

                } else {

                    $this->data['error'] = lang('redirects_edit_fail_empty_rows');
                }
            }

            $aRedirects = $aCombined;

        } else {

            $aRedirects = $oRedirectModel->getAll();
        }

        // --------------------------------------------------------------------------

        //  Set method info
        $this->data['page']->title = lang('redirects_index_title');

        // --------------------------------------------------------------------------

        $this->asset->load('knockout/dist/knockout.js', 'NAILS-BOWER');
        $this->asset->load('nails.admin.redirect.min.js', 'NAILS');
        $this->asset->inline(
            'ko.applyBindings(new redirects(' . json_encode($aRedirects) . '));',
            'JS'
        );
        echo adminHelper('loadView', 'index');
    }
}
