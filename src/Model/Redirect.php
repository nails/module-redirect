<?php

/**
 * Redirect model
 *
 * @package     Nails
 * @subpackage  module-redirect
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Redirect\Model;

use Nails\Common\Model\Base;

class Redirect extends Base
{
    /**
     * Redirect constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->table             = NAILS_DB_PREFIX . 'redirect';
        $this->defaultSortColumn = null;
        $this->tableLabelColumn  = null;
    }

    // --------------------------------------------------------------------------

    /**
     * Describes the fields for this model automatically and with some guesswork;
     * for more fine grained control models should overload this method.
     *
     * @return array
     */
    public function describeFields()
    {
        $aFields = parent::describeFields();

        $aFields['old_url']->validation[] = 'required';
        $aFields['old_url']->validation[] = 'is_unique[' . $this->getTableName() . '.old_url]';
        $aFields['new_url']->validation[] = 'required';
        $aFields['type']->validation[]    = 'required';

        return $aFields;
    }
}
