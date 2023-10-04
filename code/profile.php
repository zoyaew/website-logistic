<?php
    session_start();
    include("header.php");
    include("database.php");
    
    $sql = "
    SELECT a.first_name, a.last_name, a.username, a.email, a.branch, a.position, CONCAT(b.first_name, ' ', b.last_name) AS manager_name
    FROM employees AS a
    INNER JOIN employees AS b
    WHERE a.EMPLOYEE_ID = '{$_SESSION['user_id']}' AND a.manager_id=b.employee_id;";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result)>0) {
        $row = mysqli_fetch_assoc($result);
        $personal_info_list = array();
        foreach($row as $elem) {
            array_push($personal_info_list, $elem);
        };
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h3>Personal Information Page</h3>
    <table>
    <?php
        $label_list = ["First Name", "Last Name", "Username", "Email", "Branch", "Position", "Manager Name"];
        for($i = 0; $i<count($label_list); $i++) {
            echo "<tr><td>$label_list[$i]</td><td>:</td><td>$personal_info_list[$i]</td></tr>";
        }
    ?>
    </table>
    <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"])?>" method="POST">
        <input type="submit" name="change_pass" value="Change Password"><br>
    </form>

    <?php
        if($_SERVER["REQUEST_METHOD"]=="POST") {
            if(isset($_POST["change_pass"])) {
                echo "<form action='profile.php' method='POST'>
                    <table>
                        <tr>
                            <td><label for='new_password'>New Password</label></td>
                            <td>: <input type='password' id='new_password' name='new_password' required maxlength=20><br></td>
                        </tr>
                        <tr>
                            <td><label for='confirm_password'>Confirm Password</label></td>
                            <td>: <input type='password' id='confirm_password' name='confirm_password' required maxlength=20><br></td>
                        </tr>
                        <tr>
                            <td><input type='submit' name='submit_new_password' value='Submit'></td>
                            <td></td>
                        </tr>
                    </table>
                </form>";
                
            }
            if(isset($_POST["submit_new_password"])){
                $new_password = $_POST["new_password"];
                $confirm_password = $_POST["confirm_password"];
                $same_pass = !strcmp($new_password, $confirm_password);
                if($same_pass){
                    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $sql = "UPDATE employees SET password_hash = '{$password_hash}'
                    WHERE EMPLOYEE_ID = '{$_SESSION["user_id"]}';
                    ";
                    mysqli_query($conn, $sql);
                    echo "Password is succesfully changed.";
                    
                } else {
                    echo "Passwords do not match.";
                }
            }
        } 
    ?>

</body>

</html>
<?php
     



    mysqli_close($conn);
    include("footer.html");
?>