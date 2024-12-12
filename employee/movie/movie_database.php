<?php
session_start();
include('../../database_connection.php');

$errorMessage = null;

$MOV_NAME = '';
$MOV_LENGTH = '';
$MOV_GENRE = '';
$MOV_RATING = '';
$MOV_PRICE = '';
$CIN_ID = '';
$MOV_ID = '';

if (isset($_POST['insert'])) {
    $MOV_NAME = $mysqli->real_escape_string($_POST['fname']);
    $MOV_LENGTH = $_POST['mname'];
    $MOV_GENRE = $_POST['genre'];
    $MOV_RATING = $_POST['rating'];
    $MOV_PRICE = isset($_POST['price']) ? $_POST['price'] : '';
    $CIN_ID = $_POST['cinema'];
    $MOV_DATE = $_POST['mov_date'];

    $errorMessage = null;

    // Validate inputs
    if (empty($MOV_NAME) || empty($MOV_LENGTH) || empty($MOV_GENRE) || empty($MOV_RATING) || empty($MOV_PRICE) || empty($CIN_ID) || empty($MOV_DATE)) {
        $errorMessage = "All fields are required. Please fill in all the fields.";
    }

    if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $MOV_LENGTH)) {
        $errorMessage = "Invalid movie length: Please use the format HH:MM:SS.";
    }

    if (!is_numeric($MOV_PRICE) || $MOV_PRICE < 0) {
        $errorMessage = "Invalid price: Please enter a valid number for the price.";
    }

    if (!is_numeric($CIN_ID) || $CIN_ID < 0) {
        $errorMessage = "Invalid cinema number: Please enter a valid number for the cinema number.";
    }

    // Foreign key integrity
    if (!$errorMessage) {
        $cinemaCheckSql = "SELECT CIN_ID FROM CINEMA WHERE CIN_ID = $CIN_ID LIMIT 1";
        $cinemaResult = $mysqli->query($cinemaCheckSql);
        if ($cinemaResult->num_rows == 0) {
            $errorMessage = "The selected cinema does not exist in the database.";
        }
    }

    // Duplicate check
    if (!$errorMessage) {
        $checkDuplicateSql = "SELECT * FROM MOVIE WHERE MOV_NAME = '$MOV_NAME' AND MOV_LENGTH = '$MOV_LENGTH' AND MOV_GENRE = '$MOV_GENRE' LIMIT 1";
        $checkResult = $mysqli->query($checkDuplicateSql);
        if ($checkResult->num_rows > 0) {
            $errorMessage = "This movie already exists in the database.";
        }
    }

    // Schedule conflict check
    if (!$errorMessage) {
        $checkScheduleSql = "SELECT * FROM MOVIE WHERE CIN_ID = $CIN_ID AND MOV_DATE = '$MOV_DATE' LIMIT 1";
        $scheduleResult = $mysqli->query($checkScheduleSql);
        if ($scheduleResult->num_rows > 0) {
            $errorMessage = "A movie is already scheduled in this cinema on the selected date.";
        }
    }

    // Insert data if no errors
    if (!$errorMessage) {
        $MOV_LENGTH = "'$MOV_LENGTH'";
        $sql = "INSERT INTO movie_reservation_system.MOVIE (MOV_NAME, MOV_LENGTH, MOV_GENRE, MOV_RATING, MOV_PRICE, CIN_ID, MOV_DATE) 
                VALUES ('$MOV_NAME', $MOV_LENGTH, '$MOV_GENRE', '$MOV_RATING', '$MOV_PRICE', $CIN_ID, '$MOV_DATE')";
        if ($mysqli->query($sql)) {
            echo "<div style='color: green;'>Movie added successfully!</div>";
        } else {
            echo "<div style='color: red;'>Error: " . $mysqli->error . "</div>";
        }
    } else {
        echo "<div style='color: red;'>$errorMessage</div>";
    }
}else // Update Operation
if (isset($_POST['update'])) {
    $MOV_NAME = $mysqli->real_escape_string($_POST['fname']);
    $MOV_LENGTH = $_POST['mname'];
    $MOV_GENRE = $_POST['genre'];
    $MOV_RATING = $_POST['rating'];
    $MOV_PRICE = isset($_POST['price']) ? $_POST['price'] : '';
    $CIN_ID = $_POST['cinema'];
    $MOV_DATE = $_POST['mov_date'];
    $MOV_ID = $_POST['id'];

    $errorMessage = null;

    // Validate inputs
    if (empty($MOV_NAME) || empty($MOV_LENGTH) || empty($MOV_GENRE) || empty($MOV_RATING) || empty($MOV_PRICE) || empty($CIN_ID) || empty($MOV_DATE) || empty($MOV_ID)) {
        $errorMessage = "All fields are required. Please fill in all the fields.";
    } elseif (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $MOV_LENGTH)) {
        $errorMessage = "Invalid movie length: Please use the format HH:MM:SS.";
    } elseif (!is_numeric($MOV_PRICE) || $MOV_PRICE < 0) {
        $errorMessage = "Invalid price: Please enter a valid number for the price.";
    } elseif (!is_numeric($CIN_ID) || $CIN_ID < 0) {
        $errorMessage = "Invalid cinema number: Please enter a valid number for the cinema number.";
    } elseif (strlen($MOV_ID) !== 8 || !ctype_digit($MOV_ID)) {
        $errorMessage = "Invalid MOV_ID: Must be an 8-digit number.";
    }

    // Check if CIN_ID exists in the database
    if (!$errorMessage) {
        $cinemaCheckSql = "SELECT CIN_ID FROM CINEMA WHERE CIN_ID = $CIN_ID LIMIT 1";
        $cinemaResult = $mysqli->query($cinemaCheckSql);
        if ($cinemaResult->num_rows == 0) {
            $errorMessage = "The selected cinema does not exist in the database.";
        }
    }

    // Check if the record to update exists
    if (!$errorMessage) {
        $origin = "SELECT * FROM movie_reservation_system.MOVIE WHERE MOV_ID = '$MOV_ID'";
        $result = $mysqli->query($origin);
        if ($result->num_rows === 0) {
            $errorMessage = "The record to update does not exist.";
        }
    }

    // Update the database if no errors
    if (!$errorMessage) {
        $sql = "UPDATE movie_reservation_system.MOVIE 
                SET MOV_NAME='$MOV_NAME', MOV_LENGTH='$MOV_LENGTH', MOV_GENRE='$MOV_GENRE', MOV_RATING='$MOV_RATING', MOV_PRICE='$MOV_PRICE', CIN_ID='$CIN_ID', MOV_DATE='$MOV_DATE' 
                WHERE MOV_ID='$MOV_ID'";

        if ($mysqli->query($sql)) {
            echo "<div style='color: green;'>Data updated successfully!</div>";
        } else {
            echo "<div style='color: red;'>Error: " . $mysqli->error . "</div>";
        }
    } else {
        echo "<div style='color: red;'>$errorMessage</div>";
    }
}

