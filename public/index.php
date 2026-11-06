<?php
require_once __DIR__ . '/../app/core/Database.php';

$pdo = Database::getInstance()->getConnection();
$count = $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();

echo "Connected. Categories: $count";