<?php
// src/views/notifications/index.php
// Data yang dikirim dari NotificationController:
// $notifications (array), $totalUnread (int), $isAdmin (bool) [opsional]

$notifications = $notifications ?? [];
$totalUnread = $totalUnread ?? 0;
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';

include __DIR__ . '/../templates/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fas fa-bell me-2"></i> Semua Notifikasi</h2>
        <?php if ($totalUnread > 0): ?>
            <a href="index.php?url=notification/markAllRead" class="btn btn-outline-secondary btn-sm" onclick="return confirm('Tandai semua notifikasi sebagai sudah dibaca?')">
                <i class="fas fa-check-double"></i> Tandai semua sebagai dibaca
            </a>
        <?php endif; ?>
    </div>

    <?php if (empty($notifications)): ?>
        <div class="alert alert-info text-center py-5">
            <i class="fas fa-bell-slash fa-3x mb-3 d-block"></i>
            <h5>Belum ada notifikasi</h5>
            <p class="text-muted">Notifikasi akan muncul di sini saat ada aktivitas terkait akun Anda.</p>
        </div>
    <?php else: ?>
        <div class="list-group shadow-sm">
            <?php foreach ($notifications as $notif): ?>
                <a href="index.php?url=notification/read/<?= $notif['id'] ?>" 
                   class="list-group-item list-group-item-action <?= $notif['is_read'] ? '' : 'bg-light border-start border-primary border-3' ?>">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 <?= $notif['is_read'] ? '' : 'fw-bold' ?>">
                                <?= htmlspecialchars($notif['title']) ?>
                            </h5>
                            <p class="mb-1"><?= nl2br(htmlspecialchars($notif['message'])) ?></p>
                            <small class="text-muted">
                                <i class="far fa-clock"></i> <?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?>
                            </small>
                            <?php if ($isAdmin && isset($notif['user_name'])): ?>
                                <br><small class="text-muted">Untuk: <?= htmlspecialchars($notif['user_name']) ?></small>
                            <?php endif; ?>
                        </div>
                        <?php if (!$notif['is_read']): ?>
                            <span class="badge bg-primary rounded-pill">Baru</span>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>