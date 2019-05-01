<?php

namespace Nails\Redirect;

use Nails\Common\Events\Base;
use Nails\Redirect\Event\Listener\System;

class Events extends Base
{
    public function autoload()
    {
        return [
            new System\Ready(),
        ];
    }
}
