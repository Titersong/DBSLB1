<?php
require_once 'db.php';

$yearFrom = $_GET['year_from'] ?? 0;
$yearTo   = $_GET['year_to'] ?? 9999;

$sql = "SELECT NAME, PUBLISHER, YEAR, LITERATE, ISBN, NUMBER, QUANTITY
        FROM literature
        WHERE YEAR BETWEEN :year_from AND :year_to
          AND LITERATE IN ('Book', 'Journal', 'Newspaper')
        ORDER BY YEAR, NAME";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':year_from', $yearFrom, PDO::PARAM_INT);
$stmt->bindValue(':year_to', $yearTo, PDO::PARAM_INT);
$stmt->execute();

$rows = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Література за період</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Результати пошуку за часовим періодом</h1>
    <p><b>Період:</b> <?= htmlspecialchars($yearFrom) ?> - <?= htmlspecialchars($yearTo) ?></p>

    <?php if ($rows): ?>
        <table>
            <tr>
                <th>Name</th>
                <th>Publisher</th>
                <th>Year</th>
                <th>Type</th>
                <th>ISBN</th>
                <th>Pages/Issue</th>
                <th>Count</th>
            </tr>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['NAME']) ?></td>
                    <td><?= htmlspecialchars($row['PUBLISHER']) ?></td>
                    <td><?= htmlspecialchars($row['YEAR']) ?></td>
                    <td><?= htmlspecialchars($row['LITERATE']) ?></td>
                    <td><?= htmlspecialchars($row['ISBN']) ?></td>
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