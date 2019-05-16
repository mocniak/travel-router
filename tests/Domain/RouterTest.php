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
