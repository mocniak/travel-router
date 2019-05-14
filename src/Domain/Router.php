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
        $chain = new TransportChain();
        foreach ($boardingCards as $boardingCard) {
            $chain->extend($boardingCard);
        }
        return $chain;
    }
}
