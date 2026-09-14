<?php

/**
 * Migration: 2
 * Started:   11/08/2022
 */

namespace Nails\Redirect\Database\Migration;

use Nails\Admin\Traits\Database\Migration\PermissionMap;
use Nails\Common\Interfaces;
use Nails\Common\Traits;
use Nails\Redirect\Admin\Permission;

/**
 * Class Migration2
 *
 * Repeatable because `feature/pre-new-admin` has no equivalent migration, so an app
 * arriving from that branch resumes above this number and would never run it.
 *
 * @package Nails\Redirect\Database\Migration
 */
class Migration2 implements Interfaces\Database\Migration\Repeatable
{
    use Traits\Database\Migration;
    use PermissionMap;

    // --------------------------------------------------------------------------

    const MAP = [
        'admin:redirect:redirect:browse'   => Permission\Browse::class,
        'admin:redirect:redirect:create'   => Permission\Create::class,
        'admin:redirect:redirect:edit'     => Permission\Edit::class,
        'admin:redirect:redirect:delete'   => Permission\Delete::class,
        'admin:redirect:redirect:restore'  => '',
        'admin:redirect:redirect:downlaod' => Permission\Download::class,
    ];
}
