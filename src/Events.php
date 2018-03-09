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
                   ->setNamespace('nailsapp/common')
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
        $sUri   = $oInput->server('REQUEST_URI');
        $oModel = Factory::model('Redirect', 'nailsapp/module-redirect');

        $aResults = $oModel->getAll([
            'where' => [
                ['old_url', $sUri],
            ],
        ]);

        if (!empty($aResults)) {
            $oRedirect = reset($aResults);
            //  Avoid loops
            if ($sUri !== $oRedirect->new_url) {
                redirect($oRedirect->new_url, 'location', $oRedirect->type);
            }
        }
    }
}
