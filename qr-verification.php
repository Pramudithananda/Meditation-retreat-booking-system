<?php
require_once 'config.php';

$message = '';
$booking = null;

function extractIdFromUrl($url) {
    $parsedUrl = parse_url($url);
    if (isset($parsedUrl['query'])) {
        parse_str($parsedUrl['query'], $params);
        return isset($params['id']) ? $params['id'] : null;
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST['booking_id'];
    $bookingId = is_numeric($input) ? $input : extractIdFromUrl($input);

    if ($bookingId) {
        try {
    $stmt = $pdo->prepare("
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
            b.id = ?
        GROUP BY 
            b.id
    ");
    $stmt->execute([$bookingId]);
    $booking = $stmt->fetch();
    
    // Add time slot mapping
    $timeSlotMap = [
        1 => "06:00PM - 08:00PM",
        2 => "08:00PM - 10:00PM",
        3 => "10:00PM - 12:00AM",
        4 => "12:00AM - 02:00AM",
        5 => "02:00AM - 04:00AM",
        6 => "04:00AM - 06:00AM"
    ];

    // Convert slot IDs to readable time slots if booking exists
    if ($booking && $booking['time_slots']) {
        $timeSlots = [];
        foreach (explode(',', $booking['time_slots']) as $slotId) {
            if (isset($timeSlotMap[$slotId])) {
                $timeSlots[] = $timeSlotMap[$slotId];
            }
        }
        $booking['formatted_time_slots'] = implode(', ', $timeSlots);
    }

    if ($booking) {
        $message = 'Booking found successfully';
    } else {
        $message = 'Invalid booking ID';
    }
} catch (Exception $e) {
    $message = 'Error: ' . $e->getMessage();
}
    } else {
        $message = 'Invalid QR code format';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR Code</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/html5-qrcode"></script>
    <style>
        .scanner-container {
            width: 100%;
            max-width: 500px;
            margin: 20px auto;
        }
        .result-container {
            margin-top: 20px;
            padding: 20px;
            border-radius: 8px;
            background: #f8f9fa;
        }
        .valid {
            background: #d4edda;
            color: #155724;
        }
        .invalid {
            background: #f8d7da;
            color: #721c24;
        }
        .detail-row {
            display: flex;
            margin: 10px 0;
            border-bottom: 1px solid #ddd;
            padding: 5px 0;
        }
        .detail-label {
            font-weight: bold;
            width: 150px;
        }
        #reader {
            width: 100%;
            border-radius: 8px;
            overflow: hidden;
        }
        button[type="submit"] {
            background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        button[type="submit"]:hover {
            background: linear-gradient(135deg, #357abd 0%, #2868a9 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        button[type="submit"]:active {
            transform: translateY(1px);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }
        
        @media (max-width: 480px) {
            #manualForm {
                flex-direction: column;
            }
            
            button[type="submit"] {
                width: 100%;
            }
            /* Styles for QR Scanner buttons */
        #reader__dashboard_section_csr button,
        #reader__dashboard_section_swaplink {
            background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 10px 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        #reader__dashboard_section_csr button:hover,
        #reader__dashboard_section_swaplink:hover {
            background: linear-gradient(135deg, #357abd 0%, #2868a9 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        #reader__dashboard_section_csr button:active,
        #reader__dashboard_section_swaplink:active {
            transform: translateY(1px);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        /* Style for the stop scanning button */
        #reader__dashboard_section_csr button#stop-scan-button {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        }

        #reader__dashboard_section_csr button#stop-scan-button:hover {
            background: linear-gradient(135deg, #c0392b 0%, #a93226 100%);
        }

        /* Custom container for scanner buttons */
        .scanner-button-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 15px 0;
        }

        /* Style the file input button */
        #reader__filescan_input {
            display: none;
        }

        /* Style the select file button */
        #reader__filescan_input + label {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        #reader__filescan_input + label:hover {
            background: linear-gradient(135deg, #27ae60 0%, #219a52 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Improve the scanner region style */
        #reader {
            border: 2px solid #4a90e2;
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        #reader__scan_region {
            background: #f8f9fa;
        }

        /* Make the video preview more attractive */
        #reader__camera_selection {
            margin: 10px;
            padding: 8px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            width: calc(100% - 20px);
        }

        /* Status messages styling */
        #reader__status_span {
            color: #4a90e2;
            font-weight: 600;
            margin: 10px 0;
            display: block;
            text-align: center;
        }
        /* Scanner Area Styles */
#reader {
    width: 100%;
    border: 2px solid #4a90e2;
    border-radius: 12px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin: 20px 0;
}

#reader__scan_region {
    background: #f8f9fa;
    padding: 20px;
    border-bottom: 1px solid #e0e0e0;
}

