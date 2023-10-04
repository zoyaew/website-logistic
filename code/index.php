<?php
    include("database.php");
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Portal</title>
    <h2>Sales Portal</h2>
    <table>
        <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"])?>" method="POST">
            <tr>
                <td><label for="username">Username or Email</label></td>
                <td>:</td>
                <td><input type="text" id="username" name="username" required maxlength=20></td>
            </tr>
            <tr>
                <td><label for="password">Password</label></td>
                <td>:</td>
                <td><input type="password" id="password" name="password" required maxlength=20></td>
            <tr>
                <td></td>
                <td></td>
                <td><input type="submit" name="login" value="Log In"></td>
            </tr>
        </form>
    </table>
</head>
<body>
        
</body>
</html>

<?php
    // foreach($_POST as $k => $v) {
    //     echo "$k => $v <br>";
    // }
    if($_SERVER["REQUEST_METHOD"]=="POST") {
        // check if the field(s) are empty
        if(empty($_POST["username"]) || empty($_POST["password"])) {
            echo "Please fill in your username and password.<br>";
        } else {
            //check the validity of username and password

            $username_or_email = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
            $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
            $invalid_username_or_email = strcmp($username_or_email, $_POST["username"]);
            $invalid_password = strcmp($password, $_POST["password"]);
            
            $is_username = true;
            if(filter_var($username_or_email, FILTER_VALIDATE_EMAIL)) {
                $is_username = false;
                echo "$is_username";
            }

            if($invalid_username_or_email && $invalid_password) {
                echo "Do not use special characters for your username and password. <br>";
            }
            elseif($invalid_username_or_email) {
                echo "Do not use special characters for your username. <br>";
            }
            elseif($invalid_password) {
                echo "Do not use special characters for your password. <br>";
            }
            else {
                // check if the username / email is already on the database
                try {
                    $sql = "";
                    switch($is_username) {
                        case true:
                            $sql = "SELECT * FROM employees WHERE username='$username_or_email';";
                            break;
                        case false:
                            $sql = "SELECT * FROM employees WHERE email='$username_or_email';";
                            break;
                    }
                    
                    $result = mysqli_query($conn, $sql);
                    // case 1: name is on the database
                    if(mysqli_num_rows($result)>0) {
                        $row = mysqli_fetch_assoc($result);
                        // foreach($row as $k => $v) {
                        //     echo "$k, $v <br>";
                        // }
                        // verify password
                        if(password_verify($password, $row["password_hash"])) {
                            //TODO: check for any active session
                            ////

                            while(true) {
                                //generate a unique token
                                $generated_token = generate_session_token();
                                if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM sessions WHERE LOGIN_TOKEN='{$generated_token}';"))==0) {
                                // if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM sessions"))==0) {
                                    $_SESSION["login_token"] = generate_session_token();
                                    break;
                                }
                            }
                            $_SESSION["user_id"] = $row["EMPLOYEE_ID"];
                            $_SESSION["position"] = $row["position"];
                            $_SESSION["expiration_datetime"] = date("Y-m-d H:i:s",strtotime("+2 days", strtotime(date("Y-m-d H:i:s"))));
                            mysqli_query($conn, "INSERT INTO sessions VALUES('{$_SESSION['login_token']}', '{$_SESSION['user_id']}', '{$_SESSION['expiration_datetime']}');");
                            header("location: home.php");
                        } else {
                            echo "Incorrect password. <br>";
                            echo "<a href= {$_SERVER["PHP_SELF"]}>Forget password. <br></a>";
                        }
                        
                    }
                    // case 2: username / email is not on the database
                    else {
                        echo "Your username or email is incorrect. <br>";
                    }

                }
                catch(mysqli_sql_exception) {
                    echo "Could not register user at this moment. Please try again later.<br>";
                }
            }


        }
    }



    mysqli_close($conn);

    function generate_session_token() {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($permitted_chars), 0, 16);
    }
?>  