<?php
/** @var array<int,array<string,mixed>> $categories */
?>
<div class="list-group list-group-flush border rounded bg-white shadow-sm">
    <?php foreach ($categories as $category): ?>
        <a href="/categories/<?= htmlspecialchars($category['slug']); ?>" class="list-group-item list-group-item-action d-flex align-items-center py-2">
            <i class="bi bi-collection me-2 text-warning"></i>
            <?= htmlspecialchars($category['name']); ?>
        </a>
    <?php endforeach; ?>
</div>