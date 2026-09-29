<?php
include_once __DIR__ . '/../../../auth/session.php';
include_once __DIR__ . '/../classes/Announcement.php';

$announcement = new Announcement();
$announcements = $announcement->getAnnouncement();
?>

<div class="module-header">
    <h1>Communications & Announcements</h1>
    <p>Manage and view announcements for the school community.</p>
</div>

<div class="module-content">

    <div class="announcements-list">
        <div class="comm-list-header">
            <h2>Announcements</h2>
            <button type="button" id="comm-open-modal" class="comm-add-btn">+ Post Announcement</button>
        </div>

        <?php if (empty($announcements)): ?>
            <p class="muted">No announcements have been posted yet.</p>
        <?php else: ?>
            <?php foreach ($announcements as $ann): ?>
                <div class="announcement-card">
                    <?php if (!empty($ann['image_file'])): ?>
                        <div class="announcement-image">
                            <img src="/SMS/modules/school-directress/uploads/announcements/<?= htmlspecialchars($ann['image_file']) ?>" alt="<?= htmlspecialchars($ann['title']) ?>">
                        </div>
                    <?php endif; ?>
                    <div class="announcement-content">
                        <div class="announcement-header">
                            <span class="announcement-meta">
                                <span class="meta-date">&#128197; <?= date('F j, Y', strtotime($ann['publish_date'])) ?></span>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Post Announcement Modal -->
    <div class="comm-modal-overlay" id="comm-modal-overlay">
        <div class="comm-modal" role="dialog" aria-modal="true" aria-labelledby="comm-modal-title">
            <div class="comm-modal-header">
                <h3 id="comm-modal-title">Post Announcement</h3>
                <button type="button" class="comm-modal-close" id="comm-modal-close" aria-label="Close">&times;</button>
            </div>

            <div class="comm-modal-body">
                <div class="comm-form">
                    <form id="announcement-form" method="POST" action="controllers/AnnouncementController.php" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="ann-image">Image</label>
                            <input class="ann-image" id="ann-image" name="image" type="file" accept="image/jpeg,image/png,image/gif,image/webp">
                        </div>
                        <div class="form-row">
                            <div class="form-group small">
                                <label for="ann-date">Publish Date</label>
                                <input id="ann-date" name="publish_date" type="date">
                            </div>
                            <div class="form-group small">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn-save">Post Announcement</button>
                                    <button type="reset" class="btn-cancel">Clear</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>