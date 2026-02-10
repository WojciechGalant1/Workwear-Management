<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Services\OrderService;
use App\Services\WarehouseService;
use App\Repositories\OrderHistoryRepository;
use App\Repositories\OrderDetailsRepository;
use App\Repositories\ClothingRepository;
use App\Repositories\SizeRepository;
use App\Repositories\CodeRepository;
use App\Repositories\WarehouseRepository;
use App\Repositories\UserRepository;
use App\Entities\OrderHistory;
use App\Entities\OrderDetails;
use App\Entities\Clothing;
use App\Entities\Size;
use App\Entities\Code;
use App\Entities\Warehouse;

#[CoversClass(OrderService::class)]
class OrderServiceTest extends TestCase
{
    private const EXAMPLE_USER_ID = 1;
    private const EXAMPLE_ORDER_ID = 123;
    
    private OrderService $service;
    private MockObject $orderHistoryRepo;
    private MockObject $orderDetailsRepo;
    private MockObject $clothingRepo;
    private MockObject $sizeRepo;
    private MockObject $codeRepo;
    private MockObject $warehouseRepo;
    private MockObject $warehouseService;
    private MockObject $userRepo;

    protected function setUp(): void
    {
        $this->orderHistoryRepo = $this->createMock(OrderHistoryRepository::class);
        $this->orderDetailsRepo = $this->createMock(OrderDetailsRepository::class);
        $this->clothingRepo = $this->createMock(ClothingRepository::class);
        $this->sizeRepo = $this->createMock(SizeRepository::class);
        $this->codeRepo = $this->createMock(CodeRepository::class);
        $this->warehouseRepo = $this->createMock(WarehouseRepository::class);
        $this->warehouseService = $this->createMock(WarehouseService::class);
        $this->userRepo = $this->createMock(UserRepository::class);

        $this->service = new OrderService(
            $this->orderHistoryRepo,
            $this->orderDetailsRepo,
            $this->clothingRepo,
            $this->sizeRepo,
            $this->codeRepo,
            $this->warehouseRepo,
            $this->warehouseService,
            $this->userRepo
        );
    }

    public function testShouldCreateOrderSuccessfullyAndAddToWarehouse(): void
    {
        // GIVEN
        $items = [
            [
                'nazwa' => 'T-Shirt',
                'rozmiar' => 'M',
                'firma' => 'Nike',
                'ilosc' => 5,
                'iloscMin' => 2,
                'kod' => 'CODE-123'
            ],
            [
                'nazwa' => 'Jeans',
                'rozmiar' => '32',
                'firma' => 'Levis',
                'ilosc' => 2,
                'iloscMin' => 1,
                'kod' => 'CODE-456'
            ]
        ];
        $comments = 'Test Order';
        $status = 1; // Active

        // Mock User Validation
        $this->userRepo->method('getUserById')
            ->with(self::EXAMPLE_USER_ID)
            ->willReturn(['id' => self::EXAMPLE_USER_ID]);

        // Mock Order Creation
        $this->orderHistoryRepo->expects($this->once())
            ->method('create')
            ->with($this->callback(fn($order) => 
                $order instanceof OrderHistory && 
                $order->getUserId() === self::EXAMPLE_USER_ID &&
                $order->getUwagi() === $comments &&
                $order->getStatus() === $status
            ))
            ->willReturn(true);

        $this->orderHistoryRepo->method('getLastInsertId')
            ->willReturn((string)self::EXAMPLE_ORDER_ID);

        // Mock Item Logic (simplified for brevity, ensuring flow works)
        $this->clothingRepo->method('firstOrCreate')->willReturn(10);
        $this->sizeRepo->method('firstOrCreate')->willReturn(20);
        
        // Mock Code logic: first item exists, second creates new
        $existingCode = $this->createMock(Code::class);
        $existingCode->method('getIdKod')->willReturn(99);

        $this->codeRepo->method('findKodByNazwa')
            ->willReturnMap([
                ['CODE-123', $existingCode],
                ['CODE-456', false]
            ]);
            
        $this->codeRepo->method('create')->willReturn(100);

        // Mock Details Creation - Expect 2 items
        $this->orderDetailsRepo->expects($this->exactly(2))
            ->method('create')
            ->willReturn(true);

        // Mock Warehouse Add - Expect 2 items added
        // Since we call createOrderDetails -> addOrderToWarehouse (which calls getByZamowienieId)
        // We need orderDetailsRepo to return the items we just "saved"
        $this->orderDetailsRepo->method('getByZamowienieId')
            ->with(self::EXAMPLE_ORDER_ID)
            ->willReturn([
                ['id_ubrania' => 10, 'id_rozmiaru' => 20, 'ilosc' => 5, 'iloscMin' => 2],
                ['id_ubrania' => 10, 'id_rozmiaru' => 20, 'ilosc' => 2, 'iloscMin' => 1]
            ]);

        $this->warehouseService->expects($this->exactly(2))
            ->method('addToWarehouse')
            ->willReturn(true);

        // WHEN
        $result = $this->service->createOrder(self::EXAMPLE_USER_ID, $items, $comments, $status);

        // THEN
        $this->assertSame(self::EXAMPLE_ORDER_ID, $result);
    }