// Delete Operation
if (isset($_POST['delete'])) {
    $MOV_ID = $_POST['id'];

    // Validate MOV_ID
    if (strlen($MOV_ID) !== 8 || !ctype_digit($MOV_ID)) {
        echo "<div style='color: red;'>Error: MOV_ID must be an 8-digit number.</div>";
        exit();
    }

    // Check if the record exists
    $checkSql = "SELECT * FROM movie_reservation_system.MOVIE WHERE MOV_ID = '$MOV_ID'";
    $checkResult = $mysqli->query($checkSql);
    if ($checkResult->num_rows === 0) {
        echo "<div style='color: red;'>Error: The record you are trying to delete does not exist.</div>";
        exit();
    }

    // Delete the record
    $deleteSql = "DELETE FROM movie_reservation_system.MOVIE WHERE MOV_ID = '$MOV_ID'";
    if ($mysqli->query($deleteSql)) {
        echo "<div style='color: green;'>Movie deleted successfully!</div>";
        header("Location: movie_database.php"); // Redirect to refresh the table
        exit();
    } else {
        echo "<div style='color: red;'>Error: " . $mysqli->error . "</div>";
    }
}

// Display existing movies from the database
$sql = "SELECT MOV_ID, MOV_NAME, MOV_LENGTH, MOV_GENRE, MOV_RATING, MOV_PRICE, CIN_ID FROM MOVIE";
$result = $mysqli->query($sql);
?>

