<?php

/**
 * Migration: 3
 * Started:   21/08/2025
 */

namespace Nails\Redirect\Database\Migration;

use Nails\Common\Interfaces;
use Nails\Common\Traits;

/**
 * Class Migration3
 *
 * @package Nails\Cms\Database\Migration
 */
class Migration4 extends Migration2
{
    /**
     * Applications moving from `pre-new-admin` to `develop` will be on migration 2. This means that they
     * will not run the permission upgrade (migration 2). They WILL have the column changes, however
     * (develop: 3, pre-new-admin: 2).
     *
     * This migration ensures that the permission migrations happen again - this operation is safe to run twice.
     */
}
