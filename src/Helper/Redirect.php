<?php

namespace Nails\Redirect\Helper;

use Nails\Common\Helper\ArrayHelper;
use Nails\Config;

/**
 * Class Redirect
 *
 * @package Nails\Redirect\Helper
 */
class Redirect
{
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
