<?php

class NotificationController {

    public function __construct() {
        if (!Auth::check()) {
            http_response_code(401);
            die(json_encode(['error' => 'Unauthorized']));
        }
    }

    // Get unread notifications for navbar bell
    public function unread() {
        $db = Database::getInstance()->getConnection();
        $user = Auth::user();
        
        $role = $user['role'];
        $user_id = $user['id'];

        $stmt = $db->prepare("
            SELECT * FROM notifikasi 
            WHERE (target_user_id = ?) 
               OR (target_user_id IS NULL AND target_role = ?) 
               OR (target_role = 'semua')
            ORDER BY created_at DESC LIMIT 10
        ");
        $stmt->execute([$user_id, $role]);
        $notifs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $unread_count = 0;
        foreach ($notifs as &$n) {
            $read_by = json_decode($n['read_by_json'] ?: '[]', true);
            $n['is_read'] = in_array($user_id, $read_by);
            if (!$n['is_read']) $unread_count++;
        }

        header('Content-Type: application/json');
        echo json_encode(['count' => $unread_count, 'data' => $notifs]);
    }

    // Mark as read
    public function mark_read($notif_id) {
        $db = Database::getInstance()->getConnection();
        $user_id = Auth::user()['id'];

        $stmt = $db->prepare("SELECT read_by_json FROM notifikasi WHERE id = ?");
        $stmt->execute([$notif_id]);
        $json = $stmt->fetchColumn();

        if ($json !== false) {
            $read_by = json_decode($json ?: '[]', true);
            if (!in_array($user_id, $read_by)) {
                $read_by[] = $user_id;
                $update = $db->prepare("UPDATE notifikasi SET read_by_json = ? WHERE id = ?");
                $update->execute([json_encode($read_by), $notif_id]);
            }
        }
        echo json_encode(['success' => true]);
    }

    // Mark all as read
    public function mark_all_read() {
        $db = Database::getInstance()->getConnection();
        $user = Auth::user();
        $user_id = $user['id'];
        $role = $user['role'];

        // Get all unread notifs for this user
        $stmt = $db->prepare("
            SELECT id, read_by_json FROM notifikasi 
            WHERE (target_user_id = ?) 
               OR (target_user_id IS NULL AND target_role = ?) 
               OR (target_role = 'semua')
        ");
        $stmt->execute([$user_id, $role]);
        $notifs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($notifs as $n) {
            $read_by = json_decode($n['read_by_json'] ?: '[]', true);
            if (!in_array($user_id, $read_by)) {
                $read_by[] = $user_id;
                $update = $db->prepare("UPDATE notifikasi SET read_by_json = ? WHERE id = ?");
                $update->execute([json_encode($read_by), $n['id']]);
            }
        }
        echo json_encode(['success' => true]);
    }

    // Save Push Subscription
    public function subscribe() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['endpoint'])) {
            http_response_code(400);
            return;
        }

        $db = Database::getInstance()->getConnection();
        $user_id = Auth::user()['id'];
        
        $endpoint = $data['endpoint'];
        $p256dh = $data['keys']['p256dh'] ?? '';
        $auth = $data['keys']['auth'] ?? '';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        $stmtCek = $db->prepare("SELECT id FROM push_subscriptions WHERE endpoint = ?");
        $stmtCek->execute([$endpoint]);
        
        if ($stmtCek->fetch()) {
            $stmtUp = $db->prepare("UPDATE push_subscriptions SET user_id = ?, p256dh_key = ?, auth_key = ?, user_agent = ? WHERE endpoint = ?");
            $stmtUp->execute([$user_id, $p256dh, $auth, $userAgent, $endpoint]);
        } else {
            $stmtIn = $db->prepare("INSERT INTO push_subscriptions (user_id, endpoint, p256dh_key, auth_key, user_agent) VALUES (?, ?, ?, ?, ?)");
            $stmtIn->execute([$user_id, $endpoint, $p256dh, $auth, $userAgent]);
        }

        echo json_encode(['success' => true]);
    }
}
