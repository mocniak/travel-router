<?php

namespace TravelRouter\Domain;

final class TransportChain
{
    /**
     * @var $boardingCards BoardingCard[]
     */
    private $boardingCards;

    public function __construct()
    {
        $this->boardingCards = [];
    }

    /**
     * @return BoardingCard[]
     */
    public function boardingCards(): array
    {
        return $this->boardingCards;
    }

    public function extend(BoardingCard $card)
    {
        if (empty($this->boardingCards)) {
            $this->boardingCards[] = $card;
            return;
        } else if (end($this->boardingCards)->destination() == $card->origin()) {
            $this->boardingCards[] = $card;
            return;
        } else if ($this->boardingCards[0]->origin() == $card->destination()) {
            array_unshift($this->boardingCards, $card);
            return;
        }
    }
}
