<?php
// Include database configuration
require_once 'config.php';
require_once 'phpqrcode/qrlib.php'; // Make sure to install PHP QR Code library

// Get booking ID from URL parameter
$bookingId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Generate QR Code
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$fullUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$qrImagePath = 'qrcodes/booking_' . $bookingId . '.png';

// Create directory if it doesn't exist
if (!file_exists('qrcodes')) {
    mkdir('qrcodes', 0777, true);
}

// Generate QR code
QRcode::png($fullUrl, $qrImagePath, QR_ECLEVEL_L, 10);

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
    <title>Booking Details </title>
    <style>
        /* Previous styles remain the same */
        @font-face {
            font-family: 'sinhala';
            src: url('fonts/sinhala.ttf') format('truetype');
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
        
        /* Add new styles for QR code */
        .qr-code-container {
            margin: 2rem auto;
            text-align: center;
        }
        
        .qr-code {
            max-width: 200px;
            margin: 0 auto;
        }
        
        .qr-code img {
            width: 100%;
            height: auto;
        }
        
        .qr-caption {
            margin-top: 1rem;
            color: #666;
            font-size: 0.9rem;
        }

        /* Previous styles continue... */
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">&#10003;</div>
        <h1>Booking Details </h1>
        <div class="multilingual">
            <p>Your meditation retreat booking has been confirmed.</p>
            <p>&#3476;&#3510;&#3484;&#3546; &#3511;&#3535;&#3520;&#3505;&#3535; &#3520;&#3536;&#3497;&#3523;&#3495;&#3524;&#3505;&#3530; &#3520;&#3545;&#3505;&#3530;&#3482;&#3538;&#3515;&#3539;&#3512; &#3501;&#3524;&#3520;&#3540;&#3515;&#3540; &#3482;&#3515; &#3463;&#3501;.</p>
        </div>

        <?php if ($booking): ?>
        <!-- Add QR Code section -->
        <div class="qr-code-container">
            <div class="qr-code">
                <img src="<?php echo htmlspecialchars($qrImagePath); ?>" alt="Booking QR Code">
            </div>
            <p class="qr-caption">Scan this QR code to view booking details on your mobile device</p>
        </div>

        <div class="details-container">
            <!-- Rest of the booking details remain the same -->
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
