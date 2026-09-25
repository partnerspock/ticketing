<?php
header("Content-Type: application/json");

require "../db.php";

$sql = "
SELECT
    d.id_day,
    d.record_timestamp,
    d.isDone,
    s.seat_name,
    v.name AS visitor_name,
    vt.name_type
FROM dday d
JOIN seat s ON d.id_seat = s.id_seat
JOIN visitor v ON d.id_visitor = v.id_visitor
JOIN visitor_type vt ON v.id_type = vt.id_type
ORDER BY d.record_timestamp DESC
";

$data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "source" => "gkompa",
    "data" => $data
]);
