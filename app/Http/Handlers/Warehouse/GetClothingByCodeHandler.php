<?php
declare(strict_types=1);
namespace App\Http\Handlers\Warehouse;

require_once __DIR__ . '/../../../handler_bootstrap.php';

use App\Http\BaseHandler;
use App\Config\AccessLevels;

class GetClothingByCodeHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::USER;
    
    public function handle(): void {
        if (!isset($_GET['kod'])) {
            $this->jsonResponse(['error' => $this->translate('validation_required')]);
            return;
        }
        
        $kodRepo = $this->getRepository('CodeRepository');
        $kodData = $kodRepo->findByNazwa($_GET['kod']);
        
        if ($kodData) {
            $this->jsonResponse([
                'id_ubrania' => $kodData['id_ubrania'],
                'nazwa_ubrania' => $kodData['nazwa_ubrania'],
                'id_rozmiar' => $kodData['id_rozmiar'],
                'nazwa_rozmiaru' => $kodData['nazwa_rozmiaru']
            ]);
        } else {
            $this->jsonResponse(['error' => $this->translate('clothing_code_not_found')]);
        }
    }
}

GetClothingByCodeHandler::run();
