<?php
// functions.php — основні PHP-функції проєкту

/**
 * Валідація та збереження відповіді у файл (.json) та базу даних MySQL
 */
function saveSurveyResponse(PDO $pdo, array $postData): ?string {
    $name = trim(htmlspecialchars($postData['name'] ?? ''));
    $email = trim(htmlspecialchars($postData['email'] ?? ''));
    $q1 = trim(htmlspecialchars($postData['q1'] ?? ''));
    $q2 = trim(htmlspecialchars($postData['q2'] ?? ''));
    $q3 = trim(htmlspecialchars($postData['q3'] ?? ''));
    $q4 = trim(htmlspecialchars($postData['q4'] ?? ''));

    if (empty($name) || empty($email) || empty($q1) || empty($q2) || empty($q3)) {
        return null;
    }

    // Встановлюємо київський часовий пояс
    date_default_timezone_set('Europe/Kyiv');
    $nowFormatted = date('Y-m-d H:i:s');
    $fileTimestamp = date('Y-m-d_H-i-s');

    // 1. Збереження у текстовий JSON-файл у папці survey
    $surveyDir = __DIR__ . '/survey';
    if (!is_dir($surveyDir)) {
        mkdir($surveyDir, 0777, true);
    }

    $record = [
        'name' => $name,
        'email' => $email,
        'answers' => [
            'Найцікавіша космічна місія' => $q1,
            'Планета для колонізації' => $q2,
            'Улюблений телескоп' => $q3,
            'Побажання щодо космонавтики' => $q4
        ],
        'submitted_at' => $nowFormatted
    ];

    $filename = $surveyDir . "/response_{$fileTimestamp}_" . rand(100, 999) . ".json";
    file_put_contents($filename, json_encode($record, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    // 2. Збереження у справжню реляційну БД MySQL 
    $sql = "INSERT INTO responses (respondent_name, respondent_email, q1_mission, q2_planet, q3_telescope, q4_comment, submitted_at)
            VALUES (:name, :email, :q1, :q2, :q3, :q4, :submitted_at)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':q1' => $q1,
        ':q2' => $q2,
        ':q3' => $q3,
        ':q4' => $q4,
        ':submitted_at' => $nowFormatted
    ]);

    return $nowFormatted;
}

/**
 * Отримати всі відповіді з БД для адмін-панелі
 */
function getAllResponses(PDO $pdo): array {
    $stmt = $pdo->query("SELECT * FROM responses ORDER BY submitted_at DESC");
    return $stmt->fetchAll();
}

/**
 * Видалити запис за ID
 */
function deleteResponseById(PDO $pdo, int $id): void {
    $stmt = $pdo->prepare("DELETE FROM responses WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

/**
 * Експорт усіх відповідей у CSV-файл (відкривається в Excel)
 */
function exportResponsesToCSV(PDO $pdo): void {
    $rows = getAllResponses($pdo);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="space_survey_export_' . date('Y-m-d_H-i') . '.csv"');

    $output = fopen('php://output', 'w');
    // Додаємо BOM для коректного відображення кирилиці в Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    fputcsv($output, ['ID', 'Ім’я', 'Email', 'Місія', 'Планета', 'Телескоп', 'Коментар', 'Дата та час'], ';');
    foreach ($rows as $row) {
        fputcsv($output, $row, ';');
    }
    fclose($output);
    exit;
}