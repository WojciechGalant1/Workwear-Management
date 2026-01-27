<?php
declare(strict_types=1);
namespace App\Http\Handlers\Issue;

require_once __DIR__ . '/../../../handler_bootstrap.php';

use App\Http\BaseHandler;
use App\Config\AccessLevels;

use App\Exceptions\ValidationException;
use App\Exceptions\AuthorizationException;
use App\Exceptions\NotFoundException;

class ChangeStatusHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::SUPERVISOR;
    
    public function handle(): void {
        $this->throttle('modify:change_status', 60, 60);

        $data = $this->getJsonInput();
        if (!is_array($data)) {
            throw new ValidationException('validation_invalid_input');
        }

        if (!$this->validateCsrf($data)) {
            throw new AuthorizationException('error_csrf');
        }

        if (!isset($data['id'], $data['currentStatus'])) {
            throw new ValidationException('validation_invalid_input');
        }
        
        $id = intval($data['id']);
        $currentStatus = intval($data['currentStatus']);
        $newStatus = ($currentStatus == 1) ? 0 : 1;
        
        $wydaneUbraniaRepo = $this->getRepository('IssuedClothingRepository');
        
        if ($wydaneUbraniaRepo->updateStatus($id, $newStatus)) {
            $this->jsonResponse(['success' => true, 'newStatus' => $newStatus]);
        } else {
            throw new \Exception('status_update_failed');
        }
    }
}


ChangeStatusHandler::run();
