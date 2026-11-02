<?php
// index.php
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
    die("Database connection failed: " . $e->getMessage());
}

// =============================================================
// >> DATA FETCHING LOGIC (UPDATED FOR isDone = 0) <<
// =============================================================

$current_date = date('Y-m-d');
$total_undone_bookings = 0;

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
        AND DATE(d.record_timestamp) = :current_date
        AND d.isDone = 0 
    LEFT JOIN visitor v ON d.id_visitor = v.id_visitor
    LEFT JOIN visitor_type vt ON v.id_type = vt.id_type
    ORDER BY s.id_seat
");

$stmt->execute(['current_date' => $current_date]);
$seats = $stmt->fetchAll();

$seat_map = [];
foreach ($seats as $seat) {
    $seat_map[$seat['id_seat']] = $seat;
    if ($seat['is_taken']) {
        $total_undone_bookings++;
    }
}

$stmt_types = $pdo->query("SELECT id_type, name_type FROM visitor_type");
$visitor_types = $stmt_types->fetchAll();

function generate_seat_section($start_id, $end_id, $seat_map) {
    $output = '';
    for ($id = $start_id; $id <= $end_id; $id++) {
        if (!isset($seat_map[$id])) continue;
       
        $seat = $seat_map[$id];
        $is_taken = $seat['is_taken'];
        $class = $is_taken ? 'seat-taken' : 'seat-available';

        $output .= '<button 
                        class="seat-button ' . $class . '" 
                        data-id-seat="' . $seat['id_seat'] . '"
                        data-is-taken="' . $is_taken . '"
                        data-seat-name="' . htmlspecialchars($seat['seat_name']) . '"
                        data-visitor-name="' . htmlspecialchars($seat['visitor_name'] ?? '') . '"
                        data-visitor-type="' . htmlspecialchars($seat['visitor_type_name'] ?? '') . '"
                        data-booking-time="' . htmlspecialchars($seat['booking_time'] ?? '') . '"
                    >' . htmlspecialchars($seat['seat_name']) . '</button>';
    }
    return $output;
}

