<?php
// Include database configuration
header('Content-Type: text/html; charset=utf-8');
require_once 'config.php';

// Predefined time slots with Sinhala names
$timeSlotMap = [
    1 => "පළමු යාමය 06:00PM - 08:00PM",
    2 => "දෙවන යාමය 08:00PM - 10:00PM",
    3 => "තෙවන යාමය 10:00PM - 12:00AM",
    4 => "සිව්වන යාමය 12:00AM - 02:00AM",
    5 => "පස්වන යාමය 02:00AM - 04:00AM",
    6 => "හයවන යාමය 04:00AM - 06:00AM"
];

// Get search query
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fetch booked seats with details and search functionality
try {
    $query = "
        SELECT 
            b.id,
            b.name, 
            b.status, 
            b.experience,
            GROUP_CONCAT(DISTINCT bs.slot_id ORDER BY bs.slot_id SEPARATOR ', ') as time_periods
        FROM 
            bookings b
        JOIN 
            booking_slots bs ON b.id = bs.booking_id
    ";
    // Add search condition if search term is provided
    if (!empty($search)) {
        $query .= " WHERE b.name LIKE :search";
        $search = "%$search%";
    }
    
    $query .= "
        GROUP BY 
            b.id, b.name, b.status, b.experience
        ORDER BY 
            b.id DESC
    ";
    
    $stmt = $pdo->prepare($query);
    
    if (!empty($search)) {
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
    }
    
    $stmt->execute();
    $bookings = $stmt->fetchAll();
} catch (Exception $e) {
    die("Error fetching bookings: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Meditation Retreat Bookings</title>
    <style>
        @font-face {
            font-family: 'Noto Sans Sinhala';
            src: url('https://fonts.googleapis.com/css2?family=Noto+Sans+Sinhala&display=swap');
        }

        /* General Styles */
        body {
            font-family: 'Noto Sans Sinhala', 'Poppins', sans-serif;
            background: linear-gradient(120deg, #89f7fe, #66a6ff);
            color: #333;
            margin: 0;
            padding: 20px;
        }
            
        /* Previous styles remain the same until table styles */
        
        /* Updated Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        /* Column-specific styles */
        td:nth-child(1), th:nth-child(1) { /* Booking ID column */
            background-color: rgba(255, 245, 245, 0.5);
        }

        td:nth-child(2), th:nth-child(2) { /* Name column */
            background-color: rgba(245, 255, 245, 0.5);
        }

        td:nth-child(3), th:nth-child(3) { /* Status column */
            background-color: rgba(245, 245, 255, 0.5);
        }

        td:nth-child(4), th:nth-child(4) { /* Experience Level column */
            background-color: rgba(255, 245, 255, 0.5);
        }

        td:nth-child(5), th:nth-child(5) { /* Time Periods column */
            background-color: rgba(245, 255, 255, 0.5);
        }

        /* Header styles */
        th {
            background: linear-gradient(120deg, #ff9a9e, #fad0c4);
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }

        tr:hover td {
            background-color: rgba(241, 250, 255, 0.8);
        }
        h1 {
            text-align: center;
            font-size: 2.5rem;
            color: #ffffff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            margin-bottom: 30px;
        }

        /* Search Styles */
        .search-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .search-container input[type="text"] {
            padding: 10px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            width: 300px;
            transition: border-color 0.3s ease;
        }

        .search-container input[type="text"]:focus {
            border-color: #66a6ff;
            outline: none;
        }

        .search-container button {
            padding: 10px 20px;
            background: linear-gradient(120deg, #66a6ff, #89f7fe);
            border: none;
            border-radius: 8px;
            color: white;
            cursor: pointer;
            font-size: 1rem;
            transition: transform 0.2s ease;
        }

        .search-container button:hover {
            transform: translateY(-2px);
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: linear-gradient(120deg, #ff9a9e, #fad0c4);
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1faff;
        }

        /* Status Styles */
        .status-bikkhu {
            color: #9c27b0;
            font-weight: bold;
        }

        .status-nun {
            color: #e91e63;
            font-weight: bold;
        }

        .status-layperson {
            color: #3f51b5;
            font-weight: bold;
        }

        /* Experience Styles */
        .experience-beginner {
            color: #4caf50;
        }

        .experience-experienced {
            color: #2196f3;
        }

        /* Time Periods Style */
        .time-periods {
            white-space: pre-line;
            line-height: 1.5;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            h1 {
                font-size: 2rem;
            }

            .search-container {
                flex-direction: column;
                align-items: center;
            }

            .search-container input[type="text"] {
                width: 100%;
                max-width: 300px;
            }

            th, td {
                font-size: 14px;
                padding: 8px;
            }
        }
/* Container styles */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Language selector styles */
.language-selector {
    margin-bottom: 20px;
    text-align: right;
}

.language-btn {
    display: inline-block;
    padding: 8px 16px;
    margin-left: 10px;
    background-color: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-decoration: none;
    color: #333;
}

.language-btn.active {
    background-color: #4CAF50;
    color: white;
    border-color: #4CAF50;
}

/* Table styles */
.booking-list {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background-color: white;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.booking-list th,
.booking-list td {
    border: 1px solid #ddd;
    padding: 12px;
    text-align: left;
}

.booking-list th {
    background-color: #f5f5f5;
    font-weight: bold;
}

.booking-list tr:nth-child(even) {
    background-color: #f9f9f9;
}

.booking-list tr:hover {
    background-color: #f5f5f5;
}

/* Edit button styles */
.edit-btn {
    display: inline-block;
    background-color: #4CAF50;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    text-align: center;
}

.edit-btn:hover {
    background-color: #45a049;
}

/* Responsive styles */
@media screen and (max-width: 1024px) {
    .container {
        padding: 15px;
    }

    .booking-list th,
    .booking-list td {
        padding: 10px;
    }
}

@media screen and (max-width: 768px) {
    .booking-list {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }

    .booking-list th,
    .booking-list td {
        font-size: 14px;
        padding: 8px;
    }

    .edit-btn {
        padding: 6px 12px;
        font-size: 14px;
    }
}

@media screen and (max-width: 480px) {
    .container {
        padding: 10px;
    }

    h1 {
        font-size: 24px;
    }

    .language-btn {
        padding: 6px 12px;
        font-size: 14px;
    }

    .booking-list th,
    .booking-list td {
        padding: 6px;
        font-size: 13px;
    }

    .edit-btn {
        padding: 4px 10px;
        font-size: 13px;
    }
}
    </style>
</head>
<body>
    <h1>Meditation Retreat Bookings</h1>
    
    <!-- Search form -->
    <form method="GET" class="search-container">
        <input type="text" name="search" placeholder="Search by name..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
        <?php if (!empty($search)): ?>
            <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" style="padding: 10px 20px; text-decoration: none; color: #666;">Clear</a>
        <?php endif; ?>
    </form>

    <table>
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Name</th>
                <th>Status</th>
                <th>Experience Level</th>
                <th>Time Periods</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking): 
                // Convert slot IDs to actual time periods and join with line breaks
                $timePeriods = array_map(function($slotId) use ($timeSlotMap) {
                    return isset($timeSlotMap[$slotId]) ? $timeSlotMap[$slotId] : $slotId;
                }, explode(', ', $booking['time_periods']));
                $formattedTimePeriods = implode("\n", $timePeriods);
            ?>
            <tr>
                <td><?php echo htmlspecialchars($booking['id']); ?></td>
                <td><?php echo htmlspecialchars($booking['name']); ?></td>
                <td class="status-<?php echo strtolower(htmlspecialchars($booking['status'])); ?>">
                    <?php echo htmlspecialchars($booking['status']); ?>
                </td>
                <td class="experience-<?php echo strtolower(htmlspecialchars($booking['experience'])); ?>">
                    <?php echo htmlspecialchars($booking['experience']); ?>
                </td>
                <td class="time-periods"><?php echo nl2br(htmlspecialchars($formattedTimePeriods)); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (empty($bookings)): ?>
        <p>No bookings found.</p>
    <?php endif; ?>
</body>
</html>
