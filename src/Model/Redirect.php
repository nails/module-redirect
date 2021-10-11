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

use Nails\Common\Exception\NailsException;
use Nails\Common\Exception\ModelException;
use Nails\Common\Helper\ArrayHelper;
use Nails\Common\Helper\Form;
use Nails\Common\Model\Base;
use Nails\Common\Service\FormValidation;
use Nails\Common\Service\HttpCodes;
use Nails\Config;
use Nails\Redirect\Constants;

/**
 * Class Redirect
 *
 * @package Nails\Redirect\Model
 */
class Redirect extends Base
{
    /**
     * The table this model represents
     *
     * @var string
     */
    const TABLE = NAILS_DB_PREFIX . 'redirect';

    /**
     * The name of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_NAME = 'Redirect';

    /**
     * The provider of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_PROVIDER = Constants::MODULE_SLUG;

    // --------------------------------------------------------------------------

    /**
     * The name of the "label" column
     *
     * @var string
     */
    protected $tableLabelColumn = null;

    // --------------------------------------------------------------------------

    /**
     * Returns the searchable columns for this module
     *
     * @return string[]
     */
    public function getSearchableColumns(): array
    {
        return [
            'old_url',
            'new_url',
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * Describes the fields for this model automatically and with some guesswork;
     * for more fine grained control models should overload this method.
     *
     * @param string $sTable The database table to query
     *
     * @return array
     */
    public function describeFields($sTable = null)
    {
        $aFields = parent::describeFields($sTable);

        $aFields['old_url']
            ->setType(Form::FIELD_TEXT)
            ->addValidation(FormValidation::RULE_REQUIRED);

        $aFields['new_url']
            ->setType(Form::FIELD_TEXT)
            ->addValidation(FormValidation::RULE_REQUIRED);

        $aFields['type']
            ->addValidation(FormValidation::RULE_REQUIRED);

        foreach ($aFields['type']->options as $k => &$v) {
            $v = sprintf(
                '%s - %s',
                $v,
                HttpCodes::getByCode($v)
            );
        }

        return $aFields;
    }
}
