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
use PDO;

class Routes extends BaseRoutes
{
    /**
     * Returns an array of routes for this module
     *
     * @return array
     */
    public function getRoutes()
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
