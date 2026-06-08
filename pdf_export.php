<?php
require_once 'db.php';
require_once __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$type = $_GET['type'] ?? '';
$title = '';
$subtitle = '';
$headers = [];
$rows = [];

function clean($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

try {
    if ($type === 'publisher') {
        $publisher = $_GET['publisher'] ?? '';

        $sql = "SELECT NAME, ISBN, PUBLISHER, YEAR, NUMBER, QUANTITY
                FROM literature
                WHERE PUBLISHER = :publisher AND LITERATE = 'Book'
                ORDER BY NAME";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':publisher', $publisher, PDO::PARAM_STR);
        $stmt->execute();

        $title = 'Результати пошуку за видавництвом';
        $subtitle = 'Видавництво: ' . $publisher;
        $headers = ['Name', 'ISBN', 'Publisher', 'Year', 'Pages', 'Count'];
        $rows = $stmt->fetchAll();
    } elseif ($type === 'period') {
        $yearFrom = $_GET['year_from'] ?? 0;
        $yearTo = $_GET['year_to'] ?? 9999;

        $sql = "SELECT NAME, PUBLISHER, YEAR, LITERATE, ISBN, NUMBER, QUANTITY
                FROM literature
                WHERE YEAR BETWEEN :year_from AND :year_to
                  AND LITERATE IN ('Book', 'Journal', 'Newspaper')
                ORDER BY YEAR, NAME";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':year_from', $yearFrom, PDO::PARAM_INT);
        $stmt->bindValue(':year_to', $yearTo, PDO::PARAM_INT);
        $stmt->execute();

        $title = 'Результати пошуку за часовим періодом';
        $subtitle = 'Період: ' . $yearFrom . ' - ' . $yearTo;
        $headers = ['Name', 'Publisher', 'Year', 'Type', 'ISBN', 'Pages/Issue', 'Count'];
        $rows = $stmt->fetchAll();
    } elseif ($type === 'author') {
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

        $title = 'Результати пошуку за автором';
        $subtitle = 'Автор: ' . $authorName;
        $headers = ['Name', 'Publisher', 'Year', 'ISBN', 'Pages', 'Count'];
    } else {
        die('Невідомий тип експорту.');
    }
} catch (PDOException $e) {
    die('Помилка запиту: ' . $e->getMessage());
}

$html = '
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
        }
        h1 {
            text-align: center;
            font-size: 20px;
            margin-bottom: 8px;
        }
        p {
            font-size: 13px;
            margin-bottom: 18px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #eeeeee;
            font-weight: bold;
        }
        th, td {
            border: 1px solid #777;
            padding: 6px;
            text-align: center;
            vertical-align: middle;
        }
        .empty {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <h1>' . clean($title) . '</h1>
    <p><b>' . clean($subtitle) . '</b></p>';

if ($rows) {
    $html .= '<table><tr>';
    foreach ($headers as $header) {
        $html .= '<th>' . clean($header) . '</th>';
    }
    $html .= '</tr>';

    foreach ($rows as $row) {
        $html .= '<tr>';

        if ($type === 'publisher') {
            $html .= '<td>' . clean($row['NAME']) . '</td>';
            $html .= '<td>' . clean($row['ISBN']) . '</td>';
            $html .= '<td>' . clean($row['PUBLISHER']) . '</td>';
            $html .= '<td>' . clean($row['YEAR']) . '</td>';
            $html .= '<td>' . clean($row['NUMBER']) . '</td>';
            $html .= '<td>' . clean($row['QUANTITY']) . '</td>';
        } elseif ($type === 'period') {
            $html .= '<td>' . clean($row['NAME']) . '</td>';
            $html .= '<td>' . clean($row['PUBLISHER']) . '</td>';
            $html .= '<td>' . clean($row['YEAR']) . '</td>';
            $html .= '<td>' . clean($row['LITERATE']) . '</td>';
            $html .= '<td>' . clean($row['ISBN']) . '</td>';
            $html .= '<td>' . clean($row['NUMBER']) . '</td>';
            $html .= '<td>' . clean($row['QUANTITY']) . '</td>';
        } elseif ($type === 'author') {
            $html .= '<td>' . clean($row['NAME']) . '</td>';
            $html .= '<td>' . clean($row['PUBLISHER']) . '</td>';
            $html .= '<td>' . clean($row['YEAR']) . '</td>';
            $html .= '<td>' . clean($row['ISBN']) . '</td>';
            $html .= '<td>' . clean($row['NUMBER']) . '</td>';
            $html .= '<td>' . clean($row['QUANTITY']) . '</td>';
        }

        $html .= '</tr>';
    }

    $html .= '</table>';
} else {
    $html .= '<div class="empty">Нічого не знайдено.</div>';
}

$html .= '</body></html>';

$options = new Options();
$options->set('defaultFont', 'DejaVu Sans');
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

$fileName = 'library_export_' . $type . '.pdf';
$dompdf->stream($fileName, ['Attachment' => true]);
exit;
?>
