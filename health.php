<?php
header('Content-Type: application/json; charset=utf-8');
try {
    require_once 'config.php';
    $total = $pdo->query("SELECT COUNT(*) FROM games")->fetchColumn();
    echo json_encode([
        'status' => 'healthy',
        'app_server' => gethostname(),
        'php_version' => phpversion(),
        'database' => 'connected',
        'games_catalog' => (int)$total
    ], JSON_PRETTY_PRINT);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['status' => 'unhealthy', 'error' => $e->getMessage()]);
}
