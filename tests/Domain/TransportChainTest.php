<?php

namespace TravelRouter\Tests\Domain;

use PHPUnit\Framework\TestCase;
use TravelRouter\Domain\BoardingCard;
use TravelRouter\Domain\TransportChain;

class TransportChainTest extends TestCase
{
    public function testNewChainCanBeEmpty()
    {
        $chain = new TransportChain();
        $this->assertEmpty($chain->boardingCards());
    }

    public function testCardsExtendsChains()
    {
        $chain = new TransportChain();
        $boardingCard = new BoardingCard('Warsaw', 'Berlin', 'train', '16b', '');
        $chain->extend($boardingCard);
        $this->assertSame($boardingCard, $chain->boardingCards()[0]);
    }

    public function testChainIsExtendedOnTheLastPositionWhenTheLastDestinationIsNewCardOrigin()
    {
        $chain = new TransportChain();
        $interChange = 'Berlin';
        $cardToInterchange = new BoardingCard('Warsaw', $interChange, 'train', '16b', '');
        $cardFromInterchange = new BoardingCard($interChange, 'Dusseldorf', 'train', '123A', '');

        $chain->extend($cardToInterchange);
        $chain->extend($cardFromInterchange);

        $this->assertSame($cardToInterchange, $chain->boardingCards()[0]);
        $this->assertSame($cardFromInterchange, $chain->boardingCards()[1]);
    }

    public function testChainIsExtendedOnTheBeginningWhenOriginIsNewCardDestination()
    {
        $chain = new TransportChain();
        $interChange = 'Berlin';
        $cardToInterchange = new BoardingCard('Warsaw', $interChange, 'train', '16b', '');
        $cardFromInterchange = new BoardingCard($interChange, 'Dusseldorf', 'train', '123A', '');

        $chain->extend($cardFromInterchange);
        $chain->extend($cardToInterchange);

        $this->assertSame($cardToInterchange, $chain->boardingCards()[0]);
        $this->assertSame($cardFromInterchange, $chain->boardingCards()[1]);
    }
}