$row_starts = [1, 17, 33, 49, 65, 81, 97, 113, 129, 145];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GKI - Kompa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/kompastyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="area-box">
        <div class="button-group">
            <img src="img/gunsa.png" alt="Logo 1" class="logo">
            <span class="blinking"><h1 class="header-title">00.00.00 - See the Light</h1></span>
            <img src="img/kompa.jpg" alt="Logo 2" class="logo kompa-logo">
        </div>

        <a href="cinema_listings.php" style="text-decoration:none; width:100%;">
            <div id="screen-display">Mimbar</div>
        </a>

        <div class="legend-bar">
            <div class="legend-item">
                <div class="legend-box available-box"></div>
                <span class="seat-info">Seat is Available</span>
            </div>
            <?php if ($total_undone_bookings > 0): ?>
            <div class="legend-item">
                <button id="finishDayBtn" type="button" class="seat-info action-badge">
                    <span>(<?php echo $total_undone_bookings; ?> Seats Taken Today)</span>
                </button>
            </div>
            <?php else: ?>
            <div class="legend-item">
                <button type="button" id="stats" class="seat-info action-badge">
                    <span>Happy Sunday</span>
                </button>
            </div>
            <?php endif; ?>
            <div class="legend-item">
                <div class="legend-box taken-box"></div>
                <span class="seat-info">Seat Has Been taken</span>
            </div>
        </div>

        <div class="seat-container"> 
            <!-- Kolom Kiri (8 Kolom per baris) -->
            <div class="seat-section">
                <?php 
                    foreach ($row_starts as $start_id) {
                        echo generate_seat_section($start_id, $start_id + 7, $seat_map); 
                    }
                ?>
            </div>

            <!-- Kolom Kanan (8 Kolom per baris) -->
            <div class="seat-section">
                <?php 
                    foreach ($row_starts as $start_id) {
                        echo generate_seat_section($start_id + 8, $start_id + 15, $seat_map); 
                    }
                ?>
            </div>
        </div>

        <div class="action-buttons-bar">
            <button id="bookSelectedBtn" class="custom-btn btn-primary" disabled>
                Take Selected Seats - <span id="selected-count">0</span>
            </button>
            <button id="clearSelection" type="button" class="custom-btn btn-danger clear-button" disabled>
                Clear Selection
            </button>
        </div>
    </div> 

    <div id="finishDayModal" class="modal">
        <div class="modal-content">
            <h3>Confirm Day Completion</h3>
            <p>Hari ini ada <?php echo $total_undone_bookings; ?> yang hadir, Yakin mau diselesaikan?</p>
            <div class="modal-actions">
                <button id="confirmFinish" class="custom-btn"><i class="fa-solid fa-check"></i> Done</button>
                <button type="button" id="stats" class="custom-btn"><i class="fa-solid fa-chart-simple"></i> History</button>
                <button type="button" id="cancelFinish" class="custom-btn btn-danger">Not Yet</button>
            </div>
        </div>
    </div>

    <div id="seatModal" class="modal">
        <div class="modal-content">
            <h2 id="modal-title"></h2>
           
            <form id="bookingForm" autocomplete="off" style="display: none;">
                <div id="selectedSeatsDisplay"></div>
                <input type="hidden" id="modal_id_seats" name="id_seats[]">                
                <label for="name">Name</label>
                <input type="text" id="name" name="name" placeholder="e.g. John Doe (Optional)" style="margin-top: 10px;">
                <div class="visitor-types-row">
                    <?php foreach ($visitor_types as $type): ?>
                    <div class="form-check-custom">
                        <input type="radio" id="type_<?php echo $type['id_type']; ?>" name="id_type" value="<?php echo $type['id_type']; ?>" class="custom-radio">
                        <label for="type_<?php echo $type['id_type']; ?>"><?php echo htmlspecialchars($type['name_type']); ?></label>
                    </div>
                    <?php endforeach; ?>
                </div>
               
                <div class="modal-actions">
                    <button type="submit" id="Button" class="custom-btn">Confirm Seats</button>
                    <button type="button" id="closeModalIcon" title="Close" class="custom-btn btn-danger clear-button">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </form>
           
            <div id="deleteConfirmation" style="display: none;">
                <input type="hidden" id="delete_id_seat"> 
                <div id="booking-details-display"></div>
                <p>Sudah ada yang pilih, Yakin mau dihapus?</p>
                <div class="modal-actions">
                    <button id="deleteButton" class="custom-btn btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                    <button type="button" id="closeModalIconDelete" title="Close" class="custom-btn btn-success">Cancel</button>
                </div>
            </div>
           
            <div id="message" style="margin-top: 10px;"></div>
        </div>
    </div>

<script>
let selectedSeatIDs = [];

function updateDateTime() {
    const now = new Date();
    const formattedDate = now.toLocaleString('en-US', { 
        weekday: 'short', year: 'numeric', month: 'short', day: 'numeric', 
        hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false, timeZoneName: 'short'
    });
    $('#datetime-display').text(formattedDate);
}

function updateSelectedSeatsUI() {
    $('#selected-count').text(selectedSeatIDs.length);
    $('#bookSelectedBtn').prop('disabled', selectedSeatIDs.length === 0);
}

