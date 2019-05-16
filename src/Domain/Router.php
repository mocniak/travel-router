<?php

namespace TravelRouter\Domain;

use TravelRouter\Domain\Exception\BoardingCardCanNotExtendTravelChainException;
use TravelRouter\Domain\Exception\BoardingCardsDoesNotMakeASingleTripException;
use TravelRouter\Domain\Exception\ChainsCanNotBeMergedException;

final class Router
{
    /**
     * @param BoardingCard[] $boardingCards
     * @return BoardingCard[]
     * @throws BoardingCardsDoesNotMakeASingleTripException
     */
    public function route(array $boardingCards): array
    {
        /** @var TransportChain[] $chains */
        $chains = [];
        try {
            foreach ($boardingCards as $boardingCard) {
                if (empty($chains)) {
                    $chains[] = TransportChain::createWithBoardingCard($boardingCard);
                } else {
                    $chainWasAppended = false;
                    foreach ($chains as $chain) {
                        if (!$chain->isExtendableBy($boardingCard)) {
                            continue;
                        }
                        $chain->extend($boardingCard);
                        $chains = $this->mergeChains($chains, $chain);
                        $chainWasAppended = true;
                        break;
                    }
                    if (!$chainWasAppended) {
                        $chains[] = TransportChain::createWithBoardingCard($boardingCard);
                    }
                }
            }
        } catch (ChainsCanNotBeMergedException | BoardingCardCanNotExtendTravelChainException $e) {
            throw new BoardingCardsDoesNotMakeASingleTripException();
        }

        if (count($chains) !== 1) throw new BoardingCardsDoesNotMakeASingleTripException();

        return $chains[0]->boardingCards();
    }

    /**
     * @param TransportChain[] $chains
     * @param TransportChain $chainToMerge
     * @return TransportChain[]
     * @throws Exception\ChainsCanNotBeMergedException
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
