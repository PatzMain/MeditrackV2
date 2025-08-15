<?php
$pagesConfig = include '../../config/pages.php';
$pageData = $pagesConfig[$pageKey] ?? ['title' => 'Untitled', 'subtitle' => ''];

?>
<div class="page-header">
    <h1 class="page-title"><?php echo htmlspecialchars($pageData['title']); ?></h1>
    <p class="section-subtitle"><?php echo htmlspecialchars($pageData['subtitle']); ?></p>
</div>

