<?php
session_start();
include('../database_connection.php');

$errorMessage = null;

if (!isset($_SESSION['EMP_FNAME']) && !isset($_SESSION['EMP_ID'])) {
    header('Location: ../login.php');
    exit();
}

$sql = "SELECT MOV_NAME, MOV_PRICE, CIN_ID FROM MOVIE";
$result = $mysqli->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Movie Details</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="emp_database.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Varela+Round&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="main.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
  
    <!-- Sidebar -->
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
                        echo htmlspecialchars(string: $_SESSION['EMP_LNAME']);
                        ?>
                    </h4>
                </div>
            </div>
            <h4 class="label">Actions</h4>
            <a href="">
                <div class="tab">
                    <i class="bi bi-ticket-perforated-fill"></i>
                    <h3 class="tab-label">Movies</h3>
                </div>
            </a>

            <h4 class="label">Account Options</h4>
            <a href="../login/logout.php">
                <div class="tab">
                    <i class="bi bi-box-arrow-left"></i>
                    <h3 class="tab-label">Log Out</h3>
                </div>
            </a>
        </nav>
</div>
      
      
    </div>

    <!-- Main Content -->
    <div class="main">
      <h1>Movie Details</h1>
      
      <table>
        <thead>
          <tr>
            <th>Movie Name</th>
            <th>Price</th>
            <th>Cinema</th>
          </tr>
        </thead>
        <tbody>
          <?php
            // Loop through the result set and display each movie
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo '<td><a href="../transaction/transaction.php?cin_id=' . $row['CIN_ID'] . '">' . htmlspecialchars($row['MOV_NAME']) . '</a></td>';
                echo "<td>" . htmlspecialchars($row['MOV_PRICE']) . "</td>";
                echo "<td>" . htmlspecialchars($row['CIN_ID']) . "</td>";
                echo "</tr>";
            }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</body> 
</html>