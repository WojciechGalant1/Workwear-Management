<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Services\IssueService;
use App\Repositories\IssueRepository;
use App\Repositories\IssuedClothingRepository;
use App\Repositories\WarehouseRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\UserRepository;
use App\Core\ServiceContainer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(IssueService::class)]
class IssueServiceTest extends TestCase
{
    private const EXAMPLE_ISSUE_ID = 42;
    private const EXAMPLE_PRACOWNIK_ID = 5;
    private const EXAMPLE_USER_ID = 1;

    private IssueService $service;
    private MockObject $issueRepo;
    private MockObject $issuedClothingRepo;
    private MockObject $warehouseRepo;
    private MockObject $employeeRepo;
    private MockObject $userRepo;

    protected function setUp(): void
    {
        $this->issueRepo = $this->createMock(IssueRepository::class);
        $this->issuedClothingRepo = $this->createMock(IssuedClothingRepository::class);
        $this->warehouseRepo = $this->createMock(WarehouseRepository::class);
        $this->employeeRepo = $this->createMock(EmployeeRepository::class);
        $this->userRepo = $this->createMock(UserRepository::class);

        $this->service = new IssueService(
            $this->employeeRepo,
            $this->userRepo,
            $this->warehouseRepo,
            $this->issueRepo,
            $this->issuedClothingRepo
        );
    }

    public function testShouldCreateIssueAndReturnIdWhenAllDataIsValidAndStockIsAvailable(): void
    {
        // GIVEN
        $ubrania = [
            ['id_ubrania' => 1, 'id_rozmiar' => 3, 'ilosc' => 2, 'data_waznosci' => 12],
            ['id_ubrania' => 2, 'id_rozmiar' => 5, 'ilosc' => 1, 'data_waznosci' => 6],
        ];
        $uwagi = 'Pilne wydanie';

        $this->employeeRepo->method('getById')
            ->with(self::EXAMPLE_PRACOWNIK_ID)
            ->willReturn(['id_pracownik' => self::EXAMPLE_PRACOWNIK_ID]);

        $this->userRepo->method('getUserById')
            ->with(self::EXAMPLE_USER_ID)
            ->willReturn(['id' => self::EXAMPLE_USER_ID]);

        $this->warehouseRepo->method('getIlosc')
            ->willReturnMap([
                [1, 3, 10],
                [2, 5, 10]
            ]);

        // Verify Issue creation including COMMENTS
        $this->issueRepo->expects($this->once())
            ->method('create')
            ->with($this->callback(fn($issue) => 
                $issue instanceof \App\Entities\Issue && 
                $issue->getUwagi() === $uwagi &&
                $issue->getIdPracownik() === self::EXAMPLE_PRACOWNIK_ID
            ))
            ->willReturn((string)self::EXAMPLE_ISSUE_ID);

        // Verify Expiry Date Calculation for first item
        $expectedExpiryDate = (new \DateTime())->modify('+12 months')->format('Y-m-d');
        
        $this->issuedClothingRepo->expects($this->exactly(2))
            ->method('create')
            ->with($this->callback(function($item) use ($expectedExpiryDate) {
                // We only check the first item's expiry as an example of date logic verification
                if ($item->getIdUbrania() === 1) {
                    return $item->getDataWaznosci()->format('Y-m-d') === $expectedExpiryDate;
                }
                return true;
            }))
            ->willReturn(true);
        
        $callIndex = 0;
        $this->warehouseRepo->expects($this->exactly(2))
            ->method('updateIlosc')
            ->willReturnCallback(function ($id, $size, $qty) use (&$callIndex) {
                $expected = [
                    [1, 3, 2],
                    [2, 5, 1]
                ];
                $this->assertEquals($expected[$callIndex][0], $id);
                $this->assertEquals($expected[$callIndex][1], $size);
                $this->assertEquals($expected[$callIndex][2], $qty);
                $callIndex++;
                return true;
            });

        // WHEN
        $result = $this->service->issueClothing(
            self::EXAMPLE_PRACOWNIK_ID, 
            self::EXAMPLE_USER_ID, 
            $ubrania, 
            $uwagi
        );

        // THEN
        $this->assertSame(self::EXAMPLE_ISSUE_ID, $result, 'Zwrócone ID wydania powinno być liczbą całkowitą zgodną z bazą');
    }

    public function testShouldThrowExceptionWhenStockIsLowerThanRequested(): void
    {
        $ubrania = [['id_ubrania' => 1, 'id_rozmiar' => 3, 'ilosc' => 5]];

        $this->employeeRepo->method('getById')->willReturn(['id_pracownik' => self::EXAMPLE_PRACOWNIK_ID]);
        $this->userRepo->method('getUserById')->willReturn(['id' => self::EXAMPLE_USER_ID]);

        // Point 8: fail-fast callback instead of Map if we want to be strict
        $this->warehouseRepo->method('getIlosc')->willReturnCallback(function($id, $size) {
            if ($id === 1 && $size === 3) return 2;
            throw new \LogicException("Unexpected warehouse check for ID $id, Size $size");
        });

        $this->expectException(\Exception::class);
        $this->service->issueClothing(self::EXAMPLE_PRACOWNIK_ID, self::EXAMPLE_USER_ID, $ubrania);
    }

