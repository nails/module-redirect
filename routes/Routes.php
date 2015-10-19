<?php

/**
 * Generates Redirect routes
 *
 * @package     Nails
 * @subpackage  module-redirect
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Routes\Redirect;

use Nails\Factory;

class Routes
{
    /**
     * Returns an array of routes for this module
     * @return array
     */
    public function getRoutes()
    {
        $aRoutes        = array();
        $oRedirectModel = Factory::model('Redirect', 'nailsapp/module-redirect');
        $aRedirects     = $oRedirectModel->get_all();

        foreach ($aRedirects as $oRedirect) {

            $aRoutes[$oRedirect->old_url] = 'redirect/redirect/index/' . $oRedirect->id;
        }

        return $aRoutes;
    }
}
