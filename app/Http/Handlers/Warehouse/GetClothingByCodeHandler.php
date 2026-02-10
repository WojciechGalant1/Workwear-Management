<?php
declare(strict_types=1);
namespace App\Http\Handlers\Warehouse;

require_once __DIR__ . '/../../../handler_bootstrap.php';

use App\Http\BaseHandler;
use App\Config\AccessLevels;

use App\Exceptions\ValidationException;
use App\Exceptions\NotFoundException;
use App\Repositories\CodeRepository;

class GetClothingByCodeHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::USER;
    
    public function handle(): void {
        $this->throttle('search:clothing_by_code', 60, 60);
        
        if (!isset($_GET['kod'])) {
            throw new ValidationException('validation_required');
        }
        
        $kodRepo = $this->getRepository(CodeRepository::class);
        $kodData = $kodRepo->findByNazwa($_GET['kod']);
        
        if ($kodData) {
            $this->jsonResponse([
                'id_ubrania' => $kodData['id_ubrania'],
                'nazwa_ubrania' => $kodData['nazwa_ubrania'],
                'id_rozmiar' => $kodData['id_rozmiar'],
                'nazwa_rozmiaru' => $kodData['nazwa_rozmiaru']
            ]);
        } else {
            // Return 200 OK with error indicator for frontend to use its own translation
            $this->jsonResponse(['error' => true]);
        }
    }
}

GetClothingByCodeHandler::run();