    #[DataProvider('invalidClothingDataProvider')]
    public function testShouldThrowExceptionWhenClothingDataFailsValidation(array $ubrania): void
    {
        $this->employeeRepo->method('getById')->willReturn(['id_pracownik' => self::EXAMPLE_PRACOWNIK_ID]);
        $this->userRepo->method('getUserById')->willReturn(['id' => self::EXAMPLE_USER_ID]);

        $this->expectException(\Exception::class);
        $this->service->issueClothing(self::EXAMPLE_PRACOWNIK_ID, self::EXAMPLE_USER_ID, $ubrania);
    }

    public static function invalidClothingDataProvider(): array
    {
        return [
            'empty list' => [[]],
            'incorrect schema' => [[['something' => 1]]],
            'quantity zero' => [[['id_ubrania' => 1, 'id_rozmiar' => 1, 'ilosc' => 0]]],
            'id zero' => [[['id_ubrania' => 0, 'id_rozmiar' => 1, 'ilosc' => 1]]],
        ];
    }

    // --- CANCEL ISSUE TESTS ---

    public function testShouldSuccessfullyCancelIssueAndReturnToWarehouse(): void
    {
        $issuedItemId = 100;
        $issuedData = [
            'id_ubrania' => 10,
            'id_rozmiaru' => 20,
            'ilosc' => 5
        ];

        $this->issuedClothingRepo->expects($this->once())
            ->method('getUbraniaById')
            ->with($issuedItemId)
            ->willReturn($issuedData);

        $this->issuedClothingRepo->expects($this->once())
            ->method('deleteWydaneUbranieStatus')
            ->with($issuedItemId)
            ->willReturn(true);

        // verify stock is INCREASED (true argument)
        $this->warehouseRepo->expects($this->once())
            ->method('updateIlosc')
            ->with(10, 20, 5, true)
            ->willReturn(true);

        $this->service->cancelIssue($issuedItemId);
    }

    public function testShouldThrowExceptionWhenCancellingNonExistentIssuedItem(): void
    {
        $this->issuedClothingRepo->method('getUbraniaById')->willReturn(false);

        $this->expectException(\Exception::class);
        $this->service->cancelIssue(999);
    }

    public function testShouldThrowExceptionWhenDatabaseStatusUpdateFailsDuringCancellation(): void
    {
        $this->issuedClothingRepo->method('getUbraniaById')->willReturn(['id_ubrania' => 1, 'id_rozmiaru' => 1, 'ilosc' => 1]);
        $this->issuedClothingRepo->method('deleteWydaneUbranieStatus')->willReturn(false);

        $this->expectException(\Exception::class);
        $this->service->cancelIssue(100);
    }

    public function testShouldThrowExceptionWhenEmployeeNotFound(): void
    {
        $this->employeeRepo->method('getById')->willReturn(false);
        $this->expectException(\Exception::class);
        $this->service->issueClothing(999, self::EXAMPLE_USER_ID, [['id_ubrania' => 1, 'id_rozmiar' => 1, 'ilosc' => 1]]);
    }

    public function testShouldThrowExceptionWhenIssueHeaderCreationFails(): void
    {
        $this->employeeRepo->method('getById')->willReturn(['id_pracownik' => self::EXAMPLE_PRACOWNIK_ID]);
        $this->userRepo->method('getUserById')->willReturn(['id' => self::EXAMPLE_USER_ID]);
        $this->warehouseRepo->method('getIlosc')->willReturn(10);
        
        $this->issueRepo->method('create')->willReturn(false);

        $this->expectException(\Exception::class);
        $this->service->issueClothing(self::EXAMPLE_PRACOWNIK_ID, self::EXAMPLE_USER_ID, [['id_ubrania' => 1, 'id_rozmiar' => 1, 'ilosc' => 1]]);
    }

    public function testShouldThrowExceptionWhenIssuedItemCreationFails(): void
    {
        $this->employeeRepo->method('getById')->willReturn(['id_pracownik' => self::EXAMPLE_PRACOWNIK_ID]);
        $this->userRepo->method('getUserById')->willReturn(['id' => self::EXAMPLE_USER_ID]);
        $this->warehouseRepo->method('getIlosc')->willReturn(10);
        $this->issueRepo->method('create')->willReturn((string)self::EXAMPLE_ISSUE_ID);

        // Fail on the second item
        $this->issuedClothingRepo->method('create')
            ->willReturnOnConsecutiveCalls(true, false);

        $this->expectException(\Exception::class);
        $this->service->issueClothing(self::EXAMPLE_PRACOWNIK_ID, self::EXAMPLE_USER_ID, [
            ['id_ubrania' => 1, 'id_rozmiar' => 1, 'ilosc' => 1],
            ['id_ubrania' => 1, 'id_rozmiar' => 1, 'ilosc' => 1]
        ]);
    }
}
