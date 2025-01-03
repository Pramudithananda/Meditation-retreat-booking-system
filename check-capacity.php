<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    $slots = [];
    $stmt = $pdo->query("
        SELECT 
            bs.slot_id, 
            COUNT(bs.id) as booked_count, 
            ts.capacity
        FROM time_slots ts
        LEFT JOIN booking_slots bs ON ts.id = bs.slot_id
        GROUP BY bs.slot_id, ts.capacity
    ");
    
    while ($row = $stmt->fetch()) {
        $slots[$row['slot_id']] = $row['booked_count'];
    }
    
    echo json_encode($slots);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>