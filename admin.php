<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

// Облікові дані адміністратора
define('ADMIN_LOGIN', 'admin');
define('ADMIN_PASS', 'vntu2026');

$loginError = '';

// Вихід із системи
if (isset($_GET['logout'])) {
    unset($_SESSION['is_admin']);
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Авторизація
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $login = $_POST['login'] ?? '';
    $pass = $_POST['password'] ?? '';

    if ($login === ADMIN_LOGIN && $pass === ADMIN_PASS) {
        $_SESSION['is_admin'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $loginError = 'Невірний логін або пароль!';
    }
}

// Дії авторизованого адміністратора (видалення та експорт)
if (!empty($_SESSION['is_admin'])) {
    if (isset($_GET['delete_id'])) {
        deleteResponseById($pdo, (int)$_GET['delete_id']);
        header('Location: admin.php');
        exit;
    }

    if (isset($_GET['export']) && $_GET['export'] === 'csv') {
        exportResponsesToCSV($pdo);
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Адмін-панель — Результати опитування</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: #0d1117;
            color: #c9d1d9;
            margin: 0;
            padding: 30px;
        }
        .login-box {
            max-width: 360px;
            margin: 80px auto;
            background: #161b22;
            padding: 25px;
            border-radius: 10px;
            border: 1px solid #30363d;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0 16px;
            background: #0d1117;
            border: 1px solid #30363d;
            color: #fff;
            border-radius: 6px;
            box-sizing: border-box;
        }
        .btn {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            border: none;
        }
        .btn-primary { background: #238636; color: #fff; width: 100%; padding: 10px; }
        .btn-export { background: #1f6feb; color: #fff; }
        .btn-danger { background: #da3633; color: #fff; font-size: 13px; padding: 5px 10px; }
        .btn-logout { background: #30363d; color: #c9d1d9; }
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #161b22;
        }
        th, td {
            padding: 12px;
            border: 1px solid #30363d;
            text-align: left;
            font-size: 14px;
        }
        th { background: #21262d; color: #58a6ff; }
    </style>
</head>
<body>

<?php if (empty($_SESSION['is_admin'])): ?>
    <div class="login-box">
        <h2 style="margin-top:0; text-align:center;">Вхід для адміністратора</h2>
        <?php if ($loginError): ?>
            <p style="color:#f85149;"><?= $loginError ?></p>
        <?php endif; ?>
        <form method="POST" action="admin.php">
            <label>Логін:</label>
            <input type="text" name="login" required>
            <label>Пароль:</label>
            <input type="password" name="password" required>
            <button type="submit" name="login_submit" class="btn btn-primary">Увійти</button>
        </form>
        <p style="text-align:center; margin-top:15px;"><a href="survey.php" style="color:#58a6ff;">← До анкети</a></p>
    </div>

<?php else: ?>
    <?php $responses = getAllResponses($pdo); ?>
    <div class="toolbar">
        <h2>Відповіді респондентів (усього: <?= count($responses) ?>)</h2>
        <div>
            <a href="admin.php?export=csv" class="btn btn-export">Експортувати в CSV (Excel)</a>
            <a href="survey.php" class="btn btn-logout">До анкети</a>
            <a href="admin.php?logout=1" class="btn btn-logout">Вийти</a>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Ім’я</th>
                <th>Email</th>
                <th>Місія</th>
                <th>Планета</th>
                <th>Телескоп</th>
                <th>Коментар</th>
                <th>Час заповнення</th>
                <th>Дія</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($responses as $row): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['respondent_name']) ?></td>
                    <td><?= htmlspecialchars($row['respondent_email']) ?></td>
                    <td><?= htmlspecialchars($row['q1_mission']) ?></td>
                    <td><?= htmlspecialchars($row['q2_planet']) ?></td>
                    <td><?= htmlspecialchars($row['q3_telescope']) ?></td>
                    <td><?= htmlspecialchars($row['q4_comment']) ?></td>
                    <td><?= $row['submitted_at'] ?></td>
                    <td>
                        <a href="admin.php?delete_id=<?= $row['id'] ?>"
                           class="btn btn-danger"
                           onclick="return confirm('Видалити цей запис?');">Видалити</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($responses)): ?>
                <tr><td colspan="9" style="text-align:center;">Відповідей поки немає.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>