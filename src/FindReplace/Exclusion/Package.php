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
            //  Incoming requests are matched against this.
            NAILS_DB_PREFIX . 'redirect.old_url',
        ];
    }
}
