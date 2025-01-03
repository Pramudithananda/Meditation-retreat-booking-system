<?php
// Include database configuration
require_once 'config.php';

try {
    // Query for status counts
    $statusQuery = "
        SELECT 
            status,
            COUNT(*) as count
        FROM 
            bookings
        GROUP BY 
            status
    ";
    $statusStmt = $pdo->query($statusQuery);
    $statusCounts = $statusStmt->fetchAll();

    // Query for age range counts
    $ageQuery = "
        SELECT
            CASE
                WHEN age BETWEEN 12 AND 20 THEN '12-20'
                WHEN age BETWEEN 21 AND 40 THEN '21-40'
                WHEN age BETWEEN 41 AND 60 THEN '41-60'
                ELSE 'Above 60'
            END as age_range,
            COUNT(*) as count
        FROM
            bookings
        GROUP BY
            CASE
                WHEN age BETWEEN 12 AND 20 THEN '12-20'
                WHEN age BETWEEN 21 AND 40 THEN '21-40'
                WHEN age BETWEEN 41 AND 60 THEN '41-60'
                ELSE 'Above 60'
            END
        ORDER BY
            age_range
    ";
    $ageStmt = $pdo->query($ageQuery);
    $ageCounts = $ageStmt->fetchAll();

    // Query for district and division counts
    $locationQuery = "
        SELECT 
            district,
            division,
            COUNT(*) as count
        FROM 
            bookings
        GROUP BY 
            district,
            division
        ORDER BY 
            district,
            division
    ";
    $locationStmt = $pdo->query($locationQuery);
    $locationCounts = $locationStmt->fetchAll();

} catch (PDOException $e) {
    die("Error fetching statistics: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meditation Retreat Statistics</title>
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
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .stat-item:last-child {
            border-bottom: none;
        }

        .stat-label {
            color: #666;
        }

        .stat-value {
            font-weight: bold;
            color: #333;
        }

        .location-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .location-table th,
        .location-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .location-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Meditation Retreat Statistics</h1>

        <div class="stats-grid">
            <!-- Status Statistics -->
            <div class="stats-card">
                <h2>Status Distribution</h2>
                <?php foreach ($statusCounts as $status): ?>
                    <div class="stat-item">
                        <span class="stat-label"><?php echo htmlspecialchars($status['status']); ?></span>
                        <span class="stat-value"><?php echo htmlspecialchars($status['count']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Age Range Statistics -->
            <div class="stats-card">
                <h2>Age Distribution</h2>
                <?php foreach ($ageCounts as $age): ?>
                    <div class="stat-item">
                        <span class="stat-label"><?php echo htmlspecialchars($age['age_range']); ?></span>
                        <span class="stat-value"><?php echo htmlspecialchars($age['count']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Location Statistics -->
            <div class="stats-card">
                <h2>Location Distribution</h2>
                <table class="location-table">
                    <thead>
                        <tr>
                            <th>District</th>
                            <th>Division</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($locationCounts as $location): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($location['district']); ?></td>
                                <td><?php echo htmlspecialchars($location['division']); ?></td>
                                <td><?php echo htmlspecialchars($location['count']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="admin-page.php" style="
                display: inline-block;
                padding: 10px 20px;
                background: #2196F3;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                transition: background 0.3s;
            ">Back to Admin Page</a>
        </div>
    </div>
</body>
</html>
