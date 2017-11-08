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

namespace Nails\Redirect;

use Nails\Common\Interfaces\RouteGenerator;
use Nails\Factory;
use PDO;


class Routes implements RouteGenerator
{
    /**
     * Returns an array of routes for this module
     * @return array
     */
    public static function generate()
    {
        $oDb            = Factory::service('ConsoleDatabase', 'nailsapp/module-console');
        $oRedirectModel = Factory::model('Redirect', 'nailsapp/module-redirect');
        $aRoutes        = [];

        $oRedirects = $oDb->query(
            'SELECT id, old_url FROM ' . $oRedirectModel->getTableName()
        );

        while ($oRow = $oRedirects->fetch(PDO::FETCH_OBJ)) {

            $sUrl = preg_replace('/^\//', '', $oRow->old_url);
            $sUrl = preg_replace('/^' . preg_quote(BASE_URL, '/') . '/', '', $sUrl);
            $sUrl = preg_replace('/^' . preg_quote(SECURE_BASE_URL, '/') . '/', '', $sUrl);

            $aRoutes[$sUrl] = 'redirect/redirect/index/' . $oRow->id;
        }

        return $aRoutes;
    }
}
