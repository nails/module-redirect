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
     * Model constructor
     **/
    public function __construct()
    {
        parent::__construct();
        $this->table             = NAILS_DB_PREFIX . 'redirect';
        $this->tableAlias       = 't';
        $this->defaultSortColumn = null;
    }

    // --------------------------------------------------------------------------

    //  @todo base mdoel should provide this functionality
    public function insertBatch($aData)
    {
        return $this->db->insert_batch($this->table, $aData);
    }

    // --------------------------------------------------------------------------

    //  @todo base mdoel should provide this functionality (maybe, awfully destructive)
    public function truncateAll()
    {
        return $this->db->truncate($this->table);
    }
}
