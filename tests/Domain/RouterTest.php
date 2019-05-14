<?php

namespace TravelRouter\Tests\Domain;

use PHPUnit\Framework\TestCase;
use TravelRouter\Domain\BoardingCard;
use TravelRouter\Domain\Exception\BoardingCardsDoesNotMakeASingleTripException;
use TravelRouter\Domain\Router;

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

    public function testWhenRoutesDoesNotMakeASingleTripRouterThrowsAnException()
    {
        $boardingCard1 = new BoardingCard('Warsaw', 'Berlin', 'train', '16b', '');
        $boardingCard2 = new BoardingCard('Bonn', 'Dusseldorf', 'train', '123A', '');

        $this->expectException(BoardingCardsDoesNotMakeASingleTripException::class);

        $this->router->route([$boardingCard2, $boardingCard1]);
    }
}
