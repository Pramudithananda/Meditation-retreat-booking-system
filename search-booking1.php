<?php
// search-booking.php
session_start();
require_once 'config.php';

// Initialize variables
$error = '';
$searchResult = null;

// Process search when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'search_name', FILTER_SANITIZE_STRING);
    $phone = filter_input(INPUT_POST, 'search_phone', FILTER_SANITIZE_STRING);

    try {
        // Prepare the query based on provided inputs
        $query = "SELECT id, name, telephone FROM bookings WHERE 1=1";
        $params = [];

        if (!empty($name)) {
            $query .= " AND name LIKE ?";
            $params[] = "%$name%";
        }

        if (!empty($phone)) {
            $query .= " AND telephone = ?";
            $params[] = $phone;
        }

        if (empty($params)) {
            throw new Exception('Please provide at least one search criteria');
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $results = $stmt->fetchAll();

        if (count($results) === 1) {
            // If exactly one result is found, redirect to the details page
            header("Location: register_details.php?id=" . $results[0]['id']);
            exit;
        } elseif (count($results) > 1) {
            // If multiple results, store them to display
            $searchResult = $results;
        } else {
            $error = 'No booking found with the provided information';
        }

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Booking</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .search-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .search-results {
            margin-top: 20px;
        }
        .error-message {
            color: red;
            margin: 10px 0;
        }
        .result-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .result-item:hover {
            background: #f5f5f5;
        }
        .result-item a {
            color: #333;
            text-decoration: none;
        }
        .result-item a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="search-container">
        <h2>Search Booking</h2>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label for="search_name">Name:</label>
                <input type="text" id="search_name" name="search_name" 
                       value="<?php echo isset($_POST['search_name']) ? htmlspecialchars($_POST['search_name']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="search_phone">Phone Number:</label>
                <input type="text" id="search_phone" name="search_phone" 
                       value="<?php echo isset($_POST['search_phone']) ? htmlspecialchars($_POST['search_phone']) : ''; ?>">
            </div>

            <div class="form-group">
                <button type="submit" class="submit-btn">Search</button>
            </div>
        </form>

        <?php if ($error): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($searchResult): ?>
            <div class="search-results">
                <h3>Multiple bookings found:</h3>
                <?php foreach ($searchResult as $booking): ?>
                    <div class="result-item">
                        <a href="register_details.php?id=<?php echo htmlspecialchars($booking['id']); ?>">
                            <?php echo htmlspecialchars($booking['name']); ?> - 
                            <?php echo htmlspecialchars($booking['telephone']); ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
