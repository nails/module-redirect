<?php

namespace Nails\Redirect;

use Nails\Common;
use Nails\Common\Events\Base;
use Nails\Factory;

class Events extends Base
{
    public function autoload()
    {
        return [
            Factory::factory('EventSubscription')
                   ->setEvent(Common\Events::SYSTEM_READY)
                   ->setNamespace('nails/common')
                   ->setCallback([$this, 'redirects']),
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * Facilitate redirects
     */
    public function redirects()
    {
        $oInput = Factory::service('Input');
        $oModel = Factory::model('Redirect', 'nails/module-redirect');

        $sUri = $oInput->server('REQUEST_URI');
        $sUri = '/' . trim($sUri, '/');

        /**
         * Using PDO to craft our own query as CI manipulates the string if there's
         * a query string when attempting to protect identifiers.
         */
        $oPdoDb        = Factory::service('PDODatabase');
        $oPdoStatement = $oPdoDb->prepare('SELECT * FROM `' . $oModel->getTableName() . '` WHERE old_url = :old_url');
        $oPdoStatement->execute(['old_url' => $sUri]);
        $aResults = $oPdoStatement->fetchAll(\PDO::FETCH_OBJ);

        if (!empty($aResults)) {
            $oRedirect = reset($aResults);
            //  Avoid loops
            if ($sUri !== $oRedirect->new_url) {
                redirect($oRedirect->new_url, 'location', $oRedirect->type);
            }
        }
    }
}
