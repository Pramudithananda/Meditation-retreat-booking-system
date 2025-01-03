<?php
// Database connection setup
require_once 'config.php';

// Query to count bookings by status
try {
    // Basic count by status
    $stmt = $pdo->query("
        SELECT 
            status,
            COUNT(*) as count,
            GROUP_CONCAT(DISTINCT slot_id) as time_slots,
            COUNT(DISTINCT booking_id) as unique_bookings
        FROM 
            bookings b
        LEFT JOIN 
            booking_slots bs ON b.id = bs.booking_id
        GROUP BY 
            status
        ORDER BY 
            status
    ");
    
    // Fetch the results
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Display the results
    echo "<h2>Booking Status Counts</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Status</th><th>Total Count</th><th>Unique Bookings</th><th>Time Slots Used</th></tr>";
    
    foreach ($results as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        echo "<td>" . htmlspecialchars($row['count']) . "</td>";
        echo "<td>" . htmlspecialchars($row['unique_bookings']) . "</td>";
        echo "<td>" . htmlspecialchars($row['time_slots']) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Get total counts
    $total_stmt = $pdo->query("
        SELECT 
            COUNT(*) as total_bookings,
            COUNT(DISTINCT booking_id) as total_unique_bookings
        FROM 
            booking_slots
    ");
    
    $totals = $total_stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<p>Total Bookings: " . $totals['total_bookings'] . "</p>";
    echo "<p>Total Unique Participants: " . $totals['total_unique_bookings'] . "</p>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
