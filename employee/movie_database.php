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
    $MOV_PRICE = isset($_POST['price']) ? $_POST['price'] : ''; // Ensure MOV_PRICE is set
    $CIN_ID = $_POST['cinema'];
    $MOV_DATE = $_POST['mov_date']; // Assuming you're getting the movie date as a POST parameter

    // e. Check if required fields are blank
    if (empty($MOV_NAME) || empty($MOV_LENGTH) || empty($MOV_GENRE) || empty($MOV_RATING) || empty($MOV_PRICE) || empty($CIN_ID) || empty($MOV_DATE)) {
        $errorMessage = "All fields are required. Please fill in all the fields.";
    }

    // b. Validate MOV_LENGTH (HH:MM:SS format)
    if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $MOV_LENGTH)) {
        $errorMessage = "Invalid movie length: Please use the format HH:MM:SS.";
    }

    // a. Validate MOV_PRICE (should be numeric)
    if (!is_numeric($MOV_PRICE) || $MOV_PRICE < 0) {
        $errorMessage = "Invalid price: Please enter a valid number for the price.";
    }

    if (!is_numeric($CIN_ID) || $CIN_ID < 0) {
        $errorMessage = "Invalid cinema number: Please enter a valid number for the cinema number.";
    }

    // c. Check if CIN_ID exists in Cinema table (foreign key integrity)
    if (!$errorMessage) {
        $cinemaCheckSql = "SELECT CIN_ID FROM CINEMA WHERE CIN_ID = $CIN_ID LIMIT 1";
        $cinemaResult = $mysqli->query($cinemaCheckSql);
        if ($cinemaResult->num_rows == 0) {
            $errorMessage = "The selected cinema does not exist in the database.";
        }
    }

    // d. Check if the movie already exists (duplicate check)
    if (!$errorMessage) {
        $checkDuplicateSql = "SELECT * FROM MOVIE WHERE MOV_NAME = '$MOV_NAME' AND MOV_LENGTH = '$MOV_LENGTH' AND MOV_GENRE = '$MOV_GENRE' LIMIT 1";
        $checkResult = $mysqli->query($checkDuplicateSql);
        if ($checkResult->num_rows > 0) {
            $errorMessage = "This movie already exists in the database.";
        }
    }

    // f. Check if a movie is already scheduled in the same cinema for the same day
    if (!$errorMessage) {
        $checkScheduleSql = "SELECT * FROM MOVIE WHERE CIN_ID = $CIN_ID AND MOV_DATE = '$MOV_DATE' LIMIT 1";
        $scheduleResult = $mysqli->query($checkScheduleSql);
        if ($scheduleResult->num_rows > 0) {
            $errorMessage = "A movie is already scheduled in this cinema on the selected date.";
        }
    }

    // Proceed with inserting the data if no error messages
    if (!$errorMessage) {
        $MOV_LENGTH = "'$MOV_LENGTH'"; // wrap MOV_LENGTH in quotes for SQL query

        $sql = "INSERT INTO movie_reservation_system.MOVIE (MOV_NAME, MOV_LENGTH, MOV_GENRE, MOV_RATING, MOV_PRICE, CIN_ID, MOV_DATE) 
                VALUES ('$MOV_NAME', $MOV_LENGTH, '$MOV_GENRE', '$MOV_RATING', '$MOV_PRICE', $CIN_ID, '$MOV_DATE')";

        if ($mysqli->query($sql)) {
            echo "<div style='color: green;'>Movie added successfully!</div>";
        } else {
            echo "<div style='color: red;'>Error: " . $mysqli->error . "</div>";
        }
    }

    if ($errorMessage) {
        echo "<div style='color: red;'>$errorMessage</div>";
    }
}else if (isset($_POST['update'])) {
    
    $MOV_NAME = $mysqli->real_escape_string($_POST['fname']);
    $MOV_LENGTH = $_POST['mname'];
    $MOV_GENRE = $_POST['genre'];
    $MOV_RATING = $_POST['rating'];
    $MOV_PRICE = isset($_POST['price']) ? $_POST['price'] : ''; // Ensure MOV_PRICE is set
    $CIN_ID = $_POST['cinema'];
    $MOV_DATE = $_POST['mov_date']; // Assuming you're getting the movie date as a POST parameter
    $MOV_ID = $_POST['id'];

    // e. Check if required fields are blank
    if (empty($MOV_NAME) || empty($MOV_LENGTH) || empty($MOV_GENRE) || empty($MOV_RATING) || empty($MOV_PRICE) || empty($CIN_ID) || empty($MOV_DATE) || empty($MOV_ID)) {
        $errorMessage = "All fields are required. Please fill in all the fields.";
        echo "<div style='color: red;'>$errorMessage</div>";
    }else{
        // b. Validate MOV_LENGTH (HH:MM:SS format)
        if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $MOV_LENGTH)) {
            $errorMessage = "Invalid movie length: Please use the format HH:MM:SS.";
            echo "<div style='color: red;'>$errorMessage</div>";
        }

        // a. Validate MOV_PRICE (should be numeric)
        if (!is_numeric($MOV_PRICE) || $MOV_PRICE < 0) {
            $errorMessage = "Invalid price: Please enter a valid number for the price.";
            echo "<div style='color: red;'>$errorMessage</div>";
        }

        if (!is_numeric($CIN_ID) || $CIN_ID < 0) {
            $errorMessage = "Invalid cinema number: Please enter a valid number for the cinema number.";
            echo "<div style='color: red;'>$errorMessage</div>";
        }

        // c. Check if CIN_ID exists in Cinema table (foreign key integrity)
        if (!$errorMessage) {
            $cinemaCheckSql = "SELECT CIN_ID FROM CINEMA WHERE CIN_ID = $CIN_ID LIMIT 1";
            $cinemaResult = $mysqli->query($cinemaCheckSql);
            if ($cinemaResult->num_rows == 0) {
                $errorMessage = "The selected cinema does not exist in the database.";
                echo "<div style='color: red;'>$errorMessage</div>";
            }
        }

        // d. Check if the movie already exists (duplicate check)
        if (!$errorMessage) {
            $checkDuplicateSql = "SELECT * FROM MOVIE WHERE MOV_NAME = '$MOV_NAME' AND MOV_LENGTH = '$MOV_LENGTH' AND MOV_GENRE = '$MOV_GENRE' LIMIT 1";
            $checkResult = $mysqli->query($checkDuplicateSql);
            if ($checkResult->num_rows > 0) {
                $errorMessage = "This movie already exists in the database.";
                echo "<div style='color: red;'>$errorMessage</div>";
            }
        }

        // f. Check if a movie is already scheduled in the same cinema for the same day
        if (!$errorMessage) {
            $checkScheduleSql = "SELECT * FROM MOVIE WHERE CIN_ID = $CIN_ID AND MOV_DATE = '$MOV_DATE' LIMIT 1";
            $scheduleResult = $mysqli->query($checkScheduleSql);
            if ($scheduleResult->num_rows > 0) {
                echo "<div style='color: red;'>$errorMessage</div>";
                $errorMessage = "A movie is already scheduled in this cinema on the selected date.";
            }
        }
    }

    
    $origin = "SELECT * FROM movie_reservation_system.MOVIE WHERE MOV_ID='$MOV_ID'";
    $result = $mysqli->query($origin);

    if ($result->num_rows === 0) {
        echo "<div style='color: red;'>Error: The record to update does not exist.</div>";
        exit();
    }

    $row = $result->fetch_assoc();
    
    if($MOV_NAME != "" and $MOV_NAME != $row['MOV_NAME'])
    {
        $MOV_NAME = $MOV_NAME;
    }
    else{
        $MOV_NAME = $row['MOV_NAME'];
    }

    if($MOV_LENGTH != "" and $MOV_LENGTH != $row['MOV_LENGTH'])
    {
        $MOV_LENGTH = $MOV_LENGTH;
    }
    else{
        $MOV_LENGTH = $row['MOV_LENGTH'];
    }

    if($MOV_GENRE != "" and $MOV_GENRE != $row['MOV_GENRE'])
    {
        $MOV_GENRE = $MOV_GENRE;
    }
    else{
        $MOV_GENRE = $row['MOV_GENRE'];
    }

    if($MOV_RATING != "" and $MOV_RATING != $row['MOV_RATING'])
    {
        $MOV_RATING = $MOV_RATING;
    }
    else{
        $MOV_RATING = $row['MOV_RATING'];
    }

    if($MOV_PRICE != "" and $MOV_PRICE != $row['MOV_PRICE'])
    {
        $MOV_PRICE = $MOV_PRICE;
    }
    else{
        $MOV_PRICE = $row['MOV_PRICE'];
    }

    if($CIN_ID != "" and $CIN_ID != $row['CIN_ID'])
    {
        $CIN_ID = $CIN_ID;
    }
    else{
        $CIN_ID = $row['CIN_ID'];
    }

    $sql = "UPDATE movie_reservation_system.MOVIE SET MOV_NAME='$MOV_NAME', MOV_LENGTH='$MOV_LENGTH', MOV_GENRE='$MOV_GENRE', MOV_RATING='$MOV_RATING', MOV_PRICE='$MOV_PRICE', CIN_ID='$CIN_ID' WHERE MOV_ID='$MOV_ID'";
    if(mysqli_query($mysqli,$sql))
    {
        echo "Data updated in Database Sucessfully.";
    }
    else {
        echo mysqli_error($mysqli);
    }
}

// Handle DELETE operation
if (isset($_POST['delete'])) {
    $MOV_ID = $_POST['id'];

    if (strlen($MOV_ID) !== 8 || !ctype_digit($MOV_ID)) {
        echo "<div style='color: red;'>Error: MOV_ID must be of length 8 and must consist of digits only.</div>";
        exit();
    }

    // Check if the record exists
    $check_sql = "SELECT * FROM movie_reservation_system.MOVIE WHERE MOV_ID = '$MOV_ID'";
    $check_result = $mysqli->query($check_sql);

    if ($check_result->num_rows === 0) {
        echo "<div style='color: red;'>Error: The record you are trying to delete does not exist.</div>";
        exit();
    }

    // Delete record
    $sql = "DELETE FROM movie_reservation_system.MOVIE WHERE MOV_ID = '$MOV_ID'";

    if ($mysqli->query($sql)) {
        echo "<div style='color: green;'>Movie deleted successfully!</div>";
        header("Location: movie_database.php");
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
                <input type="date" name="mov_date">
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
