<?php
// fetch_history.php - Handles AJAX request for fetching history data as a seat map
header('Content-Type: application/json');

// --- DATABASE CONFIGURATION ---
$host = 'localhost';
$db   = 'kompa'; 
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
// ------------------------------

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'DB Connection Failed']);
    exit();
}

$selected_date = $_GET['date'] ?? null;

if (empty($selected_date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $selected_date)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid or missing date parameter.']);
    exit();
}

try {
    // 1. Check if the day was actually finished (at least one isDone=1 record exists for the date)
    $stmt_finished = $pdo->prepare("
        SELECT COUNT(id_day) as count_done 
        FROM dday 
        WHERE DATE(record_timestamp) = :selected_date 
        AND isDone = 1
    ");
    $stmt_finished->execute(['selected_date' => $selected_date]);
    $day_was_finished = $stmt_finished->fetchColumn() > 0;

    if (!$day_was_finished) {
        // If the day wasn't finished, return a specific error message
        echo json_encode(['success' => false, 'message' => 'The data for this date has not been marked as finished.']);
        exit();
    }
    
    // 2. Fetch all seats and LEFT JOIN with the FINISHED bookings for the selected date
    $stmt = $pdo->prepare("
        SELECT
            s.id_seat,
            s.seat_name,
            (d.id_day IS NOT NULL) AS is_taken,
            v.name AS visitor_name,
            vt.name_type AS visitor_type_name,
            DATE_FORMAT(d.record_timestamp, '%H:%i:%s') AS booking_time
        FROM seat s
        LEFT JOIN dday d ON s.id_seat = d.id_seat 
            AND DATE(d.record_timestamp) = :selected_date
            AND d.isDone = 1 -- Only finished (historical) bookings
        LEFT JOIN visitor v ON d.id_visitor = v.id_visitor
        LEFT JOIN visitor_type vt ON v.id_type = vt.id_type
        ORDER BY s.id_seat
    ");

    $stmt->execute(['selected_date' => $selected_date]);
    $history_seats = $stmt->fetchAll();
    
    $seat_map = [];
    $total_bookings = 0;
    foreach ($history_seats as $seat) {
        $seat_map[$seat['id_seat']] = $seat;
        if ($seat['is_taken']) {
            $total_bookings++;
        }
    }

    echo json_encode([
        'success' => true, 
        'data' => $seat_map,
        'total_bookings' => $total_bookings,
        'selected_date' => $selected_date
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    error_log("History Query Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database query failed.']);
}
?>