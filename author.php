<?php
require_once 'db.php';

$authorId = $_GET['author_id'] ?? 0;

$sql = "SELECT a.NAME AS AUTHOR_NAME,
               l.NAME,
               l.PUBLISHER,
               l.YEAR,
               l.ISBN,
               l.NUMBER,
               l.QUANTITY
        FROM author a
        INNER JOIN book_authrs ba ON a.Id = ba.FID_AUTH
        INNER JOIN literature l ON l.Id = ba.FID_BOOK
        WHERE a.Id = :author_id
          AND l.LITERATE = 'Book'
        ORDER BY l.NAME";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':author_id', $authorId, PDO::PARAM_INT);
$stmt->execute();

$rows = $stmt->fetchAll();

$authorName = $rows ? $rows[0]['AUTHOR_NAME'] : 'Невідомий автор';
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Книги автора</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Результати пошуку за автором</h1>
    <p><b>Автор:</b> <?= htmlspecialchars($authorName) ?></p>

    <?php if ($rows): ?>
        <table>
            <tr>
                <th>Name</th>
                <th>Publisher</th>
                <th>Year</th>
                <th>ISBN</th>
                <th>Pages</th>
                <th>Count</th>
            </tr>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['NAME']) ?></td>
                    <td><?= htmlspecialchars($row['PUBLISHER']) ?></td>
                    <td><?= htmlspecialchars($row['YEAR']) ?></td>
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