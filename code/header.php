<?php
    include("database.php");
    // echo "{$_SESSION["login_token"]}<br>";
    // echo "{$_SESSION["user_id"]}<br>";
    // echo "{$_SESSION["expiration_datetime"]}<br>";
    // echo "{$_SESSION["position"]}<br>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <header>
    <h2>Sales Administration Portal</h2>

    <form action = "<?php htmlspecialchars($_SERVER["PHP_SELF"])?>" method = "POST">
        <input type="submit" name="logout" value="Log Out">
    </form>

    <a href="home.php">Home</a>
    <a href="update_order.php">Update Order</a>
    <a href="new_order.php">New Order</a>
    <a href="profile.php">Profile</a>

    <hr>
    </header>
</body>
</html>

<?php
    if($_SERVER["REQUEST_METHOD"]=="POST") {
        if(isset($_POST['logout'])){
            $sql = "DELETE FROM sessions WHERE LOGIN_TOKEN = '{$_SESSION["login_token"]}';";
            $result = mysqli_query($conn, $sql);
            session_destroy();
            header("location: index.php");
        }
    }

    mysqli_close($conn);
?>