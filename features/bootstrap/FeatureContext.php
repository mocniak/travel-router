<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use Stockman\Domain\Product;
use Stockman\Domain\NewOrder;
use Stockman\Domain\Packer;
use Stockman\Domain\Stockman;
use Stockman\Infrastructure\CourierStub;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    private $warehouseRepository;
    private $stockman;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->warehouseRepository = new \Stockman\Infrastructure\MemoryWarehouseRepository();
        $this->stockman = new Stockman($this->warehouseRepository, new CourierStub(), new Packer());
    }

    /**
     * @Given there is a warehouse :warehouseName
     */
    public function thereIsAWarehouse($warehouseName)
    {
        $this->warehouseRepository->add(new \Stockman\Domain\Warehouse($warehouseName));
    }

    /**
     * @Given in warehouse :warehouseName there are :quantity products :productName
     */
    public function inWarehouseThereAreProducts($warehouseName, $productName, int $quantity)
    {
        $warehouse = $this->warehouseRepository->getByName($warehouseName);
        $warehouse->addToStock($productName, $quantity);
    }

    /**
     * @When I order :arg2 products :productName with delivery type :deliveryType
     */
    public function iOrderProducts($productName, int $quantity, $deliveryType)
    {
        $this->stockman->processNewOrder(new NewOrder($deliveryType, ...[new Product($productName, $quantity)]));
    }

    /**
     * @Given I order products with delivery type :deliveryType
     */
    public function iOrderProducts1(TableNode $table, $deliveryType)
    {
        $items = [];
        foreach ($table as $row) {
            $items[] = new Product($row['productName'], $row['quantity']);
        }
        $this->stockman->processNewOrder(new NewOrder($deliveryType, ...$items));
    }

    /**
     * @Then warehouse :warehouseName should receive freight bill with delivery type :deliveryType and products:
     */
    public function warehouseShouldReceiveFreightBillWith($warehouseName, $deliveryType, TableNode $table)
    {
        $warehouse = $this->warehouseRepository->getByName($warehouseName);
        $freightBill = $warehouse->getLatestFreightBill();

        Assert::assertEquals($deliveryType, $freightBill->deliveryMethod());

        foreach ($table as $row) {
            $thereIsGivenProduct = false;
            foreach ($freightBill->products() as $product) {
                if ($product->name() === $row['productName']) {
                    Assert::assertSame(
                        (int)$row['quantity'],
                        $product->quantity()
                    );
                    $thereIsGivenProduct = true;
                }
            }
            Assert::assertTrue($thereIsGivenProduct);
        }
    }
}