$(document).ready(function() {
    updateDateTime();
    setInterval(updateDateTime, 1000); 

    function updateSelectedSeatsUI() {
        const count = selectedSeatIDs.length;
        if (count === 0) {
            $('#bookSelectedBtn').prop('disabled', true).text('Choose Your Seats');
            $('#clearSelection').prop('disabled', true);
        } else {
            $('#bookSelectedBtn').prop('disabled', false).text('Take ' + count + ' Selected Seats');
            $('#clearSelection').prop('disabled', false);
        }
    }

    $('#clearSelection').on('click', function() {
        $('.seat-button.seat-selected').removeClass('seat-selected');
        selectedSeatIDs = [];
        updateSelectedSeatsUI();
    });

    $(".seat-button").on("click", function() {
        const $this = $(this);
        const isTaken = $this.data('is-taken');
        const idSeat = $this.data('id-seat').toString();
        const seatName = $this.data('seat-name');

        $('#message').text('').css('color', '#ef4444'); 
       
        if (isTaken == 1) { 
            const visitorName = $this.data('visitor-name');
            const visitorType = $this.data('visitor-type');
            const bookingTime = $this.data('booking-time');
           
            const detailsHtml = `
                <p><strong>Nama nya:</strong> ${visitorName}</p>
                <p><strong>Tipe nya:</strong> ${visitorType}</p>
                <p><strong>Waktu datang nya:</strong> ${bookingTime}</p>
            `;
           
            $('#booking-details-display').html(detailsHtml);
            $('#modal-title').text('Seat ' + seatName);
            $('#delete_id_seat').val(idSeat);
            $('#bookingForm').hide();
            $('#deleteConfirmation').show();
            $('#seatModal').fadeIn(200);
        } else {
            $this.toggleClass('seat-selected');
            const index = selectedSeatIDs.indexOf(idSeat);
            if (index > -1) {
                selectedSeatIDs.splice(index, 1);
            } else {
                selectedSeatIDs.push(idSeat);
            }
            updateSelectedSeatsUI();
        }
    });

    $('#bookSelectedBtn').on('click', function() {
        if (selectedSeatIDs.length === 0) return;
        const seatNames = selectedSeatIDs.map(id => $(`[data-id-seat="${id}"]`).data('seat-name'));
        
        $('#modal-title').text('Take ' + selectedSeatIDs.length + ' Seat(s)'); 
        $('#selectedSeatsDisplay').html('<p>Selected Seats: <strong>' + seatNames.join(', ') + '</strong></p>');
        
        $('#bookingForm input[name="id_seats[]"]').remove();
        selectedSeatIDs.forEach(id => {
            $('<input>').attr({ type: 'hidden', name: 'id_seats[]', value: id }).appendTo('#bookingForm');
        });

        $('#bookingForm').show();
        $('#deleteConfirmation').hide();
        $('#seatModal').fadeIn(200);

        $('#name').val('');
        $('#type_1').prop('checked', true);
    });

    $('#bookingForm').on('submit', function(e) {
        e.preventDefault();
        if (selectedSeatIDs.length === 0) return;

        $.ajax({
            url: 'process.php', 
            type: 'POST',
            data: $(this).serialize() + '&action=book',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#message').text(response.message).css('color', '#10b981'); 
                    window.location.reload();
                } else {
                    $('#message').text('Error: ' + response.message).css('color', '#ef4444');
                }
            },
            error: function() {
                $('#message').text('Terjadi kesalahan saat pemesanan.').css('color', '#ef4444'); 
            }
        });
    });
   
    $('#deleteButton').on('click', function() {
        const idSeat = $('#delete_id_seat').val();
        $.ajax({
            url: 'process.php',
            type: 'POST',
            data: { action: 'delete', id_seat: idSeat },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#message').text('Berhasil dihapus').css('color', '#10b981'); 
                    window.location.reload(); 
                } else {
                    $('#message').text('Error: ' + response.message).css('color', '#ef4444');
                }
            },
            error: function() {
                $('#message').text('Error delete this seat.').css('color', '#ef4444'); 
            }
        });
    });

    const $finishDayModal = $('#finishDayModal');
    const $finishDayBtn = $('#finishDayBtn');
    const $confirmFinishBtn = $('#confirmFinish');

    $finishDayBtn.on('click', function() {
        if (!$(this).prop('disabled')) $finishDayModal.fadeIn(200);
    });

    $('#cancelFinish').on('click', function() {
        $finishDayModal.fadeOut(200);     
    });

    $confirmFinishBtn.on('click', function() {
        $finishDayModal.fadeOut(200);
        $.ajax({
            url: 'process.php',
            type: 'POST',
            data: { action: 'finish_day' },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('🎉 Happy Sunday You All');
                    window.location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    });

    $('#stats').on('click', function() {
        window.location.href = 'history.php';
    });

    $('#closeModalIcon, #closeModalIconDelete, #seatModal').on('click', function(event) {
        if ($(event.target).is('#closeModalIcon') || $(event.target).is('#seatModal') || $(event.target).is('#closeModalIconDelete')) {
            if ($('#bookingForm').is(':visible')) {
                $('.seat-button.seat-selected').removeClass('seat-selected');
                selectedSeatIDs = [];
                updateSelectedSeatsUI();
            }
            $('#seatModal').fadeOut(200);
        }
    });

    $(window).on('click', function(event) {
        if ($(event.target).is('#finishDayModal')) {
            $('#finishDayModal').fadeOut(200);
        }
    });
});
</script>
</body>
</html>