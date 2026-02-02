<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\ClothingExpiryService;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ClothingExpiryService::class)]
class ClothingExpiryServiceTest extends TestCase
{
    private ClothingExpiryService $service;

    protected function setUp(): void
    {
        $this->service = new ClothingExpiryService();
    }

    public function testShouldReturnCorrectStatusTextBasedOnDate(): void
    {
        $now = new \DateTime();
        
        // Past date
        $expiredDate = (clone $now)->modify('-1 day');
        $this->assertSame('Przeterminowane', $this->service->getStatusText($expiredDate));

        // Future date but within 2 months (soon)
        $expiringSoonDate = (clone $now)->modify('+1 month');
        $this->assertSame('Koniec ważności', $this->service->getStatusText($expiringSoonDate));

        // Far future date
        $farFutureDate = (clone $now)->modify('+1 year');
        $this->assertSame('Brak danych', $this->service->getStatusText($farFutureDate));
    }

    public function testShouldCorrectlyDetermineIfItemIsExpiredIncludingExactNow(): void
    {
        $past = (new \DateTime())->modify('-1 minute');
        $future = (new \DateTime())->modify('+1 minute');
        $now = new \DateTime();

        $this->assertTrue($this->service->isExpired($past), 'Data przeszła powinna być wygaśnięta');
        $this->assertTrue($this->service->isExpired($now), 'Dokładnie teraz powinno być traktowane jako wygaśnięte (<= now)');
        $this->assertFalse($this->service->isExpired($future), 'Data przyszła nie powinna być wygaśnięta');
    }

    public function testShouldCorrectlyDetermineIfItemIsExpiringSoonIncludingBoundary(): void
    {
        $now = new \DateTime();
        $soon = (clone $now)->modify('+1 month'); 
        $exactlyTwoMonths = (clone $now)->modify('+2 months'); // Boundary
        $notSoon = (clone $now)->modify('+61 days'); 

        $this->assertTrue($this->service->isExpiringSoon($soon), '1 miesiąc to "soon"');
        $this->assertTrue($this->service->isExpiringSoon($exactlyTwoMonths), 'Dokładnie 2 miesiące to granica "soon"');
        $this->assertFalse($this->service->isExpiringSoon($now), 'Obecna chwila to "expired", nie "soon"');
        $this->assertFalse($this->service->isExpiringSoon($notSoon), 'Powyżej 2 miesięcy to już nie "soon"');
    }

    public function testShouldReturnReportingStatus(): void
    {
        $expired = (new \DateTime())->modify('-1 day');
        $ok = (new \DateTime())->modify('+1 year');

        $this->assertEquals(1, $this->service->canBeReported($expired), 'Przeterminowane powinno być raportowane');
        $this->assertEquals(0, $this->service->canBeReported($ok), 'Aktualne nie powinno być raportowane');
    }

    public function testShouldReturnCorrectDateFormatForSqlQueries(): void
    {
        $expectedWarningDate = (new \DateTime())->modify('+2 months')->format('Y-m-d');
        $expectedHistoryDate = (new \DateTime())->modify('-6 months')->format('Y-m-d');
        $expectedNow = (new \DateTime())->format('Y-m-d');

        $this->assertSame($expectedWarningDate, $this->service->getExpiryWarningDateFormatted());
        $this->assertSame($expectedHistoryDate, $this->service->getHistoryStartDateFormatted());
        $this->assertSame($expectedNow, $this->service->getCurrentDateFormatted());
    }
}
