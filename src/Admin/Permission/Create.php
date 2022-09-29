<?php

namespace Nails\Redirect\Admin\Permission;

use Nails\Admin\Interfaces\Permission;

class Create implements Permission
{
    public function label(): string
    {
        return 'Can create redirects';
    }

    public function group(): string
    {
        return 'Redirects';
    }
}
