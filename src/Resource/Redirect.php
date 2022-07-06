<?php

namespace Nails\Redirect\Resource;

use Nails\Admin\Interfaces\ChangeLog;
use Nails\Common\Resource\Entity;

/**
 * Class Redirect
 *
 * @package Nails\Redirect\Resource
 */
class Redirect extends Entity implements ChangeLog
{
    /** @var string */
    public $old_url;

    /** @var string */
    public $new_url;

    /** @var string */
    public $type;

    // --------------------------------------------------------------------------

    /**
     *
     * @return string
     */
    public static function getChageLogTypeLabel(): string
    {
        return 'HTTP Redirect';
    }

    // --------------------------------------------------------------------------

    public static function getChageLogTypeUrl(): string
    {
        return 'admin/redirect/redirect/index';
    }
}
