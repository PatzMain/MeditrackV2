<?php
// Load config and select the correct page set
$config = include '../../config/stats_cards.php';
$statsData = $config[$pageKey] ?? [];

if (!empty($statsData) && is_array($statsData)): ?>
    <div class="stats-cards">
        <?php foreach ($statsData as $item): ?>
            <?php
            $value    = $item['value']    ?? 0;
            $label    = $item['label']    ?? 'N/A';
            $bgColor  = $item['bgColor']  ?? '#f5f5f5';
            $svgColor = $item['svgColor'] ?? '#000';
            $svg      = $item['svg']      ?? '';
            ?>
            
            <div class="stat-card" style="background-color: <?= htmlspecialchars($bgColor) ?>;">
                <div class="stat-icon" style="color: <?= htmlspecialchars($svgColor) ?>;">
                    <?php if (!empty($svg)): ?>
                        <?php if (stripos($svg, '<svg') !== false): ?>
                            <?= $svg ?>
                        <?php else: ?>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="<?= htmlspecialchars($svgColor) ?>">
                                <?= $svg ?>
                            </svg>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?= htmlspecialchars((string)$value) ?></div>
                    <div class="stat-label"><?= htmlspecialchars($label) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
