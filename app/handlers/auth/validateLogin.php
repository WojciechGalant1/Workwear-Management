<?php
include_once __DIR__ . '/../../models/User.php';
include_once __DIR__ . '/../../services/database/Database.php';
include_once __DIR__ . '/../../auth/SessionManager.php';
include_once __DIR__ . '/../../helpers/CsrfHelper.php';
include_once __DIR__ . '/../../helpers/LocalizationHelper.php';
include_once __DIR__ . '/../../helpers/LanguageSwitcher.php';

LanguageSwitcher::initializeWithRouting();

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CsrfHelper::validateToken()) {
        http_response_code(403);
        echo json_encode(CsrfHelper::getErrorResponse());
        exit;
    }
}

try {
    $db = new Database();
    $pdo = $db->getPdo();

    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $kodID = isset($_POST['kodID']) ? trim($_POST['kodID']) : '';

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare('SELECT * FROM uzytkownicy WHERE nazwa = :username LIMIT 1');
        $stmt->execute(array(':username' => $username));
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $hashed_password = $user['password'];
            if (crypt($password, $hashed_password) == $hashed_password) {
                $sessionManager = new SessionManager();
                $sessionManager->login($user['id'], $user['status']);

                echo json_encode(array('status' => 'success', 'message' => LocalizationHelper::translate('login_success')));
            } else {
                echo json_encode(array('status' => 'error', 'message' => LocalizationHelper::translate('login_invalid_credentials')));
            }
        } else {
            echo json_encode(array('status' => 'error', 'message' => LocalizationHelper::translate('login_invalid_credentials')));
        }
    } elseif (!empty($kodID)) {
        // Very basic rate-limit using session 
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
        }
        $_SESSION['login_attempts']++;
        if ($_SESSION['login_attempts'] > 20) {
            usleep(500000); // 0.5s delay
        }

        $stmt = $pdo->prepare('SELECT * FROM uzytkownicy WHERE id_id = :kodID LIMIT 1');
        $stmt->execute(array(':kodID' => $kodID));
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $sessionManager = new SessionManager();
            $sessionManager->login($user['id'], $user['status']);

            echo json_encode(array('status' => 'success', 'message' => LocalizationHelper::translate('login_success')));
        } else {
            echo json_encode(array('status' => 'error', 'message' => LocalizationHelper::translate('login_invalid_code')));
        }
    } else {
        echo json_encode(array('status' => 'error', 'message' => LocalizationHelper::translate('login_no_credentials')));
    }
} catch (PDOException $e) {
    echo json_encode(array('status' => 'error', 'message' => LocalizationHelper::translate('login_connection_failed') . ': ' . $e->getMessage()));
}


