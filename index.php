<?php
require_once 'db.php';

$stmt1 = $pdo->query("SELECT DISTINCT PUBLISHER FROM literature ORDER BY PUBLISHER");
$publishers = $stmt1->fetchAll();

$stmt2 = $pdo->query("SELECT Id, NAME FROM author ORDER BY NAME");
$authors = $stmt2->fetchAll();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Бібліотека PDO</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Інформаційні ресурси бібліотеки</h1>

    <div class="block">
        <h2>1. Пошук книг за видавництвом</h2>
        <form action="publisher.php" method="get">
            <label>Оберіть назву видавництва:</label>
            <select name="publisher" required>
                <?php foreach ($publishers as $pub): ?>
                    <option value="<?= htmlspecialchars($pub['PUBLISHER']) ?>">
                        <?= htmlspecialchars($pub['PUBLISHER']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Результати пошуку</button>
        </form>
    </div>

    <div class="block">
        <h2>2. Пошук літератури за часовим періодом</h2>
        <form action="period.php" method="get">
            <label>Рік від:</label>
            <input type="number" name="year_from" value="2000" required>

            <label>Рік до:</label>
            <input type="number" name="year_to" value="2025" required>

            <button type="submit">Результати пошуку</button>
        </form>
    </div>

    <div class="block">
        <h2>3. Пошук книг за автором</h2>
        <form action="author.php" method="get">
            <label>Оберіть автора:</label>
            <select name="author_id" required>
                <?php foreach ($authors as $author): ?>
                    <option value="<?= $author['Id'] ?>">
                        <?= htmlspecialchars($author['NAME']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Результати пошуку</button>
        </form>
    </div>
</body>
</html>