<!-- HTML Form for movie insertion -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="movie_database.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Varela+Round&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="nav-bar">
        <div class="profile">
            <div class="avatar"></div>
            <div class="info">
                <h3 class="emp-name">
                    <?php echo htmlspecialchars($_SESSION['EMP_FNAME']); ?>
                </h3>
                <h4 class="emp-id">
                    <?php echo htmlspecialchars($_SESSION['EMP_LNAME']); ?>
                </h4>
            </div>
        </div>
        <h4 class="label">Databases</h4>
        <a href="">
            <div class="tab">
                <i class="bi bi-film"></i>
                <h3 class="tab-label">Movies</h3>
            </div>
        </a>
        <a href="../transactions/trans_database.php">
            <div class="tab">
                <i class="bi bi-bank2"></i>
                <h3 class="tab-label">Transactions</h3>
            </div>
        </a>
        <a href="../tickets/tickets.php">
            <div class="tab">
                <i class="bi bi-ticket-perforated-fill"></i>
                <h3 class="tab-label">Tickets</h3>
            </div>
        </a>

        <h4 class="label">Account Options</h4>
        <a href="../../login/logout.php">
            <div class="tab">
                <i class="bi bi-box-arrow-left"></i>
                <h3 class="tab-label">Log Out</h3>
            </div>
        </a>
    </nav>

    <div class="movie-database">
        <h1 class="title">MOVIE DATABASE</h1>
        <form action="" class="insert-form" method="POST">
            <div class="input">
                <label for="fname">Movie Name</label>
                <input type="text" name="fname">
            </div>

            <div class="input">
                <label for="mname">Movie Length</label>
                <input type="text" name="mname" placeholder="HH:MM:SS">
            </div>

            <div class="input">
                <label for="genre">Movie Genre</label>
                <select name="genre" id="genre">
                    <option value="Action">Action</option>
                    <option value="Horror">Horror</option>
                    <option value="Drama">Drama</option>
                    <option value="Science Fiction">Science Fiction</option>
                    <option value="Thriller">Thriller</option>
                    <option value="Romance">Romance</option>
                    <option value="Comedy">Comedy</option>
                    <option value="Crime">Crime</option>
                    <option value="Family">Family</option>
                    <option value="Anime">Anime</option>
                </select>
            </div>

            <div class="input">
                <label for="rating">Movie Rating</label>
                <select name="rating" id="rating">
                    <option value="G">G</option>
                    <option value="PG">PG</option>
                    <option value="R-13">R-13</option>
                    <option value="R-16">R-16</option>
                    <option value="R-18">R-18</option>
                    <option value="X">X</option>
                </select>
            </div>

            <div class="input">
                <label for="price">Movie Price</label>
                <input type="text" name="price">
            </div>

            <div class="input">
                <label for="mov_date">Movie Date</label>
                <input type="date" name="mov_date" min="<?php echo date('Y-m-d'); ?>">
            </div>



            <div class="input">
                <label for="cinema">Cinema</label>
                <input type="text" name="cinema">
            </div>

            <input type="submit" name="insert" value="Insert">

            <div class="input">
                <label for="id">MOV_ID</label>
                <input type="text" name="id">
            </div>

            <input type="submit" name="update" value="Update">
            <input type="submit" name="delete" value="Delete">

        </form>

        <div class="table-container">
            <table>
                <tr>
                    <th>MOV_ID</th>
                    <th>MOV_NAME</th>
                    <th>MOV_LENGTH</th>
                    <th>MOV_GENRE</th>
                    <th>MOV_RATING</th>
                    <th>MOV_PRICE</th>
                    <th>CIN_ID</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo sprintf('%08d', $row['MOV_ID']); ?></td>
                    <td><?php echo $row['MOV_NAME']; ?></td>
                    <td><?php echo $row['MOV_LENGTH']; ?></td>
                    <td><?php echo $row['MOV_GENRE']; ?></td>
                    <td><?php echo $row['MOV_RATING']; ?></td>
                    <td><?php echo $row['MOV_PRICE']; ?></td>
                    <td><?php echo $row['CIN_ID']; ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</body>
</html>
