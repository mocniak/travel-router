<?php

namespace TravelRouter\Domain;

use TravelRouter\Domain\Exception\BoardingCardsDoesNotMakeASingleTripException;

final class Router
{
    public function __construct()
    {
    }

    /**
     * @param BoardingCard[] $boardingCards
     * @return TransportChain
     * @throws Exception\BoardingCardCanNotExtendTravelChainException
     * @throws BoardingCardsDoesNotMakeASingleTripException
     */
    public function route(array $boardingCards)
    {
        /** @var TransportChain[] $chains */
        $chains = [];
        foreach ($boardingCards as $boardingCard) {
            if (empty($chains)) {
                $chains[] = TransportChain::createWithBoardingCard($boardingCard);
            } else {
                foreach ($chains as $chain) {
                    if ($chain->isExtendableBy($boardingCard)) {
                        $chain->extend($boardingCard);
                        $chains = $this->mergeChains($chains, $chain);
                        continue;
                    } else {
                        $chains[] = TransportChain::createWithBoardingCard($boardingCard);
                    }
                }
            }
        }
        if (count($chains) > 1) throw new BoardingCardsDoesNotMakeASingleTripException();
        return $chains[0];
    }

    /**
     * @param TransportChain[] $chains
     * @param TransportChain $chainToMerge
     * @return TransportChain[]
     */
    public function mergeChains(array $chains, TransportChain $chainToMerge): array
    {
        /** @var TransportChain $existingChain */
        foreach ($chains as $existingChain) {
            if ($existingChain->isMergableWith($chainToMerge)) {
                $mergedChain = $existingChain->merge($chainToMerge);
                $newArray = array_filter($chains, function (TransportChain $chain) use ($existingChain) {
                    return ($existingChain->origin() !== $chain->origin());
                });
                $newArray2 = array_filter($newArray, function (TransportChain $chain) use ($chainToMerge) {
                    return ($chainToMerge->origin() !== $chain->origin());
                });
                $newArray2[] = $mergedChain;
                return $newArray2;
            }
        }
        return $chains;
    }
}
