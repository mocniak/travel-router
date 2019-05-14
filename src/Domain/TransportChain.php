<?php

namespace TravelRouter\Domain;

use TravelRouter\Domain\Exception\BoardingCardCanNotExtendTravelChainException;

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

    /**
     * @param BoardingCard $card
     * @throws BoardingCardCanNotExtendTravelChainException
     */
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
        throw new BoardingCardCanNotExtendTravelChainException();
    }

    public function isExtendableBy(BoardingCard $boardingCard): bool
    {
        if ($this->origin() === $boardingCard->destination()) {
            return true;
        }
        if ($this->destination() === $boardingCard->origin()) {
            return true;
        }
        return false;
    }

    public function origin(): string
    {
        return $this->boardingCards[0]->origin();
    }

    private function destination(): string
    {
        return end($this->boardingCards)->destination();
    }
}
