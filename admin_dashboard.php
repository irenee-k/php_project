<?php
include('server.php');

// Query to get all payments
$query = "SELECT * FROM payments";
$result = mysqli_query($db, $query);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($db));
}
// Fetch all payments with user details
$query = "SELECT payments.*, users.username FROM payments 
          LEFT JOIN users ON payments.user_id = users.id";
$result = mysqli_query($db, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($db));
}

// Handle Register House
if (isset($_POST['reg_house'])) {
    $house_number = mysqli_real_escape_string($db, $_POST['house_number']);
    $house_type = mysqli_real_escape_string($db, $_POST['house_type']);
    $house_status = mysqli_real_escape_string($db, $_POST['house_status']);
    $user_id = !empty($_POST['user_id']) ? mysqli_real_escape_string($db, $_POST['user_id']) : 'NULL';

    $query = "INSERT INTO houses (house_number, house_type, status, user_id) 
              VALUES ('$house_number', '$house_type', '$house_status', $user_id)";
    mysqli_query($db, $query);
}

// Handle Notification Form Submission
if (isset($_POST['send_notification'])) {
    $message = mysqli_real_escape_string($db, $_POST['message']);
    $user_id = mysqli_real_escape_string($db, $_POST['user_id']);
    $notification_status = 'unread';
    
    // Insert notification into the database
    $notification_query = "INSERT INTO notifications (user_id, message, status) 
                           VALUES ('$user_id', '$message', '$notification_status')";
    $notification_result = mysqli_query($db, $notification_query);
    
    if ($notification_result) {
        $_SESSION['success'] = "Notification sent successfully!";
    } else {
        $_SESSION['error'] = "Failed to send notification. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<link rel="stylesheet" href="style.css">
<body>

<?php
// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You must log in first";
    header('location: login.php');
    exit;
}

// Check if the user is logged out and destroy session
if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['username']);
    header("location: login.php");
    exit;
}

// Check if the user is an admin (assuming session variable 'role' is set)
if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'Admin') {
        // Redirect admin to admin dashboard
        header('location: admin_dashboard.php');
        exit;
    }
}
?>

<header>
    <h1>Admin Dashboard</h1>
</header>

<!-- Sidebar Section -->
<div class="sidebar">
    <p style="text-align: center; font-size: 1.3em; font-weight: bold;">Admin Panel</p>
    <a href="index.php?logout=1">Logout</a>
    <a href="houses.php">Manage Houses</a>
    <a href="payments.php">Manage Payments</a>
</div>

<!-- Main Content Section -->
<div class="main-content">
    <!-- Register House Form -->
    <section>
        <h2>REGISTER HOUSE</h2>
        <?php include('errors.php'); ?>
        <form action="admin_dashboard.php" method="post" class="house">
            <div class="input_field">
                <label for="house_number">House Number:</label>
                <input type="text" id="house_number" name="house_number" placeholder="Enter House Number" value="<?php echo $house_number; ?>">
            </div>
            <div class="input_field">
                <select name="house_type">
                    <option value="">--Select House Type--</option>
                    <option value="1BHK">1BHK</option>
                    <option value="2BHK">2BHK</option>
                    <option value="3BHK">3BHK</option>
                    <option value="Apartment">Apartment</option>
                </select>
            </div>
            <div class="input_field">
                <select name="house_status">
                    <option value="">--Select Status--</option>
                    <option value="Available">Available</option>
                    <option value="Rented">Rented</option>
                </select>
                <select name="user_id">
                <option value="">-- Assign to Tenant (Optional) --</option>
                <?php 
                $user_result = mysqli_query($db, "SELECT id, username FROM users");
                while ($user = mysqli_fetch_assoc($user_result)) {
                    echo "<option value='{$user['id']}'>{$user['username']}</option>";
                }
                ?>
            </select>
            </div>
            <button class="submit" type="submit" name="reg_house">Add House</button>
        </form>
    </section>
    

    <!-- Send Notification Section -->
    <section>
        <h2>Send Notification</h2>
        <?php include('errors.php'); ?>
        <form action="admin_dashboard.php" method="post" class="send-notification">
            <div class="input_field">
                <label for="user_id">Select Tenant:</label>
                <select name="user_id" required>
                    <option value="">--Select Tenant--</option>
                    <?php
                    // Fetch all users (tenants)
                    $user_query = "SELECT id, username FROM users";
                    $user_result = mysqli_query($db, $user_query);
                    while ($user = mysqli_fetch_assoc($user_result)) {
                        echo "<option value='" . $user['id'] . "'>" . htmlspecialchars($user['username']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="input_field">
                <label for="message">Notification Message:</label>
                <textarea name="message" id="message" rows="4" required></textarea>
            </div>
            <button class="submit" type="submit" name="send_notification">Send Notification</button>
        </form>
    </section>

    <!-- Payment History Section -->
    <section>
        <h2>All Payments</h2>
        <table>
            <tr>
                <th>Tenant Name</th>
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
                        // Fetch user details based on the user_id in the payments table
                        $user_id = $payment['user_id']; // Assuming user_id is available
                        $user_query = "SELECT username FROM users WHERE id = '$user_id'";
                        $user_result = mysqli_query($db, $user_query);
                        if ($user_result && mysqli_num_rows($user_result) > 0) {
                            $user = mysqli_fetch_assoc($user_result);
                            echo "<td>" . htmlspecialchars($user['username']) . "</td>"; // Display the username
                        } else {
                            echo "<td>No user</td>"; // If no user is found
                        }
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

</body>
</html>
