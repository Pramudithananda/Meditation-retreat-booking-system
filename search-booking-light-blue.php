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
            header("Location: register_details.php?id=" . $results[0]['id']);
            exit;
        } elseif (count($results) > 1) {
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
    <!--<link rel="stylesheet" href="styles.css">-->
    <style>
        :root {
            --primary-color: #e8f4ff;
            --secondary-color: #bde0ff;
            --accent-color: #3498db;
            --text-color: #2c3e50;
            --error-color: #e74c3c;
            --hover-color: #f8fafc;
        }

        body {
            background-color: #f0f8ff;
            font-family: 'Arial', sans-serif;
            color: var(--text-color);
            line-height: 1.6;
        }

        .search-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: var(--accent-color);
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-color);
            font-weight: bold;
        }

        input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid var(--secondary-color);
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
            background-color: var(--primary-color);
        }

        input[type="text"]:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: var(--accent-color);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #2980b9;
        }

        .error-message {
            color: var(--error-color);
            background-color: #fde8e8;
            padding: 12px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: center;
        }

        .search-results {
            margin-top: 30px;
        }

        .search-results h3 {
            color: var(--accent-color);
            margin-bottom: 15px;
        }

        .result-item {
            padding: 15px;
            border-bottom: 1px solid var(--secondary-color);
            transition: background-color 0.3s ease;
        }

        .result-item:last-child {
            border-bottom: none;
        }

        .result-item:hover {
            background-color: var(--hover-color);
        }

        .result-item a {
            color: var(--text-color);
            text-decoration: none;
            display: block;
        }

        .result-item a:hover {
            color: var(--accent-color);
        }

        @media (max-width: 768px) {
            .search-container {
                margin: 20px;
                padding: 20px;
            }
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
                       placeholder="Enter name to search"
                       value="<?php echo isset($_POST['search_name']) ? htmlspecialchars($_POST['search_name']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="search_phone">Phone Number:</label>
                <input type="text" id="search_phone" name="search_phone" 
                       placeholder="Enter phone number"
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
