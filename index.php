<?php
include('server.php');

// Check if the user wants to log out
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    // Destroy session to log out
    session_unset();  // Remove all session variables
    session_destroy(); // Destroy the session
    header("Location: login.php"); // Redirect to login page after logout
    exit();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
// Fetch house details for the tenant
$house_query = "SELECT * FROM houses WHERE user_id = '$user_id'";
$house_result = mysqli_query($db, $house_query);
$house = mysqli_fetch_assoc($house_result);

// Query to get all payments related to the user
$query = "SELECT * FROM payments WHERE user_id = '$user_id'";
$result = mysqli_query($db, $query);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($db));
}

// Notification count query
$notification_query = "SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = '$user_id' AND status = 'unread'";
$notification_result = mysqli_query($db, $notification_query);
$notification_data = mysqli_fetch_assoc($notification_result);
$unread_count = $notification_data['unread_count'];

// Fetch unread notifications
$notification_query = "SELECT * FROM notifications WHERE user_id = '$user_id' AND status = 'unread'";
$notification_result = mysqli_query($db, $notification_query);

while ($row = mysqli_fetch_assoc($notification_result)) {
    echo "<div class='notification'>";
    echo "<p>" . $row['message'] . "</p>";
    echo "<p><small>Sent on: " . $row['date_sent'] . "</small></p>";
    echo "</div>";
}

// Mark notifications as read
$query = "UPDATE notifications SET status = 'read' WHERE user_id = '$user_id' AND status = 'unread'";
mysqli_query($db, $query);

echo "Notifications marked as read.";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Dashboard</title>
<style>
    /* General Styles */
body {
    font-family: 'Poppins', sans-serif;
    background-color: #f4f7f6;
    margin: 0;
    padding: 0;
}

