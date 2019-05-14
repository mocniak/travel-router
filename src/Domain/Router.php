<?php

namespace TravelRouter\Domain;

final class Router
{
    /**
     * @param BoardingCard[] $boardingCards
     * @return TransportChain
     */
    public function route(array $boardingCards)
    {
        /** @var TransportChain[] $chains */
        $chains = [];
        foreach ($boardingCards as $boardingCard) {
            foreach ($chains as $chain) {
                if ($chain->isExtendableBy($boardingCard)) {
                    $chain->extend($boardingCard);
                }
            }
        }

        return $chain;
    }
}
