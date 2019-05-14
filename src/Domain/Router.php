<?php

namespace TravelRouter\Domain;

final class Router
{
    public function __construct()
    {
    }

    /**
     * @param BoardingCard[] $boardingCards
     * @return TransportChain
     */
    public function route(array $boardingCards)
    {
        /** @var TransportChain[] $chains */
        foreach ($boardingCards as $boardingCard) {
        }
        return $chain;
    }
}
