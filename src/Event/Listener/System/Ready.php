<?php

/**
 * The Ready event listener
 *
 * @package  App
 * @category events
 */

namespace Nails\Redirect\Event\Listener\System;

use Nails\Common;
use Nails\Common\Events\Subscription;
use Nails\Factory;


/**
 * Class Ready
 *
 * @package App\Event\Listener\User
 */
class Ready extends Subscription
{
    /**
     * Ready constructor.
     */
    public function __construct()
    {
        $this
            ->setEvent(Common\Events::SYSTEM_READY)
            ->setCallback([$this, 'execute']);
    }

    // --------------------------------------------------------------------------

    /**
     * Called when the event is triggered
     *
     * @throws FactoryException
     * @throws ModelException
     */
    public function execute(): void
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