/* File Scan Button Styles */
#reader__filescan_input {
    display: none;
}

#reader__filescan_input + label {
    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    display: inline-block;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 10px 0;
    width: auto;
}

#reader__filescan_input + label:hover {
    background: linear-gradient(135deg, #27ae60 0%, #219a52 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Camera Selection Styles */
#reader__camera_selection {
    width: calc(100% - 20px);
    margin: 10px;
    padding: 12px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    background: white;
    font-size: 16px;
    color: #333;
    cursor: pointer;
    transition: all 0.3s ease;
}

#reader__camera_selection:hover {
    border-color: #4a90e2;
}

#reader__camera_selection:focus {
    outline: none;
    border-color: #4a90e2;
    box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
}

/* Camera Permission Button Styles */
#reader__dashboard_section_csr button {
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin: 10px 5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

#reader__dashboard_section_csr button:hover {
    background: linear-gradient(135deg, #357abd 0%, #2868a9 100%);
    transform: translateY(-1px);
}

/* Camera Swap Link Styles */
#reader__dashboard_section_swaplink {
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin: 10px 5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-decoration: none;
    display: inline-block;
}

#reader__dashboard_section_swaplink:hover {
    background: linear-gradient(135deg, #357abd 0%, #2868a9 100%);
    transform: translateY(-1px);
}

/* Status Message Styles */
#reader__status_span {
    color: #4a90e2;
    font-weight: 600;
    margin: 10px 0;
    display: block;
    text-align: center;
    padding: 10px;
}

/* Mobile Responsiveness */
@media (max-width: 480px) {
    #reader__camera_selection {
        width: calc(100% - 24px);
        margin: 10px auto;
    }
    
    #reader__dashboard_section_csr button,
    #reader__dashboard_section_swaplink,
    #reader__filescan_input + label {
        width: calc(100% - 20px);
        margin: 5px 10px;
        text-align: center;
    }
}
    </style>
</head>
<body>
    <div class="container">
        <h1>QR Code Verification</h1>
        
        <div class="scanner-container">
            <div id="reader"></div>
            
            <form id="manualForm" method="POST" style="margin-top: 20px;">
                <input type="text" name="booking_id" placeholder="Enter booking ID or URL">
                <button type="submit">Verify</button>
            </form>
        </div>

        <?php if ($message): ?>
            <div class="result-container <?php echo $booking ? 'valid' : 'invalid'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($booking): ?>
            <div class="result-container valid">
                <h2>Booking Details</h2>
                <div class="detail-row">
                    <span class="detail-label">Booking ID:</span>
                    <span><?php echo htmlspecialchars($booking['id']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Name:</span>
                    <span><?php echo htmlspecialchars($booking['name']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Age:</span>
                    <span><?php echo htmlspecialchars($booking['age']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Experience:</span>
                    <span><?php echo htmlspecialchars($booking['experience']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span><?php echo htmlspecialchars($booking['status']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Time Slots:</span>
                    <span><?php echo htmlspecialchars($booking['time_slots']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Telephone:</span>
                    <span><?php echo htmlspecialchars($booking['telephone']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Address:</span>
                    <span><?php echo htmlspecialchars($booking['address']); ?></span>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", 
            { 
                fps: 10,
                qrbox: { width: 250, height: 250 }
            }
        );

        function onScanSuccess(decodedText) {
            document.querySelector('input[name="booking_id"]').value = decodedText;
            document.getElementById('manualForm').submit();
        }

        html5QrcodeScanner.render(onScanSuccess);
    </script>
</body>
</html>