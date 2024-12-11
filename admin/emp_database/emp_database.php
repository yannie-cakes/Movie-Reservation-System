<?php
session_start();
include ('../database_connection.php');

$errorMessage = null;

$EMP_FNAME = '';
$EMP_MNAME = '';
$EMP_LNAME = '';
$EMP_ROLE = '';

$pattern = "/^[a-zA-Z\s]+$/";   

if (!isset($_SESSION['EMP_FNAME']) && !isset($_SESSION['EMP_ID'])) {
    header('Location: ../login.php');
    exit();
}

if(isset($_POST['insert'])){
    $EMP_FNAME = $_POST['fname'];
    $EMP_MNAME = $_POST['mname'];
    $EMP_LNAME = $_POST['lname'];
    $EMP_ROLE = $_POST['role'];

    if(preg_match($pattern, $EMP_FNAME) && preg_match($pattern, $EMP_LNAME)){
        if(empty($EMP_MNAME) || preg_match($pattern, $EMP_MNAME)) {
            // Use NULL for empty middle name
            $EMP_MNAME = empty($EMP_MNAME) ? 'NULL' : "'$EMP_MNAME'";
            
            $sql = "INSERT INTO movie_reservation_system.EMPLOYEE (EMP_FNAME, EMP_MNAME, EMP_LNAME, EMP_ROLE) 
                    VALUES ('$EMP_FNAME', $EMP_MNAME, '$EMP_LNAME', '$EMP_ROLE')";
            
            if($mysqli->query($sql)) {
                echo "<div style='color: green;'>Employee added successfully!</div>";
            } else {
                echo "<div style='color: red;'>Error: " . $mysqli->error . "</div>";
            }
        } else {
            $errorMessage = "Invalid middle name: only letters and spaces are allowed.";
        }
    } else {
        $errorMessage = "Invalid name: only letters and spaces are allowed.";
    }
    
    if ($errorMessage) {
        echo "<div style='color: red;'>$errorMessage</div>";
    }
}


$sql = "SELECT EMP_ID, EMP_FNAME, EMP_MNAME, EMP_LNAME, EMP_ROLE FROM EMPLOYEE";
$result = $mysqli->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="emp_database.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Varela+Round&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="nav-bar">
        <div class="profile">
            <div class="avatar"></div>
            <div class="info">
                <h3 class="emp-name">
                    <?php
                    echo htmlspecialchars($_SESSION['EMP_FNAME']);
                    ?>
                </h3>
                <h4 class="emp-id"><?php
                    echo htmlspecialchars($_SESSION['EMP_LNAME']);
                    ?>
                </h4>
            </div>
        </div>
        <h4 class="label">Databases</h4>
        <a href="">
            <div class="tab">
                <i class="bi bi-people-fill"></i>
                <h3 class="tab-label">Employees</h3>
            </div>
        </a>
        <a href="">
            <div class="tab">
                <i class="bi bi-bank2"></i>
                <h3 class="tab-label">Transactions</div>
            </div>
        </a>
        <a href="">
            <div class="tab">
                <i class="bi bi-ticket-perforated-fill"></i>
                <h3 class="tab-label">Tickets</h3>
            </div>
        </a>

        <h4 class="label">Account Options</h4>
        <a href="logout.php">
            <div class="tab">
                <i class="bi bi-box-arrow-left"></i>
                <h3 class="tab-label">Log Out</h3>
            </div>
        </a>
    </nav>

    <div class="emp-database">
        <h1 class="title">EMPLOYEE DATABASE</h1>
        <form action="" class="insert-form" method="POST">
            <div class="input">
                <label for="fname">First Name</label>
                <input type="text" name="fname" required>
            </div>
            <div class="input">
                <label for="mname">Middle Name</label>
                <input type="text" name="mname">
            </div>
            <div class="input">
                <label for="lname">Last Name</label>
                <input type="text" name="lname" required>
            </div>
            <div class="input">
                <label for="role">Role</label>
                <select name="role" id="role" required>
                    <option value="Admin">Admin</option>
                    <option value="Employee">Employee</option>
                </select>
            </div>

            <input type="submit" name="insert" value="Insert">
        </form>
        <div class="table-container">
        <table>
            <tr>
                <th>EMP_ID</th>
                <th>EMP_FNAME</th>
                <th>EMP_MNAME</th>
                <th>EMP_LNAME</th>
                <th>EMP_ROLE</th>
                <th>ACTION</th>
            </tr>
            <?php
            while($row=$result->fetch_assoc())
            {
        ?>
        <tr>
            <td><?php echo sprintf('%08d', $row['EMP_ID']); ?></td>
            <td><?php echo $row['EMP_FNAME'];?></td>
            <td><?php echo $row['EMP_MNAME'];?></td>
            <td><?php echo $row['EMP_LNAME'];?></td>
            <td><?php echo $row['EMP_ROLE'];?></td>
            <td><a class="action" href="update.php?id=<?php echo $row['EMP_ID']; ?>">Update</a> | <a class="action" href="delete.php?id=<?php echo $row['EMP_ID']; ?>">Delete</a></td>
        </tr>
        <?php } ?>
        </table>
    </div>
    </div>
</body>
</html>
