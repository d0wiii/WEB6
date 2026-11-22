<?php
require_once 'db.php';
require_once 'functions.php';

$submittedTime = null;
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedTime = saveSurveyResponse($pdo, $_POST);
    if (!$submittedTime) {
        $errorMessage = 'Будь ласка, заповніть усі обов’язкові поля!';
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Космічне опитування — Лабораторна №6</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: radial-gradient(circle at top, #1b2735 0%, #090a0f 100%);
            color: #e0e6ed;
            margin: 0;
            padding: 30px 15px;
            min-height: 100vh;
        }
        .container {
            max-width: 650px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        h1 { text-align: center; color: #64b5f6; margin-top: 0; }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; color: #bbdefb; }
        input[type="text"], input[type="email"], select, textarea {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #37474f;
            background: #101622;
            color: #fff;
            box-sizing: border-box;
        }
        .radio-group label { font-weight: normal; display: inline-block; margin-right: 15px; cursor: pointer; }
        button {
            width: 100%;
            padding: 12px;
            background: #1e88e5;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover { background: #1565c0; }
        .alert-success {
            background: rgba(46, 125, 50, 0.25);
            border: 1px solid #4caf50;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #a5d6a7;
        }
        .alert-error {
            background: rgba(198, 40, 40, 0.25);
            border: 1px solid #ef5350;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #ef9a9a;
        }
        .nav-links { text-align: center; margin-top: 20px; font-size: 14px; }
        .nav-links a { color: #64b5f6; text-decoration: none; margin: 0 10px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Анкета дослідника космосу</h1>

    <?php if ($submittedTime): ?>
        <div class="alert-success">
            <strong>Дякуємо за участь в опитуванні!</strong><br>
            Вашу відповідь успішно збережено у базу даних та файл.<br>
            Дата та час заповнення форми: <strong><?= htmlspecialchars($submittedTime) ?></strong>
        </div>
    <?php elseif ($errorMessage): ?>
        <div class="alert-error"><?= $errorMessage ?></div>
    <?php endif; ?>

    <form method="POST" action="survey.php">
        <div class="form-group">
            <label for="name">Ваше ім’я *</label>
            <input type="text" id="name" name="name" placeholder="Наприклад: Олександр" required>
        </div>

        <div class="form-group">
            <label for="email">Ваш Email *</label>
            <input type="email" id="email" name="email" placeholder="student@vntu.edu.ua" required>
        </div>

        <div class="form-group">
            <label for="q1">1. Яка космічна місія вражає вас найбільше? *</label>
            <select id="q1" name="q1" required>
                <option value="Apollo 11 (Висадка на Місяць)">Apollo 11 (Висадка на Місяць)</option>
                <option value="Voyager 1 та 2 (Міжзоряний простір)">Voyager 1 та 2 (Міжзоряний простір)</option>
                <option value="Mars Perseverance (Дослідження Марса)">Mars Perseverance (Дослідження Марса)</option>
                <option value="Artemis (Повернення людей на Місяць)">Artemis (Повернення людей на Місяць)</option>
            </select>
        </div>

        <div class="form-group radio-group">
            <label>2. Яку планету людство має колонізувати першою? *</label>
            <label><input type="radio" name="q2" value="Марс" checked> Марс</label>
            <label><input type="radio" name="q2" value="Місяць (супутник)"> Місяць</label>
            <label><input type="radio" name="q2" value="Титан (супутник Сатурна)"> Титан</label>
        </div>

        <div class="form-group">
            <label for="q3">3. Який космічний телескоп дав найцінніші знімки? *</label>
            <select id="q3" name="q3" required>
                <option value="James Webb (JWST)">James Webb (JWST)</option>
                <option value="Hubble">Hubble</option>
                <option value="Kepler">Kepler</option>
            </select>
        </div>

        <div class="form-group">
            <label for="q4">4. Ваші думки або побажання щодо розвитку космонавтики:</label>
            <textarea id="q4" name="q4" rows="3" placeholder="Напишіть ваш коментар..."></textarea>
        </div>

        <button type="submit">Надіслати відповідь</button>
    </form>

    <div class="nav-links">
        <a href="index.html">← На головну</a> |
        <a href="admin.php">Панель адміністратора</a>
    </div>
</div>
</body>
</html>