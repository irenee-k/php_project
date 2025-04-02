<?php
// connect to the database

// Database credentials
$host = 'localhost';  // Change this to your database host
$user = 'root';  // Change this to your database username
$password = '';  // Change this to your database password
$dbname = 'project';  // Change this to your database name

// Create connection
$db = mysqli_connect($host, $user, $password, $dbname);

// Check connection
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Start the session
session_start();

// Initialize error array
$errors = array();

// Check if a delete request has been made
if (isset($_POST['delete_payment'])) {
    $payment_id = $_POST['payment_id'];

    // Delete the payment record from the database
    $delete_query = "DELETE FROM payments WHERE id = '$payment_id'";
    if (mysqli_query($db, $delete_query)) {
        echo "Payment deleted successfully.";
    } else {
        echo "Error deleting payment: " . mysqli_error($db);
    }
}

// Query to get all payments
$query = "SELECT * FROM payments";
$result = mysqli_query($db, $query);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($db));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments List</title><style>
        body {
            background-color: #fff;
            font-family: Arial, sans-serif;
            color: #000;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #ff7200;
        }
        a {
            color: #ff7200;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #000;
        }
        th {
            background-color: #ff7200;
            color: #fff;
        }
        td {
            background-color: #f4f4f4;
        }
        button {
            background-color: #ff7200;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        button:hover {
            background-color: #e56300;
        }
    </style>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="container">
        <p><a href="admin_dashboard.php" style="color: red;">Go back to dashboard</a></p>
        <h1>List of Payments</h1>

        <!-- Table to display payment data -->
        <table border="1">
            <thead>
                <tr>
                    <th>User Id</th>
                    <th>Amount</th> <!-- Corrected the missing closing tag -->
                    <th>Date Entered</th>
                    <th>Action</th> <!-- Column for delete button -->
                </tr>
            </thead>
            <tbody>

            <?php
                // Check if the query returned any results
                if (mysqli_num_rows($result) > 0) {
                    // Loop through the result set and display each row
                    while ($payment = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($payment['user_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($payment['amount']) . "</td>";
                        echo "<td>" . htmlspecialchars($payment['date_created']) . "</td>";

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

                        // Add a delete button with the payment_id as a hidden input
                        echo "<td>";
                        echo "<form method='POST' action=''>";
                        echo "<input type='hidden' name='payment_id' value='" . $payment['id'] . "' />";
                        echo "<button type='submit' name='delete_payment'>Delete</button>";
                        echo "</form>";
                        echo "</td>";

                        echo "</tr>";
                    }
                } else {
                    // If no results are found, show a message
                    echo "<tr><td colspan='4'>No payments found.</td></tr>";  // Adjusted colspan

                }
            ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// Close the database connection
mysqli_close($db);
?>
