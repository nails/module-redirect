<?php

use Nails\Factory;
use Nails\Common\Controller\Base;

class Redirect extends Base
{
    public function index()
    {
        $oRedirectModel = Factory::model('Redirect', 'nailsapp/module-redirect');
        $oRedirect      = $oRedirectModel->get_by_id($this->uri->rsegment(3));

        if (empty($oRedirect)) {

            show_404();
        }

        redirect($oRedirect->new_url, 'location', $oRedirect->type);
    }
}
