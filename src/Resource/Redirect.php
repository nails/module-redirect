<?php

namespace Nails\Redirect\Resource;

use Nails\Common\Resource\Entity;

/**
 * Class Redirect
 *
 * @package Nails\Redirect\Resource
 */
class Redirect extends Entity
{
    /** @var string */
    public $old_url;

    /** @var string */
    public $new_url;

    /** @var string */
    public $type;
}
