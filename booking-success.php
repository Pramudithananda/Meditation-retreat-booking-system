<?php
// Include database configuration
require_once 'config.php';

// Get booking ID from URL parameter
$bookingId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch booking details
try {
    $query = "
        SELECT 
            b.id,
            b.name,
            b.age,
            b.status,
            b.experience,
            b.address,
            b.telephone,
            GROUP_CONCAT(bs.slot_id ORDER BY bs.slot_id) as time_slots
        FROM 
            bookings b
        LEFT JOIN 
            booking_slots bs ON b.id = bs.booking_id
        WHERE 
            b.id = :bookingId
        GROUP BY 
            b.id
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':bookingId', $bookingId, PDO::PARAM_INT);
    $stmt->execute();
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    // Time slot mapping
    $timeSlotMap = [
        1 => "06:00PM - 08:00PM",
        2 => "08:00PM - 10:00PM",
        3 => "10:00PM - 12:00AM",
        4 => "12:00AM - 02:00AM",
        5 => "02:00AM - 04:00AM",
        6 => "04:00AM - 06:00AM"
    ];

    // Convert slot IDs to readable time slots
    $timeSlots = [];
    if ($booking['time_slots']) {
        foreach (explode(',', $booking['time_slots']) as $slotId) {
            if (isset($timeSlotMap[$slotId])) {
                $timeSlots[] = $timeSlotMap[$slotId];
            }
        }
    }
    $booking['formatted_time_slots'] = $timeSlots;

} catch (PDOException $e) {
    die("Error fetching booking details: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Successful</title>
    <style>
        /* Your existing CSS styles from booking-success.html */
        @font-face {
            font-family: 'Sinhala';
            src: url('sinhala.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: Arial, 'Sinhala', sans-serif;
            margin: 0;
            padding: 2rem;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f5f5f5;
        }
        
        .success-container {
            text-align: center;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 90%;
        }
        
        .success-icon {
            color: #4CAF50;
            font-size: 48px;
            margin-bottom: 1rem;
        }

        .details-container {
            text-align: left;
            margin: 2rem 0;
            padding: 1.5rem;
            background: #f9f9f9;
            border-radius: 4px;
        }

        .detail-row {
            margin: 0.8rem 0;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 0.8rem;
        }

        .detail-label {
            font-weight: bold;
            color: #555;
            flex: 1;
            min-width: 120px;
        }

        .detail-value {
            color: #333;
            flex: 2;
        }

        .time-slots {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .time-slot {
            background: #e8f5e9;
            padding: 0.3rem 0.6rem;
            border-radius: 3px;
            display: inline-block;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
            margin: 0.5rem;
            border: none;
            cursor: pointer;
        }

        .btn.print {
            background-color: #2196F3;
        }

        .btn:hover {
            opacity: 0.9;
        }

        @media print {
            .btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">&#10003;</div>
        <h1>Booking Successful!</h1>
        <div class="multilingual">
            <p>Your meditation retreat booking has been confirmed.</p>
            <p>&#3476;&#3510;&#3484;&#3546; &#3511;&#3535;&#3520;&#3505;&#3535; &#3520;&#3536;&#3497;&#3523;&#3495;&#3524;&#3505;&#3530; &#3520;&#3545;&#3505;&#3530;&#3482;&#3538;&#3515;&#3539;&#3512; &#3501;&#3524;&#3520;&#3540;&#3515;&#3540; &#3482;&#3515; &#3463;&#3501;.</p>
        </div>

        <?php if ($booking): ?>
        <div class="details-container">
            <h2>Booking Details</h2>
            <div class="detail-row">
                <span class="detail-label">Name:</span>
                <span class="detail-value"><?php echo htmlspecialchars($booking['name']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Age:</span>
                <span class="detail-value"><?php echo htmlspecialchars($booking['age']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Experience:</span>
                <span class="detail-value"><?php echo htmlspecialchars($booking['experience']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value"><?php echo htmlspecialchars($booking['status']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Address:</span>
                <span class="detail-value"><?php echo htmlspecialchars($booking['address']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Telephone:</span>
                <span class="detail-value"><?php echo htmlspecialchars($booking['telephone']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Time Slots:</span>
                <span class="detail-value">
                    <div class="time-slots">
                        <?php foreach ($booking['formatted_time_slots'] as $slot): ?>
                            <span class="time-slot"><?php echo htmlspecialchars($slot); ?></span>
                        <?php endforeach; ?>
                    </div>
                </span>
            </div>
        </div>
        <?php else: ?>
            <p>Booking details not found.</p>
        <?php endif; ?>

        <div class="buttons">
            <button class="btn print" onclick="window.print()">Print Details</button>
            <a href="index.html" class="btn">Return to Home</a>
        </div>
    </div>
</body>
</html>