/* Header */
header {
    background: linear-gradient(90deg, #ff7200, #e65100);
    color: white;
    text-align: center;
    padding: 10px;
    font-size: 10px;
    font-weight: bold;
    position: sticky;
    top: 0;
    z-index: 1000;
}

 header h1 {
            margin: 0;
            font-size: 2.5em;
            letter-spacing: 1px;
        }

        .notification{
            position:absolute;
            z-index:100;
            top: 30%;
            left:30%;
        }

/* Sidebar */
.sidebar {
    width: 260px;
    height: 100vh;
    background-color: #222;
    color: white;
    position: fixed;
    left: 0;
    top: 0;
    padding-top: 20px;
    transition: all 0.3s ease;
}

.sidebar p {
    text-align: center;
    font-size: 1.5em;
    font-weight: bold;
    color: #ff7200;
}

.sidebar a {
    display: block;
    padding: 15px;
    color: white;
    text-decoration: none;
    text-align: center;
    font-size: 1.1em;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.sidebar a:hover {
    background-color: #ff7200;
    color: #222;
}

.sidebar a.active {
    background-color: #ff7200;
}

/* Main Content */
.main-content {
    margin-left: 280px;
    padding: 30px;
}

h2 {
    color: #ff7200;
}

/* Forms */
form {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 500px;
    margin: auto;
}

input, select {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
}

/* Buttons */
button {
    background: linear-gradient(to right, #ff7200, #e65100);
    color: white;
    padding: 12px;
    border: none;
    width: 100%;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    border-radius: 5px;
    transition: 0.3s;
}

button:hover {
    background: linear-gradient(to right, #e65100, #bf360c);
}

/* Tables */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: center;
}

th {
    background-color: #ff7200;
    color: white;
    font-size: 16px;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}

tr:hover {
    background-color: #ff7200;
    color: white;
}

/* Responsive Design */
@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
    }
    .main-content {
        margin-left: 0;
        padding: 15px;
    }
}
</style>
</head>
<body>

<header>
    <h1>Tenant Dashboard</h1>
<div class="notification-bell" id="notification-bell">
       
        <div class="badge" id="badge"> 🛎️ <?php echo $unread_count; ?></div>
    </div>

</header>

<!-- Sidebar Section -->
<div class="sidebar">
    <p>Tenant Panel</p>
   <a href="index.php?logout=1">Logout</a>
    <a href="index.php" class="active">Dashboard</a>
   
</div>

<!-- Main Content Section -->
<br><br><div class="main-content"></br></br>
    
<h2>Your House Details</h2>
        <?php if ($house) { ?>
            <p><strong>House Number:</strong> <?= $house['house_number'] ?></p>
            <p><strong>Type:</strong> <?= $house['house_type'] ?></p>
            <p><strong>Status:</strong> <?= $house['status'] ?></p>
        <?php } else { ?>
            <p>You have not been assigned a house yet.</p>
        <?php } ?>
    </section>
    

    <!-- Make Payment Section -->
    <section>
        <h2>Make Payment</h2>
        <form action="index.php" method="post">
            <input type="hidden" name="house_id" value="<!-- House ID will be here -->">
            <div class="input_field">
                <label for="payment_amount">Amount:</label>
                <input type="text" id="payment_amount" name="payment_amount" placeholder="Enter amount to pay" value="<?php echo($payment_amount)?>">
            </div>
            <div class="input_field">
                <label for="payment_code">Payment Code (e.g., Bank Reference Number, M-Pesa Code):</label>
                <input type="text" id="payment_code" name="payment_code" placeholder="Enter payment reference code" value="<?php echo($payment_code)?>">
            </div>
            <button type="submit" name="make_payment">Submit Payment</button>
        </form>
    </section>

      

    <!-- Payment History Section -->
    <section>
        <h2>Your Payment History</h2>
        <table>
            <tr>
                <!-- <th>House Number</th> -->
                <th>Amount Paid</th>
                <th>Payment Code</th>
                <th>Payment Date</th>
                
            </tr>
            <tr>
            <?php
                // Check if the query returned any results
                if (mysqli_num_rows($result) > 0) {
                    // Loop through the result set and display each row
                    while ($payment = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($payment['amount']) . "</td>";
                        echo "<td>" . htmlspecialchars($payment['payment_code']) . "</td>";
                        echo "<td>" . htmlspecialchars($payment['date_created']) . "</td>";

                    }
                } else {
                    // If no results are found, show a message
                    echo "<tr><td colspan='5'>No payments made yet.</td></tr>";
                }
            ?> 
            </tr>                       
            </table>
    </section>

</div>
<!-- Notifications Modal (Initially Hidden) -->
<div id="notifications-modal" style="display:none; position: fixed; top: 20%; right: 10%; background-color: #fff; border: 1px solid #ccc; padding: 20px; width: 300px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
    <h3>Unread Notifications</h3>
    <div id="notifications-list"></div>
    <button onclick="markNotificationsAsRead()">Mark All as Read</button>
</div>

<!-- Script Section -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Function to update notification count
    function updateNotificationCount() {
        $.ajax({
            url: 'notification_bell.php',  // Fetch unread count from the backend
            method: 'GET',
            success: function(data) {
                $('#badge').text(data);  // Update the badge with the unread count
            }
        });
    }

    // Show notifications when the bell icon is clicked
    $('#notification-bell').click(function() {
        $.ajax({
            url: 'get_notifications.php',  // Fetch the list of notifications
            method: 'GET',
            success: function(data) {
                $('#notifications-list').html(data);
                $('#notifications-modal').show();  // Display the notifications modal
            }
        });
    });

    // Mark notifications as read
    function markNotificationsAsRead() {
        $.ajax({
            url: 'mark_notifications_read.php',  // Mark notifications as read
            method: 'POST',
            success: function() {
                $('#notifications-modal').hide();
                updateNotificationCount();  // Re-fetch the unread count
            }
        });
    }

    // Initial update of notification count
    updateNotificationCount();
</script>

</body>
</html>
