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
use Nails\Admin\Controller\Base;

class Redirect extends Base
{
    /**
     * Announces this controller's navGroups
     * @return stdClass
     */
    public static function announce()
    {
        if (userHasPermission('admin:redirect:redirect:manage')) {

            $navGroup = new \Nails\Admin\Nav('Redirects', 'fa-arrow-circle-o-right');
            $navGroup->addAction('Manage Redirects');

            return $navGroup;
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

        $oRedirectModel = Factory::model('Redirect', 'nailsapp/module-redirect');

        // --------------------------------------------------------------------------

        if ($this->input->post()) {

            $old = $this->input->post('old');
            $new = $this->input->post('new');
            $type = $this->input->post('type');

            //  TODO: Validation and re-setting of values

            //  Check we don't have any blank entries in our post arrays, then update if so
            if (count($old) == count(array_filter($old)) && count($new) == count(array_filter($new))) {

                $aCombined = array();
                for ($iCount = 0; $iCount < count($old); $iCount++) {

                    $aCombined[] = array(
                        'old' => $old[$iCount],
                        'new' => $new[$iCount],
                        'type' => $type[$iCount]
                    );
                }

                // --------------------------------------------------------------------------

                if ($oRedirectModel->truncateAll() && $oRedirectModel->insertBatch($aCombined)) {

                    $this->session->set_flashdata('success', lang('redirects_edit_ok'));
                }

            } else {

                // TODO: Error handling
                $this->session->set_flashdata('error', lang('redirects_edit_fail_empty_rows'));
            }
        }

        // --------------------------------------------------------------------------

        $this->data['redirects'] = $oRedirectModel->get_all();

        // --------------------------------------------------------------------------

        //  Set method info
        $this->data['page']->title = lang('redirects_index_title');

        // --------------------------------------------------------------------------

        \Nails\Admin\Helper::loadView('index');
    }
}
