<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/notify.php';

function tickets_get(PDO $pdo, array $opts = []) {
    $params = [];
    $sql = 'SELECT * FROM tickets';
    if (isset($opts['user_id'])) {
        $sql .= ' WHERE user_id = ?';
        $params[] = $opts['user_id'];
    } elseif (isset($opts['status'])) {
        $sql .= ' WHERE status = ?';
        $params[] = $opts['status'];
    }
    $sql .= ' ORDER BY created_at DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function tickets_create(PDO $pdo, int $user_id, string $subject, string $message, ?string $category = null) {
    $stmt = $pdo->prepare('INSERT INTO tickets (user_id, subject, message, category) VALUES (?,?,?,?)');
    $stmt->execute([$user_id, $subject, $message, $category]);
    $ticket_id = $pdo->lastInsertId();
    $admins = $pdo->query('SELECT id,email FROM admins')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($admins as $a) {
        $pdo->prepare('INSERT INTO notifications (admin_id, message) VALUES (?,?)')
            ->execute([$a['id'], "تیکت جدید توسط کاربر {$user_id} ثبت شد"]);
        sendEmail($a['email'], 'تیکت جدید', 'تیکت تازه‌ای ثبت شده است');
    }
    return $ticket_id;
}

function tickets_reply(PDO $pdo, int $ticket_id, int $admin_id, string $response) {
    $stmt = $pdo->prepare('UPDATE tickets SET response=?, status="answered", admin_id=?, responded_at=NOW() WHERE id=?');
    $stmt->execute([$response, $admin_id, $ticket_id]);
    $info = $pdo->prepare('SELECT u.email, u.phone, u.id FROM tickets t JOIN users u ON t.user_id=u.id WHERE t.id=?');
    $info->execute([$ticket_id]);
    if ($row = $info->fetch(PDO::FETCH_ASSOC)) {
        sendEmail($row['email'], 'پاسخ پشتیبانی', $response);
        if (!empty($row['phone'])) {
            sendSMS($row['phone'], 'پاسخ جدید به تیکت شما ثبت شد');
        }
        $pdo->prepare('INSERT INTO notifications (user_id, message) VALUES (?,?)')
            ->execute([$row['id'], 'پاسخ جدید به تیکت شما ثبت شد']);
    }
}

function tickets_close(PDO $pdo, int $ticket_id) {
    $pdo->prepare('UPDATE tickets SET status="closed" WHERE id=?')->execute([$ticket_id]);
}

if (realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME'])) {
    header('Content-Type: application/json; charset=utf-8');
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'GET') {
        if (isset($_SESSION['user'])) {
            echo json_encode(tickets_get($pdo, ['user_id' => $_SESSION['user']['id']]));
        } elseif (isset($_SESSION['admin'])) {
            $status = $_GET['status'] ?? null;
            echo json_encode(tickets_get($pdo, $status ? ['status' => $status] : []));
        } else {
            http_response_code(403);
            echo json_encode(['error' => 'unauthorized']);
        }
    } elseif ($method === 'POST' && isset($_SESSION['user'])) {
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $category = trim($_POST['category'] ?? '');
        if ($subject && $message) {
            $id = tickets_create($pdo, $_SESSION['user']['id'], $subject, $message, $category ?: null);
            echo json_encode(['id' => $id]);
        } else {
            http_response_code(422);
            echo json_encode(['error' => 'invalid']);
        }
    } elseif ($method === 'POST' && isset($_SESSION['admin'])) {
        $id = (int)($_POST['ticket_id'] ?? 0);
        $response = trim($_POST['response'] ?? '');
        if ($id && $response) {
            tickets_reply($pdo, $id, $_SESSION['admin']['id'], $response);
            echo json_encode(['ok' => true]);
        } else {
            http_response_code(422);
            echo json_encode(['error' => 'invalid']);
        }
    } elseif ($method === 'DELETE' && isset($_SESSION['admin'])) {
        parse_str(file_get_contents('php://input'), $del);
        $id = (int)($del['ticket_id'] ?? 0);
        if ($id) {
            tickets_close($pdo, $id);
            echo json_encode(['ok' => true]);
        } else {
            http_response_code(422);
            echo json_encode(['error' => 'invalid']);
        }
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'method']);
    }
    exit;
}
?>
