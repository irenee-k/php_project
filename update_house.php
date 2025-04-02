<?php include('server.php') ?>
<?php

// Initialize error array
$errors = array();

// Fetch the house details
if (isset($_GET['house_id'])) {
    $house_id = $_GET['house_id'];
    $query = "SELECT * FROM houses WHERE id = '$house_id'";
    $result = mysqli_query($db, $query);

    if (!$result || mysqli_num_rows($result) == 0) {
        die("House not found.");
    }

    $house = mysqli_fetch_assoc($result);
}

// Update the house details
if (isset($_POST['update_house'])) {
    $house_number = mysqli_real_escape_string($db, $_POST['house_number']);
    $house_type = mysqli_real_escape_string($db, $_POST['house_type']);
    $status = mysqli_real_escape_string($db, $_POST['status']);

    // Update query
    $update_query = "UPDATE houses SET house_number = '$house_number', house_type = '$house_type', status = '$status' WHERE id = '$house_id'";
    if (mysqli_query($db, $update_query)) {
        echo "House updated successfully.";
        header("Location: houses.php"); // Redirect to the houses list page
        exit;
    } else {
        echo "Error updating house: " . mysqli_error($db);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update House</title>
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
    width: 50%;
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
    margin-bottom: 20px;
}

/* Go back link */
a {
    text-decoration: none;
    font-size: 16px;
    color: red;
}

a:hover {
    text-decoration: underline;
}

/* Form styles */
form {
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Input fields */
.input_field {
    width: 80%;
    margin-bottom: 15px;
    text-align: left;
}

.input_field label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}

.input_field input,
.input_field select {
    width: 100%;
    padding: 8px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    outline: none;
}

.input_field input:focus,
.input_field select:focus {
    border-color: #007bff;
}

/* Button */
button {
    padding: 10px 15px;
    font-size: 16px;
    background-color: #ff7200;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background-color: darkgreen;
}
</style>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="container">
        <p><a href="houses.php" style="color: red;">Go back to Houses List</a></p>
        <h1>Update House Details</h1>

        <!-- Form to update house details -->
        <form method="POST" action="">
            <div class="input_field">
                <label for="house_number">House Number</label>
                <input type="text" name="house_number" value="<?php echo htmlspecialchars($house['house_number']); ?>" required>
            </div>

            <div class="input_field">
                <label for="house_type">House Type</label>
                <select name="house_type" required>
                    <option value="1BHK" <?php echo ($house['house_type'] == '1BHK') ? 'selected' : ''; ?>>1BHK</option>
                    <option value="2BHK" <?php echo ($house['house_type'] == '2BHK') ? 'selected' : ''; ?>>2BHK</option>
                    <option value="3BHK" <?php echo ($house['house_type'] == '3BHK') ? 'selected' : ''; ?>>3BHK</option>
                    <option value="Apartment" <?php echo ($house['house_type'] == 'Apartment') ? 'selected' : ''; ?>>Apartment</option>
                </select>
            </div>

            <div class="input_field">
                <label for="status">Status</label>
                <select name="status" required>
                    <option value="Available" <?php echo ($house['status'] == 'Available') ? 'selected' : ''; ?>>Available</option>
                    <option value="Rented" <?php echo ($house['status'] == 'Rented') ? 'selected' : ''; ?>>Rented</option>
                </select>
            </div>

            <div class="input_field">
                <button type="submit" name="update_house" class="btn-link">Update</button>
            </div>
        </form>
    </div>
</body>
</html>

<?php
// Close the database connection
mysqli_close($db);
?>
