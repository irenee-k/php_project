 <?php
session_start();

// initializing variables
$username = "";
$email    = "";
$password_1 = "";
$password_2 = "";
$house_number = "";
$house_type = "";
$status = "";
$user_id ="";
$payment_amount = "";
$payment_date = "";
$payment_code = "";
$amount ="";
$date_created ="";
$errors = array(); 

// connect to the database
$db = mysqli_connect('localhost', 'root', '', 'project');





// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receive all input values from the form
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  if (empty($username)) { array_push($errors, "Username is required"); }
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($password_1)) { array_push($errors, "Password is required"); }
  if ($password_1 != $password_2) {
    array_push($errors, "The two passwords do not match");
  }

  // first check the database to make sure 
  // a user does not already exist with the same username and/or email
  $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);
  
  if ($user) { // if user exists
    if ($user['username'] === $username) {
      array_push($errors, "Username already exists");
    }

    if ($user['email'] === $email) {
      array_push($errors, "Email already exists");
    }
  }

  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) {
    $password = md5($password_1); //encrypt the password before saving in the database

    $query = "INSERT INTO users (username, email, password) 
              VALUES('$username', '$email', '$password')";
    mysqli_query($db, $query);
    $_SESSION['username'] = $username;
    $_SESSION['success'] = "You are now logged in";
    header('location: index.php');
  }
}


// LOGIN USER
if (isset($_POST['login_user'])) {
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  if (empty($username)) {
    array_push($errors, "Username is required");
  }
  if (empty($password)) {
    array_push($errors, "Password is required");
  }

  if (count($errors) == 0) {
    $password = md5($password); // Hash the password to compare
    $query = "SELECT * FROM users WHERE username='$username' AND password='$password' LIMIT 1";
    $results = mysqli_query($db, $query);
    if (mysqli_num_rows($results) == 1) {
      $user = mysqli_fetch_assoc($results); // Fetch the user data

      // Set session variables
      $_SESSION['username'] = $username;
      $_SESSION['success'] = "You are now logged in";

      $_SESSION['user_id'] = $user['id'];

      // Check the user's role and redirect accordingly
    if ($user['role'] === 'Admin') {  // Ensure that 'Admin' is capitalized exactly as in your database
    header('location: admin_dashboard.php');
    exit();  // Don't forget to exit after the redirect
} else {
    header('location: index.php');
    exit();
}}}}

///====ADDING HOUSES BY ADMIN====///
if (isset($_POST['reg_house'])) {

  // Receive form values
  $house_number = mysqli_real_escape_string($db, $_POST['house_number']);
  $house_type = $_POST['house_type'];
  $status = $_POST['house_status'];

  // Validate input fields
  if (empty($house_number)) { array_push($errors, "House number is required"); }
  if (empty($house_type)) { array_push($errors, "House type is required"); }

  // Check if user is logged in and has a user_id
  if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id']; // Get the logged-in user's ID
  } else {
    array_push($errors, "You must be logged in to add a house.");
  }

  // Check if house number already exists in the database
  if (count($errors) == 0) {
    $house_check_query = "SELECT * FROM houses WHERE house_number='$house_number' LIMIT 1";
    $result = mysqli_query($db, $house_check_query);
    $house = mysqli_fetch_assoc($result);

    if ($house) { // If the house already exists
      array_push($errors, "The house already exists");
    }
    

  }

  // If no errors, insert the house data
  if (count($errors) == 0) {
    // Insert house details along with user_id
    $query = "INSERT INTO houses (house_number, house_type, status, user_id) 
              VALUES('$house_number', '$house_type', '$status', '$user_id')";

    // Execute the query and check for errors
    if (mysqli_query($db, $query)) {
      $_SESSION['house_number'] = $house_number;
      $_SESSION['success'] = "House Added";
      header('location: admin_dashboard.php');
    } else {
      array_push($errors, "Error: " . mysqli_error($db)); // Capture MySQL errors
    }
  }
}

// Display any errors
if (count($errors) > 0) {
  foreach ($errors as $error) {
    echo $error . "<br>";
  }
}

///====USER PAYMENTS====///
if (isset($_POST['make_payment'])) {

  // Receive form values
  $payment_amount = mysqli_real_escape_string($db, $_POST['payment_amount']);
  $payment_code = mysqli_real_escape_string($db, $_POST['payment_code']);

  // Validate input fields
  if (empty($payment_amount)) { array_push($errors, "Amount number is required"); }
   if (empty($payment_code)) { array_push($errors, "Payment code type is required"); }

  // Check if user is logged in and has a user_id
  if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id']; // Get the logged-in user's ID
  } else {
    array_push($errors, "You must be logged in to make payments.");
  }

  // Check if payment code already exists in the database
  if (count($errors) == 0) {
    $payment_check_query = "SELECT * FROM payments WHERE payment_code='$payment_code' LIMIT 1";
    $result = mysqli_query($db, $payment_check_query);
    $payment = mysqli_fetch_assoc($result);

    if ($payment) { // If the payment code already exists
      array_push($errors, "Payment with similar code exists.");
    }
    

  }

  // If no errors, insert the house data
  if (count($errors) == 0) {
    // Insert house details along with user_id
    $query = "INSERT INTO payments (user_id, amount, payment_code) 
              VALUES('$user_id', '$payment_amount', '$payment_code')";

    // Execute the query and check for errors
    if (mysqli_query($db, $query)) {
      $_SESSION['payment_code'] = $payment_code;
      $_SESSION['success'] = "Payment added successfully.";
      header('location: index.php');
    } else {
      array_push($errors, "Error: " . mysqli_error($db)); // Capture MySQL errors
    }
  }
}



?>
