<?php

/**
 * Migration:   1
 * Started:     22/03/2018
 * Finalised:   22/03/2018
 */

namespace Nails\Redirect\Database\Migration;

use Nails\Common\Console\Migrate\Base;

class Migration1 extends Base
{
    /**
     * Execute the migration
     * @return Void
     */
    public function execute()
    {
        $this->query("
            ALTER TABLE `{{NAILS_DB_PREFIX}}redirect`
                CHANGE `old_url` `old_url` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
        ");
        $this->query("
            ALTER TABLE `{{NAILS_DB_PREFIX}}redirect`
                CHANGE `new_url` `new_url` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
        ");
    }
}
