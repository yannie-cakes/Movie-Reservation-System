<?php
session_start();
include('../../database_connection.php');

$EMP_ID = $_GET['id'] ?? null;
$errorMessage = null;
$successMessage = null;

// Fetch existing employee details
if ($EMP_ID) {
    // Ensure EMP_ID is a number
    if (!is_numeric($EMP_ID)) {
        die("Invalid Employee ID.");
    }

    $sql = "SELECT * FROM movie_reservation_system.EMPLOYEE WHERE EMP_ID = $EMP_ID";
    $result = $mysqli->query($sql);
    $employee = $result->fetch_assoc();
    if (!$employee) {
        die("Employee not found.");
    }
}

// Handle form submission for update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect input values
    $EMP_ID = $_POST['id'];
    $EMP_FNAME = trim($_POST['fname']);
    $EMP_MNAME = trim($_POST['mname']);
    $EMP_LNAME = trim($_POST['lname']);
    $EMP_ROLE = trim($_POST['role']);

    $pattern = "/^[a-zA-Z\s]+$/";

    // Validate input
    if (preg_match($pattern, $EMP_FNAME) && preg_match($pattern, $EMP_LNAME)) {
        if (empty($EMP_MNAME) || preg_match($pattern, $EMP_MNAME)) {
            // Manually sanitize the input to avoid SQL injection
            $EMP_FNAME = htmlspecialchars($EMP_FNAME, ENT_QUOTES, 'UTF-8');
            $EMP_MNAME = $EMP_MNAME ? htmlspecialchars($EMP_MNAME, ENT_QUOTES, 'UTF-8') : null;
            $EMP_LNAME = htmlspecialchars($EMP_LNAME, ENT_QUOTES, 'UTF-8');
            $EMP_ROLE = htmlspecialchars($EMP_ROLE, ENT_QUOTES, 'UTF-8');

            // Create the UPDATE query
            $sql = "UPDATE movie_reservation_system.EMPLOYEE 
                    SET EMP_FNAME = '$EMP_FNAME', 
                        EMP_MNAME = '$EMP_MNAME', 
                        EMP_LNAME = '$EMP_LNAME', 
                        EMP_ROLE = '$EMP_ROLE' 
                    WHERE EMP_ID = $EMP_ID";

            // Execute the query
            if ($mysqli->query($sql)) {
                $successMessage = "Employee updated successfully!";
                header('Location: emp_database.php'); // Redirect back to employee database page
                exit();
            } else {
                $errorMessage = "Error updating employee: " . $mysqli->error;
            }
        } else {
            $errorMessage = "Invalid middle name: only letters and spaces are allowed.";
        }
    } else {
        $errorMessage = "Invalid name: only letters and spaces are allowed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Employee</title>
    <link rel="stylesheet" href="emp_database.css">
</head>
<body>
    <div class="update-form-container">
        <h1>Update Employee</h1>
        <?php if ($errorMessage): ?>
            <div style="color: red;"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        <?php if ($successMessage): ?>
            <div style="color: green;"><?php echo $successMessage; ?></div>
        <?php endif; ?>

        <form action="update.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($EMP_ID); ?>">

            <div class="input">
                <label for="fname">First Name</label>
                <input type="text" name="fname" value="<?php echo htmlspecialchars($employee['EMP_FNAME']); ?>" required>
            </div>
            <div class="input">
                <label for="mname">Middle Name</label>
                <input type="text" name="mname" value="<?php echo htmlspecialchars($employee['EMP_MNAME']); ?>">
            </div>
            <div class="input">
                <label for="lname">Last Name</label>
                <input type="text" name="lname" value="<?php echo htmlspecialchars($employee['EMP_LNAME']); ?>" required>
            </div>
            <div class="input">
                <label for="role">Role</label>
                <select name="role" id="role" required>
                    <option value="Admin" <?php echo $employee['EMP_ROLE'] === 'Admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="Employee" <?php echo $employee['EMP_ROLE'] === 'Employee' ? 'selected' : ''; ?>>Employee</option>
                </select>
            </div>

            <input type="submit" value="Update">
        </form>
    </div>
</body>
</html>
