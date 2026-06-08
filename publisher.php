<?php
require_once 'db.php';

$publisher = $_GET['publisher'] ?? '';

$sql = "SELECT NAME, ISBN, PUBLISHER, YEAR, NUMBER, QUANTITY
        FROM literature
        WHERE PUBLISHER = :publisher AND LITERATE = 'Book'
        ORDER BY NAME";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':publisher', $publisher, PDO::PARAM_STR);
$stmt->execute();

$rows = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Книги видавництва</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Результати пошуку за видавництвом</h1>
    <p><b>Видавництво:</b> <?= htmlspecialchars($publisher) ?></p>

    <form action="pdf_export.php" method="get">
        <input type="hidden" name="type" value="publisher">
        <input type="hidden" name="publisher" value="<?= htmlspecialchars($publisher) ?>">
        <button type="submit" class="pdf-button">Завантажити PDF</button>
    </form>

    <?php if ($rows): ?>
        <table>
            <tr>
                <th>Name</th>
                <th>ISBN</th>
                <th>Publisher</th>
                <th>Year</th>
                <th>Pages</th>
                <th>Count</th>
            </tr>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['NAME']) ?></td>
                    <td><?= htmlspecialchars($row['ISBN']) ?></td>
                    <td><?= htmlspecialchars($row['PUBLISHER']) ?></td>
                    <td><?= htmlspecialchars($row['YEAR']) ?></td>
                    <td><?= htmlspecialchars($row['NUMBER']) ?></td>
                    <td><?= htmlspecialchars($row['QUANTITY']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Нічого не знайдено.</p>
    <?php endif; ?>

    <p><a href="index.php">Повернутися назад</a></p>
</body>
</html>