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

use Nails\Common\Model\BaseRoutes;
use Nails\Factory;

class Routes extends BaseRoutes
{
    /**
     * Returns an array of routes for this module
     *
     * @return array
     */
    public function getRoutes()
    {
        $aRoutes        = [];
        $oRedirectModel = Factory::model('Redirect', 'nailsapp/module-redirect');
        $aRedirects     = $oRedirectModel->getAll();

        foreach ($aRedirects as $oRedirect) {

            $sUrl = site_url($oRedirect->old_url);
            $sUrl = preg_replace('/^' . preg_quote(BASE_URL, '/') . '/', '', $sUrl);
            $sUrl = preg_replace('/^' . preg_quote(SECURE_BASE_URL, '/') . '/', '', $sUrl);

            $aRoutes[$sUrl] = 'redirect/redirect/index/' . $oRedirect->id;
        }

        return $aRoutes;
    }
}
