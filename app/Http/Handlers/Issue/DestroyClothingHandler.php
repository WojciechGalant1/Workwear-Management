<?php
declare(strict_types=1);
namespace App\Http\Handlers\Issue;

require_once __DIR__ . '/../../../handler_bootstrap.php';

use App\Http\BaseHandler;
use App\Config\AccessLevels;

use App\Exceptions\ValidationException;
use App\Exceptions\AuthorizationException;
use App\Repositories\IssuedClothingRepository;

class DestroyClothingHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::SUPERVISOR;
    
    public function handle(): void {
        $this->throttle('modify:destroy_clothing', 30, 60);

        if (!$this->isPost()) {
            throw new ValidationException('error_method_not_allowed');
        }

        $data = $this->getJsonInput();
        if (!is_array($data)) {
            throw new ValidationException('validation_invalid_input');
        }

        if (!$this->validateCsrf($data)) {
            throw new AuthorizationException('error_csrf');
        }

        $id = intval($data['id'] ?? 0);
        
        if ($id <= 0) {
            throw new ValidationException('validation_clothing_id_required');
        }
        
        $wydaneUbraniaRepo = $this->getRepository(IssuedClothingRepository::class);
        
        if ($wydaneUbraniaRepo->destroyStatus($id)) {
            $this->successResponse();
        } else {
            throw new \Exception('status_update_failed');
        }
    }
}

DestroyClothingHandler::run();
