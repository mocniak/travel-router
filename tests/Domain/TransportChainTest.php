<?php

namespace TravelRouter\Tests\Domain;

use PHPUnit\Framework\TestCase;
use TravelRouter\Domain\BoardingCard;
use TravelRouter\Domain\Exception\BoardingCardCanNotExtendTravelChainException;
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

    public function testChainThrowsExceptionWhenIsExtendendenWithTicketThatNotFits()
    {
        $chain = new TransportChain();
        $cardInEurope = new BoardingCard('Warsaw', 'Berlin', 'train', '16b', '');
        $cardInAsia = new BoardingCard('Doha', 'Dubai', 'plane', '17F', 'cabin luggage only');

        $chain->extend($cardInAsia);

        $this->expectException(BoardingCardCanNotExtendTravelChainException::class);
        $chain->extend($cardInEurope);
    }

    public function testChainSaysIfIsExtendableByBoardingCardInTheBeginningAndAtTheEnd()
    {
        $chain = new TransportChain();
        $frontBoardingCard = new BoardingCard('Warsaw', 'Berlin', 'train', '16b', '');
        $middleBoardingCard = new BoardingCard('Berlin', 'Amsterdam', 'train', 'A443', '');
        $backBoardingCard = new BoardingCard('Amsterdam', 'London', 'train', '556', 'crossing international border');

        $chain->extend($middleBoardingCard);

        $this->assertTrue($chain->isExtendableBy($frontBoardingCard));
        $this->assertTrue($chain->isExtendableBy($backBoardingCard));
    }

    public function testChainSaysIfItIsNotExtendableByCardFromSomewhereElse()
    {
        $chain = new TransportChain();
        $asianBoardingCard = new BoardingCard('Beijing', 'Seoul', 'train', '16b', '');
        $europeanBorgindCard = new BoardingCard('Berlin', 'Amsterdam', 'train', 'A443', '');
        $americanBoardingCard = new BoardingCard('New York', 'Mexico City', 'train', '556', 'crossing international border');

        $chain->extend($europeanBorgindCard);

        $this->assertFalse($chain->isExtendableBy($asianBoardingCard));
        $this->assertFalse($chain->isExtendableBy($americanBoardingCard));
    }
}
