<?php

/**
 * This class provides some common Redirect controller functionality in admin
 *
 * @package     Nails
 * @subpackage  module-redirect
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Redirect\Controller;

use Nails\Admin\Controller\Base;
use Nails\Factory;

class BaseAdmin extends Base
{
    public function __construct()
    {
        parent::__construct();
        $oAsset = Factory::service('Asset');
        $oAsset->load('admin.css', 'nailsapp/module-redirect');
    }
}
