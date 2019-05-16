<?php

namespace TravelRouter\Tests\Domain;

use PHPUnit\Framework\TestCase;
use TravelRouter\Domain\BoardingCard;
use TravelRouter\Domain\Exception\BoardingCardsDoesNotMakeASingleTripException;
use TravelRouter\Domain\Router;
use TravelRouter\Domain\TransportChain;

class RouterTest extends TestCase
{
    /** @var Router */
    private $router;

    public function setUp(): void
    {
        $this->router = new Router();
    }

    public function testForOneBoardingCardRouterReturnsChainWithOneBoardingCard()
    {
        $boardingCard = new BoardingCard('Warsaw', 'Berlin', 'train', '16b', '');
        $chain = $this->router->route([$boardingCard]);
        $this->assertSame($boardingCard, $chain->boardingCards()[0]);
    }

    public function testForTwoBoardingCardsRouterReturnsChainWhereFirstOneHasDestinationSameAsSecondsOrigin()
    {
        $interChange = 'Berlin';
        $boardingCard1 = new BoardingCard('Warsaw', $interChange, 'train', '16b', '');
        $boardingCard2 = new BoardingCard($interChange, 'Dusseldorf', 'train', '123A', '');
        $chain = $this->router->route([$boardingCard2, $boardingCard1]);
        $this->assertSame($boardingCard1, $chain->boardingCards()[0]);
        $this->assertSame($boardingCard2, $chain->boardingCards()[1]);
    }

    public function testRouterHandlesConnectingTwoSeparateRoutesByThirdOneInBetween()
    {
        $edgeCard1 = new BoardingCard('Warsaw', 'Berlin', 'train', '16b', '');
        $connectingCard = new BoardingCard('Berlin', 'Dusseldorf', 'train', '123A', '');
        $edgeCard2 = new BoardingCard('Dusseldorf', 'Strasbourg', 'train', 'E16A', '');
        $chain = $this->router->route([$edgeCard1, $edgeCard2, $connectingCard]);
        $this->assertSame($edgeCard1, $chain->boardingCards()[0]);
        $this->assertSame($connectingCard, $chain->boardingCards()[1]);
        $this->assertSame($edgeCard2, $chain->boardingCards()[2]);
    }

    public function testRouterHandlesConnectingARouteAroundTheWorld()
    {
        $cards = [
            new BoardingCard('Berlin', 'Dusseldorf', 'bus', null, ''),
            new BoardingCard('Dusseldorf', 'Amsterdam', 'train', '432', ''),
            new BoardingCard('Warsaw', 'Berlin', 'train', '16b', ''),
            new BoardingCard('Amsterdam', 'Brussels', 'train', '123A', ''),
            new BoardingCard('Brussels', 'Paris', 'train', '17F', ''),
            new BoardingCard('London', 'New York', 'plane', '13A', 'Cabin baggage only'),
            new BoardingCard('Chicago', 'Los Angeles', 'rental car', '123A', ''),
            new BoardingCard('Paris', 'London', 'train', '123A', 'Crossing international border'),
            new BoardingCard('Los Angeles', 'Honolulu', 'plane', '123A', ''),
            new BoardingCard('New York', 'Chicago', 'bus', '123A', ''),
            new BoardingCard('Honolulu', 'Tokio', 'ship', '123A', 'Crossing international border'),
            new BoardingCard('Tokio', 'Osaka', 'train', '123A', ''),
            new BoardingCard('Osaka', 'Pyongyang', 'plane', '123A', 'Crossing international border'),
            new BoardingCard('Pyongyang', 'Beijing', 'train', '123A', 'Crossing international border'),
            new BoardingCard('Beijing', 'Moscow', 'plane', '123A', 'Crossing international border'),
            new BoardingCard('Moscow', 'Minsk', 'train', '123A', 'Crossing international border'),
            new BoardingCard('Minsk', 'Kiev', 'train', '123A', 'Crossing international border'),
        ];

        shuffle($cards);

        $chain = $this->router->route($cards);

        $this->assertEquals('Warsaw', $chain->origin());
        $this->assertEquals('Kiev', $chain->destination());
    }

    public function testWhenRoutesDoesNotMakeASingleTripRouterThrowsAnException()
    {
        $boardingCard1 = new BoardingCard('Warsaw', 'Berlin', 'train', '16b', '');
        $boardingCard2 = new BoardingCard('Bonn', 'Dusseldorf', 'train', '123A', '');

        $this->expectException(BoardingCardsDoesNotMakeASingleTripException::class);

        $this->router->route([$boardingCard2, $boardingCard1]);
    }

    public function testMergeChainsMergesTwoChainsWhenTheyCanConnect()
    {
        $chain = TransportChain::createWithBoardingCard(new BoardingCard('Hanover', 'Bonn', 'train', '123A', ''));
        $chains = [
            TransportChain::createWithBoardingCard(new BoardingCard('Berlin', 'Hanover', 'train', '22F', '')),
        ];

        $newChains = $this->router->mergeChains($chains, $chain);
        $mergedChain = $newChains[0];

        $this->assertEquals(1, count($newChains));
        $this->assertEquals('Berlin', $mergedChain->origin());
        $this->assertEquals('Bonn', $mergedChain->destination());
    }
}
