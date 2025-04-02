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
if (isset($_POST['delete_house'])) {
    $house_id = $_POST['house_id'];

    // Delete the house record from the database
    $delete_query = "DELETE FROM houses WHERE id = '$house_id'";
    if (mysqli_query($db, $delete_query)) {
        echo "House deleted successfully.";
    } else {
        echo "Error deleting house: " . mysqli_error($db);
    }
}

// Query to get all houses
$query = "SELECT * FROM houses";
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
    <title>Houses List</title>
    <style>
        /* General styles */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

/* Container */
.container {
    width: 80%;
    margin: 30px auto;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}

/* Title */
h1 {
    color: #333;
}

/* Go back link */
a {
    text-decoration: none;
    font-size: 16px;
}

a:hover {
    text-decoration: underline;
}

/* Table styles */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: #fff;
}

th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #ff7200;
    color: white;
}

tr:nth-child(even) {
    background-color: #f2f2f2;
}

/* Buttons */
button, .btn-link {
    padding: 8px 12px;
    margin: 5px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
}

button {
    background-color: #ff7200;
    color: white;
}

button:hover {
    background-color: darkred;
}

.btn-link {
    display: inline-block;
    background-color: #ff7200;
    color: white;
    text-decoration: none;
    padding: 8px 12px;
}

.btn-link:hover {
    background-color: #ff7200;
}
</style>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="container">
        <p><a href="admin_dashboard.php" style="color: red;">Go back to dashboard</a></p>
        <h1>List of Houses</h1>

        <!-- Table to display house data -->
        <table border="1">
            <thead>
                <tr>
                    <th>House Number</th>
                    <th>House Type</th>
                    <th>Status</th>
                    <th>Owner</th>
                    <th>Action</th> <!-- Column for delete button -->
                    <th>Action</th> <!-- Column for update button -->
                </tr>
            </thead>
            <tbody>
                <?php
                // Check if the query returned any results
                if (mysqli_num_rows($result) > 0) {
                    // Loop through the result set and display each row
                    while ($house = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($house['house_number']) . "</td>";
                        echo "<td>" . htmlspecialchars($house['house_type']) . "</td>";
                        echo "<td>" . htmlspecialchars($house['status']) . "</td>";

                        // Fetch user details based on the user_id in the houses table
                        $user_id = $house['user_id']; // Assuming user_id is available
                        $user_query = "SELECT username FROM users WHERE id = '$user_id'";
                        $user_result = mysqli_query($db, $user_query);
                        if ($user_result && mysqli_num_rows($user_result) > 0) {
                            $user = mysqli_fetch_assoc($user_result);
                            echo "<td>" . htmlspecialchars($user['username']) . "</td>"; // Display the username
                        } else {
                            echo "<td>No user</td>"; // If no user is found
                        }

                        // Add a delete button with the house_id as a hidden input
                        echo "<td>";
                        echo "<form method='POST' action=''>";
                        echo "<input type='hidden' name='house_id' value='" . $house['id'] . "' />";
                        echo "<button type='submit' name='delete_house'>Delete</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "<td><a href='update_house.php?house_id=" . $house['id'] . "' class='btn-link'>Update</a></td>";

                        echo "</tr>";
                    }
                } else {
                    // If no results are found, show a message
                    echo "<tr><td colspan='5'>No houses found.</td></tr>";
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
