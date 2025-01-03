<?php
require_once 'config.php';

try {
    // Get selected district filter if any
    $selectedDistrict = isset($_GET['district']) ? $_GET['district'] : '';
    
    // Query for unique districts (for filter dropdown)
    $distinctQuery = "SELECT DISTINCT district FROM bookings ORDER BY district";
    $distinctStmt = $pdo->query($distinctQuery);
    $districts = $distinctStmt->fetchAll(PDO::FETCH_COLUMN);

    // Modified district query with optional filter
    $districtQuery = "
        SELECT 
            address,
            COUNT(*) as count,
            GROUP_CONCAT(DISTINCT division) as divisions
        FROM 
            bookings
        " . ($selectedDistrict ? "WHERE district = ?" : "") . "
        GROUP BY 
            district
        ORDER BY 
            count DESC
    ";
    
    if ($selectedDistrict) {
        $districtStmt = $pdo->prepare($districtQuery);
        $districtStmt->execute([$selectedDistrict]);
    } else {
        $districtStmt = $pdo->query($districtQuery);
    }
    $districtCounts = $districtStmt->fetchAll();

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>District Statistics</title>
    <style>
        /* Previous styles remain unchanged */
        .filter-section {
            margin-bottom: 20px;
            text-align: center;
        }
        
        select {
            padding: 8px 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-right: 10px;
        }
        
        .filter-button {
            padding: 8px 15px;
            background: #2196F3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .divisions {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>District Statistics</h1>

        <div class="filter-section">
            <form action="" method="GET">
                <select name="district">
                    <option value="">All Districts</option>
                    <?php foreach ($districts as $district): ?>
                        <option value="<?php echo htmlspecialchars($district); ?>"
                                <?php echo $selectedDistrict === $district ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($district); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="filter-button">Filter</button>
            </form>
        </div>

        <div class="stats-grid">
            <div class="stats-card">
                <h2>District Distribution</h2>
                <?php foreach ($districtCounts as $data): ?>
                    <div class="stat-item">
                        <div>
                            <span class="stat-label"><?php echo htmlspecialchars($data['district']); ?></span>
                            <div class="divisions">
                                Divisions: <?php echo htmlspecialchars($data['divisions']); ?>
                            </div>
                        </div>
                        <span class="stat-value"><?php echo htmlspecialchars($data['count']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="admin-page.php" class="back-button">Back to Admin Page</a>
        </div>
    </div>
</body>
</html>