<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Services\WarehouseService;
use App\Repositories\WarehouseRepository;
use App\Repositories\ClothingRepository;
use App\Repositories\SizeRepository;
use App\Repositories\OrderHistoryRepository;
use App\Repositories\OrderDetailsRepository;
use App\Entities\Warehouse;
use App\Entities\Clothing;
use App\Entities\Size;
use App\Entities\OrderHistory;

#[CoversClass(WarehouseService::class)]
class WarehouseServiceTest extends TestCase
{
    private const EXAMPLE_USER_ID = 1;
    private const EXAMPLE_WAREHOUSE_ID = 100;

    private WarehouseService $service;
    private MockObject $warehouseRepo;
    private MockObject $clothingRepo;
    private MockObject $sizeRepo;
    private MockObject $orderHistoryRepo;
    private MockObject $orderDetailsRepo;

    protected function setUp(): void
    {
        $this->warehouseRepo = $this->createMock(WarehouseRepository::class);
        $this->clothingRepo = $this->createMock(ClothingRepository::class);
        $this->sizeRepo = $this->createMock(SizeRepository::class);
        $this->orderHistoryRepo = $this->createMock(OrderHistoryRepository::class);
        $this->orderDetailsRepo = $this->createMock(OrderDetailsRepository::class);

        $this->service = new WarehouseService(
            $this->warehouseRepo,
            $this->clothingRepo,
            $this->sizeRepo,
            $this->orderHistoryRepo,
            $this->orderDetailsRepo
        );
    }

    public function testShouldIncreaseQuantityWhenItemExistsInWarehouse(): void
    {
        $item = new Warehouse(10, 20, 5, 2);
        
        $this->warehouseRepo->method('findByUbranieAndRozmiar')
            ->with(10, 20)
            ->willReturn(['id' => self::EXAMPLE_WAREHOUSE_ID]);

        $this->warehouseRepo->expects($this->once())
            ->method('increaseIlosc')
            ->with(self::EXAMPLE_WAREHOUSE_ID, 5)
            ->willReturn(true);

        $this->warehouseRepo->expects($this->never())->method('insertNew');

        $result = $this->service->addToWarehouse($item);
        $this->assertTrue($result);
    }

    public function testShouldInsertNewItemWhenItDoesNotExistInWarehouse(): void
    {
        $item = new Warehouse(10, 20, 5, 2);

        $this->warehouseRepo->method('findByUbranieAndRozmiar')
            ->with(10, 20)
            ->willReturn(null);

        $this->warehouseRepo->expects($this->once())
            ->method('insertNew')
            ->with($item)
            ->willReturn(true);

        $this->warehouseRepo->expects($this->never())->method('increaseIlosc');

        $result = $this->service->addToWarehouse($item);
        $this->assertTrue($result);
    }

    public function testShouldUpdateWarehouseItemAndCreateHistoryWhenQuantityChanges(): void
    {
        // GIVEN
        $name = 'New Shirt';
        $size = 'L';
        $newQty = 50;
        $minQty = 5;
        $comments = 'Stock Adjustment';
        $oldQty = 40; // Diff +10

        // Mock Clothing & Size lookups
        $this->clothingRepo->method('findByName')->willReturn(null);
        $this->clothingRepo->method('create')->willReturn(10); // ID for 'New Shirt'
        
        $this->sizeRepo->method('findByName')->willReturn(null);
        $this->sizeRepo->method('create')->willReturn(20); // ID for 'L'

        // Mock Warehouse Updates
        $this->warehouseRepo->method('updateUbranieId')->willReturn(true);
        $this->warehouseRepo->method('updateRozmiarId')->willReturn(true);
        
        $this->warehouseRepo->method('getIloscById')
            ->with(self::EXAMPLE_WAREHOUSE_ID)
            ->willReturn($oldQty);
            
        $this->warehouseRepo->method('updateIloscAndMin')->willReturn(true);

        // Expect History Creation because quantity changed
        $this->orderHistoryRepo->expects($this->once())
            ->method('create')
            ->with($this->callback(fn($order) => 
                $order->getStatus() === 2 && // Status 2 = warehouse change
                $order->getUwagi() === $comments
            ))
            ->willReturn(true);
            
        $this->orderHistoryRepo->method('getLastInsertId')->willReturn('999');
        $this->orderDetailsRepo->expects($this->once())
            ->method('create')
            ->willReturn(true);

        // WHEN
        $result = $this->service->updateWarehouseItem(
            self::EXAMPLE_WAREHOUSE_ID, 
            $name, 
            $size, 
            $newQty, 
            $minQty, 
            $comments, 
            self::EXAMPLE_USER_ID
        );

        // THEN
        $this->assertTrue($result['success']);
        // $this->assertSame('warehouse_update_success', $result['message']); // Would need Mock/Translation check
    }

    public function testShouldUpdateWarehouseItemWithoutHistoryWhenQuantityUnchanged(): void
    {
        // GIVEN
        $name = 'Existing Shirt';
        $size = 'M';
        $newQty = 100;
        $minQty = 10;
        $oldQty = 100; // No Change

        $this->clothingRepo->method('findByName')->willReturn(null);
        $this->clothingRepo->method('create')->willReturn(1);
        $this->sizeRepo->method('findByName')->willReturn(null);
        $this->sizeRepo->method('create')->willReturn(2);

        $this->warehouseRepo->method('updateUbranieId')->willReturn(true);
        $this->warehouseRepo->method('updateRozmiarId')->willReturn(true);
        $this->warehouseRepo->method('getIloscById')->willReturn($oldQty);
        $this->warehouseRepo->method('updateIloscAndMin')->willReturn(true);

        // Expect NO history creation
        $this->orderHistoryRepo->expects($this->never())->method('create');

        // WHEN
        $result = $this->service->updateWarehouseItem(
            self::EXAMPLE_WAREHOUSE_ID, 
            $name, 
            $size, 
            $newQty, 
            $minQty, 
            '', 
            self::EXAMPLE_USER_ID
        );

        // THEN
        $this->assertTrue($result['success']);
    }

    public function testShouldReturnFailureWhenUserNotFoundForHistory(): void
    {
        // ... Logic setup (diff +10) ...
        $this->warehouseRepo->method('updateUbranieId')->willReturn(true);
        $this->warehouseRepo->method('updateRozmiarId')->willReturn(true);
        $this->warehouseRepo->method('getIloscById')->willReturn(40); // Old
        $this->warehouseRepo->method('updateIloscAndMin')->willReturn(true); // New is 50

        // Simulate no user ID provided and no session
        // (Service throws Exception 'error_user_not_found')
        
        $result = $this->service->updateWarehouseItem(
            self::EXAMPLE_WAREHOUSE_ID, 
            'N', 
            'S', 
            50, 
            5, 
            '', 
            null // No User ID
        );

        $this->assertFalse($result['success']);
        // The service catches the exception and returns success=false
    }
}
