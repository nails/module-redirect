<?php

namespace Nails\Redirect\Service;

use Nails\Common\Exception\Database\ConnectionException;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Common\Service\Input;
use Nails\Common\Service\PDODatabase;
use Nails\Factory;
use Nails\Redirect\Constants;
use Nails\Redirect\Resource;
use PDO;

/**
 * Class Redirect
 *
 * @package Nails\Redirect\Service
 */
class Redirect
{
    /**
     * Determines if a redirect is required
     *
     * @return Resource\Redirect|null
     * @throws ConnectionException
     * @throws FactoryException
     * @throws ModelException
     */
    public function detectRedirect(): ?Resource\Redirect
    {
        $sUrl     = $this->detectUrl();
        $aResults = $this->lookUpRedirects($sUrl);

        if (!empty($aResults)) {

            $aResult = reset($aResults);
            /** @var Resource\Redirect $oRedirect */
            $oRedirect = Factory::resource('Redirect', Constants::MODULE_SLUG, $aResult);

            //  Avoid loops
            return $sUrl !== $oRedirect->new_url
                ? $oRedirect
                : null;
        }

        return null;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the URL to use for redirect look-ups
     *
     * @return string
     * @throws FactoryException
     */
    protected function detectUrl(): string
    {
        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        return '/' . trim($oInput->server('REQUEST_URI'), '/');
    }

    // --------------------------------------------------------------------------

    /**
     * Looks up the redirect table for a matching URL
     *
     * @param string $sUrl The URL to look up
     *
     * @return array
     * @throws ConnectionException
     * @throws FactoryException
     * @throws ModelException
     */
    protected function lookUpRedirects(string $sUrl): array
    {
        /**
         * Using PDO to craft our own query as CI manipulates the string if there's
         * a query string when attempting to protect identifiers.
         */
        /** @var PDODatabase $oPdoDb */
        $oPdoDb = Factory::service('PDODatabase');
        $oModel = Factory::model('Redirect', Constants::MODULE_SLUG);

        $oPdoStatement = $oPdoDb->prepare('SELECT * FROM `' . $oModel->getTableName() . '` WHERE old_url = :old_url');
        $oPdoStatement->execute(['old_url' => $sUrl]);

        return $oPdoStatement->fetchAll(PDO::FETCH_ASSOC);
    }
}
