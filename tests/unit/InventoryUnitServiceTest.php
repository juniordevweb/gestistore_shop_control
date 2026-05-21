<?php

namespace Tests\Support;

use App\Libraries\InventoryUnitService;
use CodeIgniter\Test\CIUnitTestCase;

class InventoryUnitServiceTest extends CIUnitTestCase
{
    public function testKgQuantityAndPriceAreConvertedToBaseUnit(): void
    {
        $service = new InventoryUnitService();

        $this->assertSame('50000', $service->toBaseQuantity('50', 'kg'));
        $this->assertSame('0.6', $service->toBaseUnitPrice('600', 'kg'));
        $this->assertSame('0.8', $service->toBaseUnitPrice('800', 'kg'));
    }

    public function testSaleProfitUsesBaseUnitCost(): void
    {
        $service = new InventoryUnitService();

        $cost = $service->multiply('300', '0.6');
        $profit = $service->subtract('250', $cost);

        $this->assertSame('180', $cost);
        $this->assertSame('70', $profit);
    }
}
