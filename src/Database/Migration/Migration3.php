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
class Migration3 implements Interfaces\Database\Migration
{
    use Traits\Database\Migration;

    // --------------------------------------------------------------------------

    /**
     * Execute the migration
     */
    public function execute(): void
    {
        /**
         * Applications moving from `pre-new-admin` to `develop` will be on migration 2. This means that they
         * * will not run the permission upgrade (migration 2). They WILL have the column changes, however
         * * (develop: 3, pre-new-admin: 2).
         *
         * This migration is safe to run twice
         */
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}redirect` CHARACTER SET = utf8mb4;');
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}redirect` CHANGE `old_url` `old_url` VARCHAR(500) CHARACTER SET utf8mb4 NULL DEFAULT NULL;');
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}redirect` CHANGE `new_url` `new_url` VARCHAR(500) CHARACTER SET utf8mb4 NULL DEFAULT NULL;');
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}redirect` CHANGE `type` `type` ENUM(\'301\',\'302\') CHARACTER SET utf8mb4 NULL DEFAULT \'301\';');
    }
}
