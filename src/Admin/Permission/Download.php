<?php

namespace Nails\Redirect\Admin\Permission;

use Nails\Admin\Interfaces\Permission;

class Download implements Permission
{
    public function label(): string
    {
        return 'Can download redirects';
    }

    public function group(): string
    {
        return 'Redirects';
    }
}
