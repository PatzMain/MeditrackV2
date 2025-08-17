<?php
/**
 * Table AJAX Handler
 * Processes all AJAX requests for table operations
 */

// Include required files
require_once 'connection.php';
require_once 'table_config.php';
require_once 'TableLayout.php';

// Set JSON header for AJAX responses (except for exports)
$action = $_REQUEST['action'] ?? '';
if ($action !== 'export') {
    header('Content-Type: application/json');
}

// Handle the request
try {
    TableLayout::handleAjaxRequest();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>