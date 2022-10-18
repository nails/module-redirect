<?php

namespace Nails\Redirect\Admin\Permission;

use Nails\Admin\Interfaces\Permission;

class Delete implements Permission
{
    public function label(): string
    {
        return 'Can delete redirects';
    }

    public function group(): string
    {
        return 'Redirects';
    }
}
