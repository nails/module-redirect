<?php

namespace Nails\Redirect\Admin\Permission;

use Nails\Admin\Interfaces\Permission;

class Browse implements Permission
{
    public function label(): string
    {
        return 'Can browse redirects';
    }

    public function group(): string
    {
        return 'Redirects';
    }
}
