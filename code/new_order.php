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
    <h3>New Transaction Page</h3>
    
    <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"])?>" method="POST">
        <input type="submit" name="add_new_customer" value="Add New Customer">
    </form>

    <?php
        if($_SERVER["REQUEST_METHOD"]=="POST") {
            if(isset($_POST["add_new_customer"])) {?>
                <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"])?>" method="POST">
                    <table>
                        <tr>
                            <td>Customer Name</td>
                            <td>:</td>
                            <td><input type="text" id="customer_name" name="customer_name" required maxlength="50"></td>
                        </tr>
                        <tr>
                            <td>Customer Address</td>
                            <td>:</td>
                            <td><input type="text" id="customer_address" name="customer_address" required maxlength="225"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td><input type="submit" name="submit_new_customer" value="Submit New Customer"></td>
                        </tr>
                    </table>
                </form>
            <?php
            }
            elseif(isset($_POST["submit_new_customer"])) {
                $sql_submit_new_customer = "INSERT INTO customers(customer_name, customer_address)
                                            VALUES ('{$_POST['customer_name']}', '{$_POST['customer_address']}');";
                mysqli_query($conn, $sql_submit_new_customer);
                echo "New customer created. {$_POST['customer_name']} has customer id: " . mysqli_insert_id($conn);
            }
        }
    ?>

    <br>

    <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"])?>" method="POST">
        <table>
            <tr>
                <td>Customer Id</td>
                <td>:</td>
                <td><input type="text" id="customer_id" name="customer_id" ></td>
            </tr>
            <tr>
                <td>Product Id</td>
                <td>:</td>
                <td><input type="text" id="product_id" name="product_id" ></td>
            </tr>
            <tr>
                <td>Quantity</td>
                <td>:</td>
                <td><input type="number" id="quantity" name="quantity" ></td>
            </tr>
            <tr>
                <td>Description</td>
                <td>:</td>
                <td><input type="text" id="description" name="description"></td>
            </tr>
            <tr>
                <td>Downpayment</td>
                <td>:</td>
                <td><input type="radio" id="downpayment" name="downpayment" value="Yes" >Yes
                    <input type="radio" id="downpayment" name="downpayment" value="No" >No
                </td>
            </tr>
            <tr>
                <td>Estimated Payment Deadline</td>
                <td>:</td>
                <td><input type="date" id="payment_deadline" name="payment_deadline"></td>
            </tr>
            <tr>
                <td>Estimated Shipment Deadline</td>
                <td>:</td>
                <td><input type="date" id="shipment_deadline" name="shipment_deadline"></td>
            </tr>
            <tr>
                <td><input type="submit" name="add_new_transaction" value="Add New Transaction"></td>
                <td></td>
                <td></td>
            </tr>
        </table>
        <?php
            if($_SERVER["REQUEST_METHOD"]=="POST") {
                if(isset($_POST["add_new_transaction"])) {
                    // check required fields
                    if(empty($_POST["customer_id"])){
                        echo "Do not leave 'Customer Id' empty!<br>";
                    } elseif(empty($_POST["product_id"])) {
                        echo "Do not leave 'Product Code' empty!<br>";
                    } elseif(empty($_POST["quantity"])) {
                        echo "Do not leave 'Quantity' empty!<br>";
                    } elseif(empty($_POST["downpayment"])) {
                        echo "Do not leave 'Downpayment' empty!<br>";
                    } else {

                        // check validity of customer_id and product_id
                        $check_fields = check_customer_and_product_id($conn, $_POST['customer_id'], $_POST['product_id']);
                        if(!$check_fields[0]){
                            switch($check_fields[1]){
                                case "c": echo "ERROR: Customer Id not found in database <br>"; break;
                                case "p": echo "ERROR: Product Id not found in database <br>"; break;
                            }
                        } else{
                            echo "Please check this new record.<br>";
                            $current_time_date = date('Y-m-d H:i:s', time());
                            $new_record_fields = [$current_time_date, $_POST["customer_id"], $check_fields[0], $check_fields[1], $_POST["product_id"], $check_fields[2], $_POST["quantity"], $check_fields[3], $_POST["quantity"]*$check_fields[3]];
                            array_push($new_record_fields, ($_POST["description"]!="") ? $_POST["description"] : "");
                            array_push($new_record_fields, ($_POST["payment_deadline"]!="") ? $_POST["payment_deadline"] : date( "Y-m-d", strtotime( "$current_time_date + 14 day" ) ));
                            array_push($new_record_fields, ($_POST["shipment_deadline"]!="") ? $_POST["shipment_deadline"] : date( "Y-m-d", strtotime( "$current_time_date + 28 day" ) ));
                            array_push($new_record_fields, ($_POST["downpayment"]=="Yes") ? "AWAITING SHIPMENT" : "AWAITING PAYMENT");
                            $_SESSION['new_record'] = $new_record_fields;
                            ?>
                            <table>
                                <tr>
                                    <th>Order Datetime</th>
                                    <th>Customer Id</th>
                                    <th>Customer Name</th>
                                    <th>Customer Address</th>
                                    <th>Product Code</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>PPU</th>
                                    <th>Total Payment</th>
                                    <th>Order Description</th>
                                    <th>Payment Deadline</th>
                                    <th>Shipment Deadline</th>
                                    <th>Status</th>
                                </tr>
                                <tr>
                                    <?php
                                        foreach($new_record_fields as $elem) {
                                            echo "<td>".$elem."</td>";
                                        }
                                    ?>
                                </tr>
                            </table>
                            
                            
                            <table>
                                <tr>
                                    <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"])?>" method="POST">
                                        <td><?php echo "Are you sure you want to submit?<br>"; ?></td>
                                        <td><input type="submit" name="cancel_new_transaction" value="Cancel New Transaction"><td>
                                        <td><input type="submit" name="submit_new_transaction" value="Submit New Transaction"></td>
                                    </form>
                                </tr>
                            </table>
                            <?php
                        }
                    }
                }
                if(isset($_POST["cancel_new_transaction"])) {
                    echo "Cancel new Transaction<br>";
                }
                if(isset($_POST["submit_new_transaction"])) {
                    echo "Submit New Transaction<br>";
                    $new_record_fields = $_SESSION['new_record'];
                    $sql_submit_new_customer = "INSERT INTO orders(customer_id, employee_id, product_id, order_quantity, order_price, 
                                                    order_description, order_registration_date, order_payment_deadline, order_shipment_deadline, order_status)
                                                VALUES ('{$new_record_fields[1]}', '{$_SESSION['user_id']}', '{$new_record_fields[4]}', '{$new_record_fields[6]}', '{$new_record_fields[8]}', 
                                                    '{$new_record_fields[9]}', '{$new_record_fields[0]}', '{$new_record_fields[10]}', '{$new_record_fields[11]}', '{$new_record_fields[12]}');";
                    mysqli_query($conn, $sql_submit_new_customer);
                    echo "New transaction submitted. Receipt no: " . mysqli_insert_id($conn);
                } 
            }
        ?>
    </form>

</body>

</html>
<?php
    // foreach($_POST as $k => $v) {
    //     echo "$k $v <br>";
    // }
    
    function check_customer_and_product_id($conn, $customer_id, $product_id) {
        $sql_customer_check = "SELECT customer_name, customer_address FROM customers WHERE customer_id='{$customer_id}';";
        $customer_check_result = mysqli_query($conn, $sql_customer_check);
        if(mysqli_num_rows($customer_check_result)==0) {
            return [false, "c"];
        }
        $sql_product_check = "SELECT product_name, product_price FROM products WHERE product_id='{$product_id}';";
        $product_check_result = mysqli_query($conn, $sql_product_check);
        if(mysqli_num_rows($product_check_result)==0) {
            return [false, "p"];
        }
        
        $customer = mysqli_fetch_assoc($customer_check_result);
        $product = mysqli_fetch_assoc($product_check_result);

        return [$customer['customer_name'], $customer['customer_address'], $product['product_name'], $product['product_price']];
    }
    mysqli_close($conn);
    include("footer.html");
?>