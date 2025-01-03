<?php
require_once 'config.php';

try {
    // Existing queries for status and age
    $statusQuery = "SELECT status, COUNT(*) as count FROM bookings GROUP BY status ORDER BY count DESC";
    $ageQuery = "SELECT
        CASE
            WHEN age BETWEEN 12 AND 20 THEN '12-20'
            WHEN age BETWEEN 21 AND 40 THEN '21-40'
            WHEN age BETWEEN 41 AND 60 THEN '41-60'
            ELSE 'Above 60'
        END as age_range,
        COUNT(*) as count
        FROM bookings
        GROUP BY CASE
            WHEN age BETWEEN 12 AND 20 THEN '12-20'
            WHEN age BETWEEN 21 AND 40 THEN '21-40'
            WHEN age BETWEEN 41 AND 60 THEN '41-60'
            ELSE 'Above 60'
        END ORDER BY age_range";
    
    $statusStmt = $pdo->query($statusQuery);
    $ageStmt = $pdo->query($ageQuery);
    $statusCounts = $statusStmt->fetchAll();
    $ageCounts = $ageStmt->fetchAll();

    // Modified district and division queries
    $selectedDistrict = isset($_GET['district']) ? $_GET['district'] : '';
    
    // Get district totals
    $districtQuery = "SELECT 
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(address, 'District:', -1), 'Division:', 1)) as district,
        COUNT(*) as count 
        FROM bookings 
        GROUP BY district 
        ORDER BY district";
    
    // Get division counts per district
    $divisionQuery = "SELECT 
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(address, 'District:', -1), 'Division:', 1)) as district,
        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(address, 'Division:', -1), 'Address:', 1)) as division,
        COUNT(*) as count 
        FROM bookings " .
        ($selectedDistrict ? "WHERE TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(address, 'District:', -1), 'Division:', 1)) = ?" : "") .
        " GROUP BY district, division ORDER BY district, division";

    $districtStmt = $pdo->query($districtQuery);
    $districts = $districtStmt->fetchAll();

    if ($selectedDistrict) {
        $divisionStmt = $pdo->prepare($divisionQuery);
        $divisionStmt->execute([$selectedDistrict]);
    } else {
        $divisionStmt = $pdo->query($divisionQuery);
    }
    $divisions = $divisionStmt->fetchAll();

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meditation Retreat Statistics</title>
    <style>
        /* Keep existing styles */
        .division-item {
            margin-left: 20px;
            color: #666;
            padding: 8px 0;
            display: flex;
            justify-content: space-between;
        }
        .filter-section {
            margin-bottom: 15px;
        }
        select {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
    </style>
    <!-- Include your existing styles here -->
</head>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(120deg, #89f7fe, #66a6ff);
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #fff;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stats-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .stats-card h2 {
            color: #2196F3;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 1.5rem;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s;
        }

        .stat-item:last-child {
            border-bottom: none;
        }

        .stat-item:hover {
            background-color: #f8f9fa;
            padding-left: 10px;
            padding-right: 10px;
            margin: 0 -10px;
            border-radius: 5px;
        }

        .stat-label {
            color: #555;
            font-weight: 500;
        }
        .stat-label-dis {
            color: #148f77;
            font-weight: 500;
        }
        .stat-value {
            font-weight: bold;
            color: #3498db;
            background: rgba(33, 150, 243, 0.1);
            padding: 2px 10px;
            border-radius: 15px;
        }
        .stat-value-dis {
            font-weight: bold;
            color: #C70039;
            background: rgba(33, 150, 243, 0.1);
            padding: 2px 10px;
            border-radius: 15px;
        }
        .stat-value-div {
            font-weight: bold;
            color: #3498db;
            background: rgba(33, 150, 243, 0.1);
            padding: 2px 10px;
            border-radius: 15px;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background: #2196F3;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
            margin-top: 20px;
        }

        .back-button:hover {
            background: #1976D2;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
<body>
    <div class="container">
        <h1>Meditation Retreat Statistics</h1>

        <div class="stats-grid">
            <!-- Status Statistics Card -->
            <div class="stats-card">
                <h2>Status Distribution</h2>
                <?php foreach ($statusCounts as $status): ?>
                    <div class="stat-item">
                        <span class="stat-label"><?= htmlspecialchars($status['status']) ?></span>
                        <span class="stat-value"><?= htmlspecialchars($status['count']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Age Distribution Card -->
            <div class="stats-card">
                <h2>Age Distribution</h2>
                <?php foreach ($ageCounts as $age): ?>
                    <div class="stat-item">
                        <span class="stat-label"><?= htmlspecialchars($age['age_range']) ?></span>
                        <span class="stat-value"><?= htmlspecialchars($age['count']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Modified District Distribution Card -->
            <div class="stats-card">
                <h2>District Distribution</h2>
                <div class="filter-section">
                    <form method="GET">
                        <select name="district" onchange="this.form.submit()">
                            <option value="">All Districts</option>
                            <?php foreach ($districts as $district): ?>
                                <option value="<?= htmlspecialchars($district['district']) ?>"
                                    <?= $selectedDistrict === $district['district'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($district['district']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
                <?php
                foreach ($districts as $district) {
                    echo "<div class='stat-item'>";
                    echo "<span class='stat-label-dis'>" . htmlspecialchars($district['district']) . "</span>";
                    echo "<span class='stat-value-dis'>" . htmlspecialchars($district['count']) . "</span>";
                    echo "</div>";

                    foreach ($divisions as $division) {
                        if ($division['district'] === $district['district']) {
                            echo "<div class='division-item'>";
                            echo "<span class='stat-label'>" . htmlspecialchars($division['division']) . "</span>";
                            echo "<span class='stat-value-div'>" . htmlspecialchars($division['count']) . "</span>";
                            echo "</div>";
                        }
                    }
                }
                ?>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="admin.php" class="back-button">Back to Admin Page</a>
        </div>
    </div>
</body>
</html>