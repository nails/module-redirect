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
use Nails\Common\Model\Base;
use Nails\Common\Service\HttpCodes;
use Nails\Config;

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

        $aFields['old_url']->validation[] = 'required';
        $aFields['new_url']->validation[] = 'required';
        $aFields['type']->validation[]    = 'required';

        foreach ($aFields['type']->options as $k => &$v) {
            $v = sprintf(
                '%s - %s',
                $v,
                HttpCodes::getByCode($v)
            );
        }

        return $aFields;
    }

    // --------------------------------------------------------------------------

    /**
     * Creates a new object; overriding to normalise URLs
     *
     * @param array   $aData         The data to create the object with
     * @param boolean $bReturnObject Whether to return just the new ID or the full object
     *
     * @return mixed
     * @throws ModelException
     */
    public function create(array $aData = [], $bReturnObject = false)
    {
        $this->normaliseUrls($aData);
        return parent::create($aData, $bReturnObject);
    }

    // --------------------------------------------------------------------------

    /**
     * Updates an existing object; overriding to normalise URLs
     *
     * @param integer|array $mIds  The ID (or array of IDs) of the object(s) to update
     * @param array         $aData The data to update the object(s) with
     *
     * @return boolean
     * @throws ModelException
     */
    public function update($mIds, array $aData = []): bool
    {
        $this->normaliseUrls($aData);
        return parent::update($mIds, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Normalises the old_url and new_url keys in the $aData array
     *
     * @param array $aData The $aData array
     *
     * @throws \Exception
     */
    protected function normaliseUrls(array &$aData)
    {
        if (array_key_exists('old_url', $aData)) {
            $aData['old_url'] = static::normaliseUrl(trim($aData['old_url']));
        }
        if (array_key_exists('new_url', $aData)) {
            $aData['new_url'] = static::normaliseUrl(trim($aData['new_url']));
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Normalises a URL to just its path and query components
     *
     * @param string $sUrl The URL to normalise
     *
     * @return string
     * @throws \Exception
     */
    public static function normaliseUrl($sUrl)
    {
        $aUrl = parse_url($sUrl);
        if (!is_array($aUrl)) {
            throw new NailsException('Failed to parse URL (' . $sUrl . ')');
        }

        $sScheme = ArrayHelper::get('scheme', $aUrl, 'http');
        $sHost   = ArrayHelper::get('host', $aUrl);
        $sPath   = ArrayHelper::get('path', $aUrl, '/');
        $sQuery  = ArrayHelper::get('query', $aUrl);

        $aBaseUrl    = parse_url(Config::get('BASE_URL'));
        $sBaseScheme = ArrayHelper::get('scheme', $aBaseUrl, 'http');
        $sBaseHost   = ArrayHelper::get('host', $aBaseUrl, Config::get('BASE_URL'));

        if ($sBaseScheme === $sScheme && $sBaseHost === $sHost) {
            $sDomain = '';
        } elseif ($sHost) {
            $sDomain = $sScheme . '://' . $sHost;
        } else {
            $sDomain = '';
        }

        $sUrl = $sDomain . implode(
                '?',
                array_filter([
                    ArrayHelper::get('path', $aUrl),
                    ArrayHelper::get('query', $aUrl),
                ])
            );

        return rtrim($sUrl, '/');
    }
}
