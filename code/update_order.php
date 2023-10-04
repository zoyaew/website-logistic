<?php
    session_start();
    include("header.php");
    include("database.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h3>Update Order Status</h3>
    
    <table>
        <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"])?>" method="POST">
            <tr>
                <td><b>Receipt Number</b></td>
                <td>:</td>
                <td> <input type="text" for="receipt_no" name="receipt_no"> </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td> <input type="submit" name="submit_receipt_no" value="Check"> </td>
            </tr>
        </form>
    </table>

    <?php
        if($_SERVER["REQUEST_METHOD"]=="POST") {
            if(isset($_POST["submit_receipt_no"]) && $_POST["receipt_no"]!="") {
                $receipt_no = $_POST["receipt_no"];
                $sql_receipt_check = "SELECT * FROM orders WHERE RECEIPT_NO='{$_POST["receipt_no"]}';";
    
                $receipt_check_result = mysqli_query($conn, $sql_receipt_check);
                if(mysqli_num_rows($receipt_check_result)==0) {
                    echo "Receipt No. {$receipt_no} not found in database. <br>";
                    $_SESSION["receipt_no"]="";
                } else {
                    echo "Receipt No. {$receipt_no} found. <br>";
                    $_SESSION["receipt_no"]=$receipt_no;
                    $order_record_row = mysqli_fetch_assoc($receipt_check_result);
                    $order_record_status = $order_record_row['order_status'];
                    echo "Current Order Status: {$order_record_status} <br>";
                    switch($order_record_status) {
                        case "COMPLETED":
                        case "CANCELLED":
                            echo "Order has been {$order_record_status}. Status cannot be changed. <br>";
                            break;
                        case "AWAITING PAYMENT":
                            ?>
                                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                                    <label for="order_status_option">Status:</label>
                                    <select name="order_status_option" id="order_status_option">
                                        <option value="">--- Choose an updated status ---</option>
                                        <option value="AWAITING SHIPMENT">AWAITING SHIPMENT</option>
                                        <option value="COMPLETED" selected>COMPLETED</option>
                                        <option value="CANCELLED">CANCELLED</option>
                                    </select>
                                    <button type="submit_awaiting_payment">Select</button>
                                </form>
                            <?php
                            break;
                        case "AWAITING SHIPMENT":
                            ?>
                                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                                    <label for="order_status_option">Status:</label>
                                    <select name="order_status_option" id="order_status_option">
                                        <option value="">--- Choose an updated status ---</option>
                                        <option value="COMPLETED" selected>COMPLETED</option>
                                        <option value="CANCELLED">CANCELLED</option>
                                    </select>
                                    <button type="submit_awaiting_shipment">Select</button>
                                </form>
                            <?php
                            break;
                    }
                }
            }
        }
    ?>
</body>

</html>
<?php
    global $from;

    if($_SERVER["REQUEST_METHOD"]=="POST") {
        if(isset($_POST["order_status_option"])) {
            if(isset($_SESSION['receipt_no'])){
                if($_SESSION['receipt_no']!='') {
                    $receipt_no = $_SESSION['receipt_no'];
                    $update_status_query = "UPDATE orders
                                    SET order_status = '{$_POST['order_status_option']}'
                                    WHERE RECEIPT_NO='{$receipt_no}';";
                    mysqli_query($conn, $update_status_query);
                    echo "Order status of receipt no. {$receipt_no} is updated successfully.";
                }
            }
        }
    }
    // foreach($_POST as $k => $v) {
    //     echo "$k => $v <br>";
    // }
    mysqli_close($conn);
    include("footer.html");
?>