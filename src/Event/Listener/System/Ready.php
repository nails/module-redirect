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
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Factory;
use Nails\Redirect\Constants;

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
        /** @var \Nails\Redirect\Service\Redirect $oService */
        $oService  = Factory::service('Redirect', Constants::MODULE_SLUG);
        $oRedirect = $oService->detectRedirect();

        if (!empty($oRedirect)) {
            redirect($oRedirect->new_url, 'location', $oRedirect->type);
        }
    }
}
