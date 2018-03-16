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
        $this->searchableFields  = ['old_url', 'neW_url'];
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

    // --------------------------------------------------------------------------

    /**
     * Normalises a URL to just its path and query components
     *
     * @param stirng $sUrl The URL to normalise
     *
     * @throws \Exception
     * @return string
     */
    public static function normaliseUrl($sUrl)
    {
        $aUrl = parse_url($sUrl);
        if (!is_array($aUrl)) {
            throw new \Exception('Failed to parse URL (' . $sUrl . ')');
        }
        return implode(
            '?',
            array_filter([
                getFromArray('path', $aUrl),
                getFromArray('query', $aUrl),
            ])
        );
    }
}
