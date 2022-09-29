<?php

/**
 * Migration: 2
 * Started:   11/08/2022
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

    const MAP = [
        'admin:redirect:redirect:browse'   => Permission\Browse::class,
        'admin:redirect:redirect:create'   => Permission\Create::class,
        'admin:redirect:redirect:edit'     => Permission\Edit::class,
        'admin:redirect:redirect:delete'   => Permission\Delete::class,
        'admin:redirect:redirect:restore'  => '',
        'admin:redirect:redirect:downlaod' => Permission\Download::class,
    ];

    // --------------------------------------------------------------------------

    /**
     * Execute the migration
     */
    public function execute(): void
    {
        $oResult = $this->query('SELECT id, acl FROM `{{NAILS_DB_PREFIX}}user_group`');
        while ($row = $oResult->fetchObject()) {

            $acl = json_decode($row->acl) ?? [];

            foreach ($acl as &$old) {
                $old = self::MAP[$old] ?? $old;
            }

            $acl = array_filter($acl);
            $acl = array_unique($acl);
            $acl = array_values($acl);

            $this
                ->prepare('UPDATE `{{NAILS_DB_PREFIX}}user_group` SET `acl` = :acl WHERE `id` = :id')
                ->execute([
                    ':id'  => $row->id,
                    ':acl' => json_encode($acl),
                ]);
        }
    }
}
