<?php
// Detect if inside patient_monitoring (depth 2)
$isMonitoring = strpos($_SERVER['PHP_SELF'], 'patient_monitoring/') !== false;

// Base path depending on depth
$basePath = $isMonitoring ? '../../' : '../';
?>

<link rel="stylesheet" href="<?= $basePath ?>css/pages.css">
<link rel="stylesheet" href="<?= $basePath ?>css/navbar.css">
<link rel="stylesheet" href="<?= $basePath ?>css/cards.css">
<link rel="stylesheet" href="<?= $basePath ?>css/table.css">
<link rel="stylesheet" href="<?= $basePath ?>css/search.css">
<link rel="stylesheet" href="<?= $basePath ?>css/modal.css">
