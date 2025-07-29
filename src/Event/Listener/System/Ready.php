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
use Nails\Common\Exception\Database\ConnectionException;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Factory;
use Nails\Redirect\Constants;
use Nails\Redirect\Service\Redirect;

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
     * @throws ConnectionException
     */
    public function execute(): void
    {
        /** @var Redirect $oService */
        $oService  = Factory::service('Redirect', Constants::MODULE_SLUG);
        $aRedirect = $oService->detectRedirect();

        if (!empty($aRedirect)) {
            [$sUrl, $sType] = $aRedirect;
            redirect(
                sUrl: $sUrl,
                sMethod: 'location',
                iHttpResponseCode: (int) $sType,
                bAllowExternal: true
            );
        }
    }
}
