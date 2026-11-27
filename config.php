<?php
$pdo = new PDO(
    "mysql:host=10.10.10.13;dbname=loja_games;charset=utf8mb4",
    "loja_user",
    "Loja@2026!",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);
