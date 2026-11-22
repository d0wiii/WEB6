<?php
// db.php — підключення до реляційної SQL-бази даних SQLite
$dbFile = __DIR__ . '/space_survey.sqlite';

try {
    $pdo = new PDO("sqlite:" . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Автоматичне створення SQL-таблиці при першому відкритті сайту
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS responses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            respondent_name TEXT NOT NULL,
            respondent_email TEXT NOT NULL,
            q1_mission TEXT NOT NULL,
            q2_planet TEXT NOT NULL,
            q3_telescope TEXT NOT NULL,
            q4_comment TEXT,
            submitted_at TEXT NOT NULL
        )
    ");
} catch (PDOException $e) {
    die("Помилка підключення до бази даних SQLite: " . $e->getMessage());
}