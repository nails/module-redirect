<?php

/**
 * Migration: 2
 * Started:   21/08/2025
 */

namespace Nails\Redirect\Database\Migration;

use Nails\Common\Interfaces;
use Nails\Common\Traits;
use Nails\Redirect\Admin\Permission;

/**
 * Class Migration2
 *
 * @package Nails\Cms\Database\Migration
 */
class Migration2 implements Interfaces\Database\Migration
{
    use Traits\Database\Migration;

    // --------------------------------------------------------------------------

    /**
     * Execute the migration
     */
    public function execute(): void
    {
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}redirect` CHARACTER SET = utf8mb4;');
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}redirect` CHANGE `old_url` `old_url` VARCHAR(500) CHARACTER SET utf8mb4 NULL DEFAULT NULL;');
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}redirect` CHANGE `new_url` `new_url` VARCHAR(500) CHARACTER SET utf8mb4 NULL DEFAULT NULL;');
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}redirect` CHANGE `type` `type` ENUM('301','302') CHARACTER SET utf8mb4 NULL DEFAULT '301';');
    }
}
