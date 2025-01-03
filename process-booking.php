<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    // Validate and sanitize input
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $age = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT);
    $experience = filter_input(INPUT_POST, 'experience', FILTER_SANITIZE_STRING);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);
    $time_periods = filter_input(INPUT_POST, 'time_period', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? [];

    // Validation
    if (!$name || !$age || !$experience || !$status || !$address || !$telephone || empty($time_periods)) {
        throw new Exception('All fields are required');
    }
    if ($age < 12 || $age > 100) {
        throw new Exception('Age must be between 15 and 65');
    }

    // Start transaction
    $pdo->beginTransaction();

    // Insert booking
    $stmt = $pdo->prepare("
        INSERT INTO bookings (name, age, experience, status, address, telephone) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$name, $age, $experience, $status, $address, $telephone]);
    $booking_id = $pdo->lastInsertId();

    // Check and insert time slots
    foreach ($time_periods as $slot_id) {
        $slot_id = intval($slot_id);

        // Check capacity
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count 
            FROM booking_slots 
            WHERE slot_id = ?
        ");
        $stmt->execute([$slot_id]);
        $result = $stmt->fetch();

        if ($result['count'] >= 3500) {
            throw new Exception("Selected time slot is full");
        }

        // Insert booking slot
        $stmt = $pdo->prepare("
            INSERT INTO booking_slots (booking_id, slot_id) 
            VALUES (?, ?)
        ");
        $stmt->execute([$booking_id, $slot_id]);

        // Update count
        $stmt = $pdo->prepare("
            UPDATE time_slot_counts 
            SET booked_count = booked_count + 1 
            WHERE slot_id = ?
        ");
        $stmt->execute([$slot_id]);
    }

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Booking successful!',
        'bookingId' => $booking_id  // Add booking ID to response
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
