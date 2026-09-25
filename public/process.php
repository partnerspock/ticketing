<?php
// process.php - Handles AJAX requests for booking, deletion, and finishing the day
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
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'DB Connection Failed']));
}

$action = $_POST['action'] ?? null;
$current_date = date('Y-m-d'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($action) {
        
        case 'book':
            // LOGIC MODIFIED: Accept array of seats
            $id_seats = $_POST['id_seats'] ?? []; // Expecting an array
            $name = $_POST['name'] != '' ? $_POST['name'] : null;
            $id_type = $_POST['id_type'] ?? 1;

            if (empty($id_seats) || !is_array($id_seats)) {
                echo json_encode(['success' => false, 'message' => 'No seats selected.']);
                exit;
            }

            try {
                $pdo->beginTransaction();

                // Check for existing active bookings for ALL selected seats
                $placeholders = implode(',', array_fill(0, count($id_seats), '?'));
                $stmt_check = $pdo->prepare("SELECT seat_name FROM seat s JOIN dday d ON s.id_seat = d.id_seat WHERE d.id_seat IN ($placeholders) AND DATE(d.record_timestamp) = CURDATE() AND d.isDone = 0");
                $stmt_check->execute($id_seats);
                $taken_seats = $stmt_check->fetchAll(PDO::FETCH_COLUMN);

                if (!empty($taken_seats)) {
                    $pdo->rollBack();
                    echo json_encode(['success' => false, 'message' => 'Seat(s) ' . implode(', ', $taken_seats) . ' are already taken for today.']);
                    exit();
                }

                // 1. Insert visitor record ONCE
                $stmt_visitor = $pdo->prepare("INSERT INTO visitor (name, id_type) VALUES (?, ?)");
                $stmt_visitor->execute([$name, $id_type]);
                $id_visitor = $pdo->lastInsertId();
                
                $successful_bookings = 0;
                // 2. Insert dday records for EACH seat
                $stmt_dday = $pdo->prepare("INSERT INTO dday (id_seat, id_visitor, record_timestamp, isDone) 
                                           VALUES (?, ?, NOW(), 0)"); 
                
                foreach ($id_seats as $id_seat) {
                    $stmt_dday->execute([$id_seat, $id_visitor]);
                    $successful_bookings++;
                }


                $pdo->commit();
                echo json_encode(['success' => true, 'message' => "$successful_bookings Seats Successfully Choose"]);

            } catch (PDOException $e) {
                $pdo->rollBack();
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Try Again Later: ' . $e->getMessage()]);
            }
            break;
       
        case 'delete':
            // Logic remains the same (delete single active booking)
            // ... (rest of the delete logic) ...
            $id_seat = $_POST['id_seat'] ?? null;
        
            try {
                $pdo->beginTransaction();
                
                // Find visitor ID linked to the current day's active booking
                $stmt_find_visitor = $pdo->prepare("SELECT id_visitor FROM dday WHERE id_seat = ? AND DATE(record_timestamp) = CURDATE() AND isDone = 0");
                $stmt_find_visitor->execute([$id_seat]);
                $id_visitor = $stmt_find_visitor->fetchColumn();

                if ($id_visitor) {
                    // 1. Delete dday record
                    $stmt_delete_dday = $pdo->prepare("DELETE FROM dday WHERE id_seat = ? AND DATE(record_timestamp) = CURDATE() AND isDone = 0");
                    $stmt_delete_dday->execute([$id_seat]);
                    
                    $pdo->commit();
                    echo json_encode(['success' => true, 'message' => 'Delete succed']);
                } else {
                    $pdo->rollBack();
                    echo json_encode(['success' => false, 'message' => 'This seat was not taken.']);
                }

            } catch (PDOException $e) {
                $pdo->rollBack();
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Delete failed: ' . $e->getMessage()]);
            }
            break;
       
        case 'finish_day':
            // Logic remains the same (update isDone=1)
            // ... (rest of the finish_day logic) ...
            $stmt = $pdo->prepare("UPDATE dday 
                                   SET isDone = 1 
                                   WHERE DATE(record_timestamp) = :current_date
                                   AND isDone = 0");

            try {
                $stmt->execute(['current_date' => $current_date]);
                $count = $stmt->rowCount();
                echo json_encode([
                    'success' => true, 
                    'message' => "Successfully marked $count bookings as finished (isDone=1)."
                ]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database update failed.']);
            }
            break;
       
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action specified.']);
            break;
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
}
// End of process.php
