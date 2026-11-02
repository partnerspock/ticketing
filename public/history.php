<?php
// history.php
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
    die("Database connection failed: " . $e->getMessage());
}

// Seat row starting points
$row_starts = [1, 17, 33, 49, 65, 81, 97, 113, 129, 145];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>History - GKI Kompa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/kompastyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* --- History-specific overrides & Fix No Vertical Scroll --- */
        body {
            height: 100vh;
            max-height: 100vh;
            overflow: hidden; /* Mencegah scroll vertikal di halaman history */
        }

        .history-page {
            width: 100%;
            max-width: 1100px;
            max-height: 96vh;
            background-color: rgba(42, 40, 38, 0.1); /* Transparan 90% */
            backdrop-filter: blur(2px); /* Efek blur kaca */
            -webkit-backdrop-filter: blur(2px);
            border-radius: 1.5rem;
            border: 1px solid var(--border-warm);
            padding: 1.5rem 2rem;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }

        .history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border-warm);
            flex-shrink: 0;
        }

        .history-header h2 {
            margin: 0;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .history-controls {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .history-controls input[type="date"] {
            padding: 0.55rem 0.8rem;
            background-color: #24201c;
            border: 1px solid var(--border-warm);
            border-radius: 0.65rem;
            color: var(--text-main);
            font-size: 0.85rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .history-controls input[type="date"]:focus {
            border-color: var(--accent-blue);
        }

        /* Stats bar */
        .stats-bar {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            align-items: center;
            font-size: 0.85rem;
            color: var(--text-muted);
            flex-shrink: 0;
        }

        .stat-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: #24201c;
            border: 1px solid var(--border-warm);
            border-radius: 9999px;
            padding: 0.3rem 0.8rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .stat-chip .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .dot-available { background: #fff; border: 1px solid var(--border-warm); }
        .dot-taken     { background: var(--accent-blue); }

        /* Result area */
        #history-results {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            overflow-y: auto;
            flex-grow: 1;
            padding: 0.25rem;
        }

        #history-results .seat-container {
            margin-top: 0;
        }

        /* Tooltip */
        .seat-button[data-tip] {
            position: relative;
        }
        .seat-button[data-tip]:hover::after {
            content: attr(data-tip);
            position: absolute;
            bottom: calc(100% + 6px);
            left: 50%;
            transform: translateX(-50%);
            background: #1e1a16;
            color: var(--text-main);
            border: 1px solid var(--border-warm);
            border-radius: 0.5rem;
            padding: 0.4rem 0.65rem;
            font-size: 0.72rem;
            white-space: nowrap;
            z-index: 999;
            pointer-events: none;
        }

        /* Placeholder message */
        .history-placeholder {
            text-align: center;
            padding: 2.5rem 1rem;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .history-placeholder i {
            display: block;
            font-size: 2.2rem;
            margin-bottom: 0.75rem;
            opacity: 0.4;
        }

        .alert-info-custom {
            background: rgba(34,197,94,0.08);
            border: 1px solid rgba(34,197,94,0.25);
            border-radius: 0.75rem;
            padding: 0.85rem 1.1rem;
            color: var(--accent-blue);
            font-size: 0.85rem;
        }

        .alert-error-custom {
            background: rgba(244,63,94,0.08);
            border: 1px solid rgba(244,63,94,0.25);
            border-radius: 0.75rem;
            padding: 0.85rem 1.1rem;
            color: var(--accent-red);
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .history-header { flex-direction: column; align-items: stretch; }
            .history-controls { flex-direction: column; }
            .history-controls input[type="date"],
            .history-controls button { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="history-page">

        <!-- HEADER -->
        <div class="history-header">
            <div class="history-controls">
                <a href="index.php" class="custom-btn btn-primary" style="padding: 0.55rem 1rem; font-size: 0.85rem;">
                    <i class="fa-solid fa-arrow-left"></i> Back to Seat Map
                </a>
            </div>
            <div>
                <h2 style="color:var(--accent-blue);"><i class="fa-solid fa-clock-rotate-left" style="color:var(--accent-blue);margin-right:0.4rem;"></i>Attendance History</h2>
            </div>
            <div class="history-controls">
                <input type="date" id="select_date" value="<?php echo date('Y-m-d'); ?>">
                <button id="viewHistoryBtn" class="custom-btn btn-primary" style="padding: 0.55rem 1rem; font-size: 0.85rem;">
                    <i class="fa-solid fa-magnifying-glass"></i> View
                </button>
            </div>
        </div>

        <!-- RESULTS -->
        <div id="history-results">
            <div class="history-placeholder">
                <i class="fa-regular fa-calendar-days"></i>
                Select a date and click <strong>View</strong> to see attendance history.
            </div>
        </div>

    </div>

<script>
$(document).ready(function() {

    const generateSeatHtml = (seat) => {
        const isTaken = seat.is_taken == 1;
        const className = isTaken ? 'seat-taken' : 'seat-available';
        const seatName = seat.seat_name;
        let tipAttr = '';
        if (isTaken) {
            const name = seat.visitor_name || 'N/A';
            const type = seat.visitor_type_name || 'N/A';
            const time = seat.booking_time || 'N/A';
            tipAttr = `data-tip="${name} · ${type} · ${time}"`;
        }
        return `<button class="seat-button ${className} seat-history" style="pointer-events:${isTaken?'auto':'none'};cursor:${isTaken?'default':'default'};" ${tipAttr}>${seatName}</button>`;
    };

    $('#viewHistoryBtn').on('click', function() {
        const selectedDate = $('#select_date').val();
        const resultsDiv = $('#history-results');

        resultsDiv.html('<div class="history-placeholder"><i class="fa-solid fa-spinner fa-spin"></i>Loading history...</div>');

        if (!selectedDate) {
            resultsDiv.html('<div class="alert-error-custom"><i class="fa-solid fa-circle-exclamation"></i> Please select a date.</div>');
            return;
        }

        $.ajax({
            url: 'fetch_history.php',
            type: 'GET',
            data: { date: selectedDate },
            dataType: 'json',
            success: function(response) {
                if (!response.success) {
                    if (response.message.includes("not been marked as finished")) {
                        resultsDiv.html('<div class="alert-info-custom"><i class="fa-solid fa-circle-info" style="margin-right:0.5rem;"></i>No finished session found for this date. Either the day hasn\'t ended yet, or no data exists.</div>');
                    } else {
                        resultsDiv.html(`<div class="alert-error-custom"><i class="fa-solid fa-circle-exclamation" style="margin-right:0.5rem;"></i>${response.message}</div>`);
                    }
                    return;
                }

                const seatMap = response.data;
                const rowStarts = <?php echo json_encode($row_starts); ?>;
                let col1 = '', col2 = '';

                rowStarts.forEach(startId => {
                    for (let id = startId; id <= startId + 7; id++) {
                        if (seatMap[id]) col1 += generateSeatHtml(seatMap[id]);
                    }
                });

                rowStarts.forEach(startId => {
                    for (let id = startId + 8; id <= startId + 15; id++) {
                        if (seatMap[id]) col2 += generateSeatHtml(seatMap[id]);
                    }
                });

                const html = `
                    <div id="screen-display" style="pointer-events:none;cursor:default;">Mimbar</div>
                    <div class="stats-bar">
                        <span class="stat-chip">
                            <span class="dot dot-taken"></span>
                            Seats Taken: <strong>${response.total_bookings}</strong>
                        </span>
                        <span class="stat-chip">
                            <span class="dot dot-available"></span>
                            Available
                        </span>
                        <span class="stat-chip" style="margin-left:auto;">
                            <i class="fa-regular fa-calendar" style="color:var(--accent-blue);"></i>
                            ${response.selected_date}
                        </span>
                    </div>
                    <div class="seat-container">
                        <div class="seat-section">${col1}</div>
                        <div class="seat-section">${col2}</div>
                    </div>
                `;
                resultsDiv.html(html);
            },
            error: function() {
                resultsDiv.html('<div class="alert-error-custom"><i class="fa-solid fa-triangle-exclamation" style="margin-right:0.5rem;"></i>Error fetching data from server.</div>');
            }
        });
    });
});
</script>
</body>
</html>