# Travel Router

Simple boarding cards router. It sorts boardinf cards by their origin and destination to make a step-by-step travel.

Installing
After clone repository and cd into project directory:

$ composer install

## Running the tests

Unit tests:

$ php vendor/bin/phpunit

## Usage

Endpoint specified as a method route() in Router.php is specified by PHP Doc and expects an array of BoardingCard value objects.

Example:
```
$router->route([
    new BoardingCard('Berlin', 'Dusseldorf', 'bus 8N', null, 'Carry on baggage only'),
    new BoardingCard('Dusseldorf', 'Amsterdam', 'flight DE204', '432', 'Baggage drop at ticket counter 344'),
    new BoardingCard('Warsaw', 'Berlin', 'IC301 train, car 12, seat 17', '16b', null),
]);
```
Returns array of the same objects which make a Warsaw –> Berlin -> Dusseldorf -> Amsterdam journey.

When given cards don't make a one-way step-by-step trip Router throws a `BoardingCardsDoesNotMakeASingleTripException`.
