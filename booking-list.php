<?php
require_once 'config.php';

// Get search parameter
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Modified query to include search
$stmt = $pdo->prepare("
    SELECT b.*, GROUP_CONCAT(bs.slot_id) as time_slots 
    FROM bookings b 
    LEFT JOIN booking_slots bs ON b.id = bs.booking_id 
    WHERE b.name LIKE :search
    GROUP BY b.id 
    ORDER BY b.created_at DESC
");
$stmt->execute(['search' => "%$search%"]);
$bookings = $stmt->fetchAll();

// Get current language from URL parameter or default to Sinhala
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'si';

// Add search text to translations
$translations = [
    'si' => [
        'title' => 'ලියාපදිංචි වූවන්ගේ ලැයිස්තුව',
        'name' => 'නම',
        'age' => 'වයස',
        'experience' => 'අත්දැකීම්',
        'status' => 'තත්වය',
        'address' => 'ලිපිනය',
        'telephone' => 'දුරකථන',
        'time_slots' => 'කාල පරාස',
        'edit' => 'වෙනස් කරන්න',
        'beginner' => 'ආරම්භක',
        'experienced' => 'පළපුරුදු',
        'layperson' => 'ගිහි',
        'bikkhu' => 'භික්ෂු',
        'nun' => 'මේහෙණින් වහන්සේ',
        'search_placeholder' => 'නම සොයන්න...',
        'no_results' => 'ප්‍රතිඵල හමු නොවීය'
    ],
    'en' => [
        'title' => 'Registered Participants List',
        'name' => 'Name',
        'age' => 'Age',
        'experience' => 'Experience',
        'status' => 'Status',
        'address' => 'Address',
        'telephone' => 'Telephone',
        'time_slots' => 'Time Slots',
        'edit' => 'Edit',
        'beginner' => 'Beginner',
        'experienced' => 'Experienced',
        'layperson' => 'Layperson',
        'bikkhu' => 'Bikkhu',
        'nun' => 'Nun',
        'search_placeholder' => 'Search by name...',
        'no_results' => 'No results found'
    ]
];

// Time slot mapping remains the same...
$timeSlots = [
    1 => "06:00PM - 08:00PM",
    2 => "08:00PM - 10:00PM",
    3 => "10:00PM - 12:00AM",
    4 => "12:00AM - 02:00AM",
    5 => "02:00AM - 04:00AM",
    6 => "04:00AM - 06:00AM"
];
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $translations[$lang]['title']; ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Previous styles remain... */
        
        .search-container {
            margin: 20px 0;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .search-input {
            flex: 1;
            max-width: 300px;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .search-input:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 3px rgba(74, 175, 80, 0.3);
        }

        .no-results {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
        /* Container styles */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Language selector styles */
.language-selector {
    margin-bottom: 20px;
    text-align: right;
}

.language-btn {
    display: inline-block;
    padding: 8px 16px;
    margin-left: 10px;
    background-color: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-decoration: none;
    color: #333;
}

.language-btn.active {
    background-color: #4CAF50;
    color: white;
    border-color: #4CAF50;
}

/* Table styles */
.booking-list {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background-color: white;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.booking-list th,
.booking-list td {
    border: 1px solid #ddd;
    padding: 12px;
    text-align: left;
}

.booking-list th {
    background-color: #f5f5f5;
    font-weight: bold;
}

.booking-list tr:nth-child(even) {
    background-color: #f9f9f9;
}

.booking-list tr:hover {
    background-color: #f5f5f5;
}

/* Edit button styles */
.edit-btn {
    display: inline-block;
    background-color: #4CAF50;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    text-align: center;
}

.edit-btn:hover {
    background-color: #45a049;
}

/* Responsive styles */
@media screen and (max-width: 1024px) {
    .container {
        padding: 15px;
    }

    .booking-list th,
    .booking-list td {
        padding: 10px;
    }
}

@media screen and (max-width: 768px) {
    .booking-list {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }

    .booking-list th,
    .booking-list td {
        font-size: 24px;
        padding: 8px;
    }

    .edit-btn {
        padding: 6px 12px;
        font-size: 24px;
    }
}

@media screen and (max-width: 480px) {
    .container {
        padding: 10px;
    }

    h1 {
        font-size: 34px;
    }

    .language-btn {
        padding: 6px 12px;
        font-size: 34px;
    }

    .booking-list th,
    .booking-list td {
        padding: 6px;
        font-size: 23px;
    }

    .edit-btn {
        padding: 4px 10px;
        font-size: 23px;
    }
}
    </style>
</head>
<body>
    <div class="container">
        <div class="language-selector">
            <a href="?lang=si" class="language-btn <?php echo $lang === 'si' ? 'active' : ''; ?>">සිංහල</a>
            <a href="?lang=en" class="language-btn <?php echo $lang === 'en' ? 'active' : ''; ?>">English</a>
        </div>

        <h1><?php echo $translations[$lang]['title']; ?></h1>

        <div class="search-container">
            <input 
                type="text" 
                id="searchInput" 
                class="search-input" 
                placeholder="<?php echo $translations[$lang]['search_placeholder']; ?>"
                value="<?php echo htmlspecialchars($search); ?>"
            >
        </div>

        <table class="booking-list">
            <thead>
                <tr>
                    <th><?php echo $translations[$lang]['name']; ?></th>
                    <th><?php echo $translations[$lang]['age']; ?></th>
                    <th><?php echo $translations[$lang]['experience']; ?></th>
                    <th><?php echo $translations[$lang]['status']; ?></th>
                    <th><?php echo $translations[$lang]['telephone']; ?></th>
                    <th><?php echo $translations[$lang]['time_slots']; ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bookings)): ?>
                    <tr>
                        <td colspan="7" class="no-results">
                            <?php echo $translations[$lang]['no_results']; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['name']); ?></td>
                            <td><?php echo $booking['age']; ?></td>
                            <td><?php echo $translations[$lang][$booking['experience']]; ?></td>
                            <td><?php echo $translations[$lang][$booking['status']]; ?></td>
                            <td><?php echo htmlspecialchars($booking['telephone']); ?></td>
                            <td>
                                <?php
                                $slots = explode(',', $booking['time_slots']);
                                foreach ($slots as $slot) {
                                    echo $timeSlots[$slot] . "<br>";
                                }
                                ?>
                            </td>
                            <td>
                                <a href="edit-booking.php?id=<?php echo $booking['id']; ?>&lang=<?php echo $lang; ?>" class="edit-btn">
                                    <?php echo $translations[$lang]['edit']; ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        document.getElementById('searchInput').addEventListener('input', function(e) {
            // Get current URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            
            // Update or add search parameter
            if (e.target.value) {
                urlParams.set('search', e.target.value);
            } else {
                urlParams.delete('search');
            }
            
            // Preserve language parameter
            if (!urlParams.has('lang')) {
                urlParams.set('lang', '<?php echo $lang; ?>');
            }
            
            // Update URL and reload page
            window.location.href = window.location.pathname + '?' + urlParams.toString();
        });
    </script>
</body>
</html>
