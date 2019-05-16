<?php

namespace TravelRouter\Domain;

use TravelRouter\Domain\Exception\BoardingCardCanNotExtendTravelChainException;
use TravelRouter\Domain\Exception\ChainsCanNotBeMergedException;

final class TransportChain
{
    /**
     * @var $boardingCards BoardingCard[]
     */
    private $boardingCards;

    private function __construct()
    {
        $this->boardingCards = [];
    }

    public static function createWithBoardingCard(BoardingCard $card): self
    {
        $chain = new self();
        $chain->boardingCards[] = $card;

        return $chain;
    }

    public function clone(): self
    {
        $clonedChain = new self();
        $clonedChain->boardingCards = $this->boardingCards;

        return $clonedChain;
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

    public function isMergableWith(TransportChain $otherChain): bool
    {
        if ($this->origin() === $otherChain->destination()) {
            return true;
        }
        if ($this->destination() === $otherChain->origin()) {
            return true;
        }
        return false;
    }

    public function origin(): string
    {
        return $this->boardingCards[0]->origin();
    }

    public function destination(): string
    {
        return end($this->boardingCards)->destination();
    }

    /**
     * @param TransportChain $chain
     * @return TransportChain
     * @throws BoardingCardCanNotExtendTravelChainException
     * @throws ChainsCanNotBeMergedException
     */
    public function merge(TransportChain $chain)
    {
        if ($this->isMergableWith($chain)) {
            if ($this->origin() === $chain->destination()) {
                $mergedChain = $chain->clone();
                foreach ($this->boardingCards as $card) {
                    $mergedChain->extend($card);
                }
                return $mergedChain;
            }
            if ($this->destination() === $chain->origin()) {
                $mergedChain = $this->clone();
                foreach ($chain->boardingCards as $card) {
                    $mergedChain->extend($card);
                }
                return $mergedChain;
            }
        }
        throw new ChainsCanNotBeMergedException();
    }
}
