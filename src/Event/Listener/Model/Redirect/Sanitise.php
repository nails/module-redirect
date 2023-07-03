<?php

namespace Nails\Redirect\Event\Listener\Model\Redirect;

use Nails\Common\Events\Subscription;
use Nails\Common\Helper\Model\Where;
use Nails\Redirect\Exception\RedirectException\LoopDetectedException;
use Nails\Redirect\Model\Redirect;
use Nails\Redirect\Helper;

/**
 * Class Sanitise
 *
 * @package Nails\Redirect\Event\Listener\Model\Redirect
 */
class Sanitise extends Subscription
{
    /**
     * Sanitise constructor.
     *
     * @throws \ReflectionException
     */
    public function __construct()
    {
        $this
            ->setEvent([
                Redirect::EVENT_CREATING,
                Redirect::EVENT_UPDATING,
            ])
            ->setNamespace(Redirect::getEventNamespace())
            ->setCallback([$this, 'execute']);
    }

    // --------------------------------------------------------------------------

    /**
     * Fired when the events are triggered
     *
     * @param array    $aData  The data used for creating/editing
     * @param Redirect $oModel The Redirect model
     *
     * @throws \Exception
     */
    public function execute(array &$aData, Redirect $oModel, int $iId = null): void
    {
        $this
            ->normaliseUrls($aData)
            ->deleteObsolete($aData, $oModel, $iId)
            ->detectLoops($aData, $oModel);
    }

    // --------------------------------------------------------------------------

    /**
     * Normalises URLs
     *
     * @param array $aData The data used for creating/editing
     *
     * @return $this
     * @throws \Exception
     */
    private function normaliseUrls(array &$aData): self
    {
        if (array_key_exists('old_url', $aData)) {
            $aData['old_url'] = Helper\Redirect::normaliseUrl(trim($aData['old_url']));
        }
        if (array_key_exists('new_url', $aData)) {
            $aData['new_url'] = Helper\Redirect::normaliseUrl(trim($aData['new_url']));
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Delete multiple instances of the same old URL (so there's a canonical redirect)
     *
     * @param array    $aData  The data used for creating/editing
     * @param Redirect $oModel The Redirect model
     *
     * @return $this
     */
    private function deleteObsolete(array $aData, Redirect $oModel, int $iId = null): self
    {
        if (array_key_exists('old_url', $aData)) {
            $oModel->deleteWhere(array_filter([
                $iId ? ['id !=', $iId] : null,
                ['old_url', $aData['old_url']],
            ]));
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Detect redirect loops
     *
     * @param array    $aData  The data used for creating/editing
     * @param Redirect $oModel The Redirect model
     *
     * @return $this
     */
    private function detectLoops(array $aData, Redirect $oModel): self
    {
        $aTrack = [];
        $sUrl   = $aData['new_url'] ?? null;

        do {

            /** @var \Nails\Redirect\Resource\Redirect|null $oTarget */
            $oTarget = $oModel->getFirst([
                new Where('old_url', $sUrl),
            ]);

            if (!empty($oTarget) && in_array($oTarget->id, $aTrack)) {
                throw new LoopDetectedException('Redirect loop detected');

            } elseif ($oTarget) {
                $sUrl     = $oTarget->new_url;
                $aTrack[] = $oTarget->id;
            }

        } while ($oTarget);

        return $this;
    }
}