    public function testShouldThrowExceptionWhenNoItemsProvided(): void
    {
        $this->expectException(\Exception::class);
        $this->service->createOrder(self::EXAMPLE_USER_ID, []);
    }

    public function testShouldThrowExceptionWhenUserNotFound(): void
    {
        $this->userRepo->method('getUserById')->willReturn(false);
        
        $this->expectException(\Exception::class);
        // Provide valid items to bypass items check
        $this->service->createOrder(self::EXAMPLE_USER_ID, [['nazwa' => 'T', 'rozmiar' => 'M', 'firma' => 'F', 'ilosc' => 1]]);
    }

    #[DataProvider('invalidItemsDataProvider')]
    public function testShouldThrowExceptionWhenItemValidationFails(array $items): void
    {
        $this->userRepo->method('getUserById')->willReturn(['id' => self::EXAMPLE_USER_ID]);
        
        $this->expectException(\Exception::class);
        $this->service->createOrder(self::EXAMPLE_USER_ID, $items);
    }

    public static function invalidItemsDataProvider(): array
    {
        return [
            'missing name' => [[['rozmiar' => 'M', 'firma' => 'F', 'ilosc' => 1]]],
            'missing size' => [[['nazwa' => 'T', 'firma' => 'F', 'ilosc' => 1]]],
            'missing firm' => [[['nazwa' => 'T', 'rozmiar' => 'M', 'ilosc' => 1]]],
            'zero quantity' => [[['nazwa' => 'T', 'rozmiar' => 'M', 'firma' => 'F', 'ilosc' => 0]]],
            'negative quantity' => [[['nazwa' => 'T', 'rozmiar' => 'M', 'firma' => 'F', 'ilosc' => -5]]],
        ];
    }

    public function testShouldNotAddToWarehouseWhenStatusIsDraft(): void
    {
        // GIVEN
        $items = [['nazwa' => 'T', 'rozmiar' => 'M', 'firma' => 'F', 'ilosc' => 1]];
        $status = 2; // e.g., Draft/Internal transfer

        $this->userRepo->method('getUserById')->willReturn(['id' => self::EXAMPLE_USER_ID]);
        $this->orderHistoryRepo->method('create')->willReturn(true);
        $this->orderHistoryRepo->method('getLastInsertId')->willReturn((string)self::EXAMPLE_ORDER_ID);
        
        $this->clothingRepo->method('firstOrCreate')->willReturn(1);
        $this->sizeRepo->method('firstOrCreate')->willReturn(1);
        $mockCode = $this->createMock(Code::class);
        $mockCode->method('getIdKod')->willReturn(99);
        $this->codeRepo->method('findKodByNazwa')->willReturn($mockCode);
        $this->orderDetailsRepo->method('create')->willReturn(true);

        // WHEN
        $this->warehouseService->expects($this->never())->method('addToWarehouse');
        
        $this->service->createOrder(self::EXAMPLE_USER_ID, $items, '', $status);
    }

    public function testShouldThrowExceptionWhenOrderCreationFails(): void
    {
        $this->userRepo->method('getUserById')->willReturn(['id' => self::EXAMPLE_USER_ID]);
        $this->orderHistoryRepo->method('create')->willReturn(false);

        $this->expectException(\Exception::class);
        $this->service->createOrder(self::EXAMPLE_USER_ID, [['nazwa' => 'T', 'rozmiar' => 'M', 'firma' => 'F', 'ilosc' => 1]]);
    }
}
