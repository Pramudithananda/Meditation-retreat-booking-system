<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    $name = filter_input(INPUT_GET, 'name', FILTER_SANITIZE_STRING);
    
    if (!$name) {
        throw new Exception('Name is required');
    }

    // Search for booking
    $stmt = $pdo->prepare("
        SELECT * FROM bookings 
        WHERE name LIKE ?
        ORDER BY id DESC 
        LIMIT 1
    ");
    
    $stmt->execute(["%" . $name . "%"]);
    $booking = $stmt->fetch();

    if ($booking) {
        echo json_encode([
            'success' => true,
            'booking' => $booking
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No booking found with that name'
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
