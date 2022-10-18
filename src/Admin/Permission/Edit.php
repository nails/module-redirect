<?php

namespace Nails\Redirect\Admin\Permission;

use Nails\Admin\Interfaces\Permission;

class Edit implements Permission
{
    public function label(): string
    {
        return 'Can edit redirects';
    }

    public function group(): string
    {
        return 'Redirects';
    }
}
