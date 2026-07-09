<?php
// src/controllers/NotificationController.php

require_once __DIR__ . '/../models/Notification.php';

class NotificationController {
    private $notifModel;

    public function __construct($pdo) {
        $this->notifModel = new Notification($pdo);
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?url=auth/loginForm");
            exit;
        }
    }

    public function index() {
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $notifications = $this->notifModel->getByUser($_SESSION['user_id'], $limit, $offset, false);
        $totalUnread = $this->notifModel->countUnread($_SESSION['user_id']);
        include __DIR__ . '/../views/notifications/index.php';
    }

    public function read($id) {
        $this->notifModel->markAsRead($id, $_SESSION['user_id']);
        $notifList = $this->notifModel->getByUser($_SESSION['user_id'], 100, 0, false);
        $target = null;
        foreach ($notifList as $n) {
            if ($n['id'] == $id) { $target = $n['link']; break; }
        }
        if ($target) header("Location: " . $target);
        else header("Location: index.php?url=notification/index");
        exit;
    }

    public function markAllRead() {
        $this->notifModel->markAllAsRead($_SESSION['user_id']);
        header("Location: index.php?url=notification/index");
        exit;
    }
}
?>