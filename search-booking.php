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
    <style>
        :root {
            --primary-color: #e8f4ff;
            --secondary-color: #bde0ff;
            --accent-color: #3498db;
            --text-color: #2c3e50;
            --error-color: #e74c3c;
        }

        body {
            background-color: #f0f8ff;
            font-family: 'Arial', sans-serif;
            color: var(--text-color);
            line-height: 1.6;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .search-container {
            width: 100%;
            max-width: 600px;
            margin: 20px;
            padding: 10px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: var(--accent-color);
            text-align: center;
            margin-bottom: 40px;
            font-size: 36px;
            font-weight: normal;
        }
        h1 {
            color: var(--accent-color);
            text-align: center;
            margin-bottom: 40px;
            font-size: 36px;
            font-weight: normal;
        }

        .form-group {
            margin-bottom: 25px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: var(--text-color);
            font-size: 20px;
            width: 100%;
            text-align: left;
        }

        input[type="text"] {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            background-color: var(--primary-color);
            box-sizing: border-box;
            text-align: left;
        }

        input[type="text"]:focus {
            outline: none;
            background-color: var(--secondary-color);
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            background-color: var(--accent-color);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        .submit-btn:hover {
            background-color: #2980b9;
        }

        .error-message {
            color: var(--error-color);
            text-align: center;
            margin: 20px 0;
        }

        .search-results {
            margin-top: 30px;
            text-align: center;
        }

        .result-item {
            padding: 15px;
            border-bottom: 1px solid var(--secondary-color);
            text-align: center;
        }

        .result-item a {
            color: var(--text-color);
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .search-container {
                margin: 10px;
                padding: 20px;
            }
            
            h2 {
                font-size: 28px;
                margin-bottom: 30px;
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
                       placeholder="Enter name"
                       value="<?php echo isset($_POST['search_name']) ? htmlspecialchars($_POST['search_name']) : ''; ?>">
            </div>
            <h1>
                 Or
            </h1>
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
