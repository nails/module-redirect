<?php

namespace Nails\Redirect\FindReplace\Exclusion;

use Nails\FindReplace\Interfaces\Exclusion;

class Package implements Exclusion
{
    public function hidden(): array
    {
        return [];
    }

    public function readOnly(): array
    {
        return [
            NAILS_DB_PREFIX . 'redirect.old_url',
            NAILS_DB_PREFIX . 'redirect.new_url',
        ];
    }
}
