<?php
// Database connection
$conn = new mysqli('localhost', '1083571', 'monaragala', '1083571');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch registered members
$sql = "SELECT id, name FROM bookings ORDER BY id ASC";
$result = $conn->query($sql);
$registrants = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $registrants[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Registered Members</title>
    <link rel="stylesheet" href="styles.css">
    <style>
body {
    margin: 0;
    padding: 0;
    font-family: 'Arial', sans-serif;
    background: linear-gradient(120deg, #a6c0fe, #f68084); /* Gradient background */
    color: #ffffff;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.container {
    text-align: center;
    background: rgba(0, 0, 0, 0.6); /* Semi-transparent overlay */
    padding: 20px 40px;
    border-radius: 10px;
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
}

h1 {
    font-size: 2.5rem;
    margin-bottom: 20px;
    letter-spacing: 2px;
    text-transform: uppercase;
    background: -webkit-linear-gradient(#ff9a9e, #fad0c4);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.blinking {
    font-size: 2.5rem;
    color: #ffd700; /* Bright yellow for blinking text */
    animation: blink 1s infinite;
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0; }
}
    </style>
</head>
<body>
    <div class="container">
        <h1>Registered Names & Numbers</h1>
        <div id="blinking-area" class="blinking"></div>
    </div>
    <script>
        const registrants = <?php echo json_encode($registrants); ?>;
        let index = 0;

        function showRegistrant() {
            const displayArea = document.getElementById('blinking-area');
            if (registrants.length > 0) {
                const { id, name } = registrants[index];
                displayArea.textContent = `#${id}  --- ${name}`;
                index = (index + 1) % registrants.length;
            }
        }

        setInterval(showRegistrant, 6000); // Change every second
        showRegistrant(); // Display the first registrant immediately
    </script>
</body>
</html>
	