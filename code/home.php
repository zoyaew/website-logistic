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
    <title>Document</title>
</head>
<body>
    <!-- <?php echo "EMPLOYEE ID: {$_SESSION["user_id"]}"; ?> <br>
    <br> -->
    <h3>Sales History</h3>
    <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"])?>" method="POST">
        <table>
            <tr>
                <td><b>Customer Name</b></td>
                <td>:<input type="radio" id="customer_name" name="customer_name" value="show">Show</td>
                <td><input type="radio" id="customer_name" name="" value="hide">Hide</td><br>
            </tr>
            <tr>
                <td><b>Product Name</b></td>
                <td>:<input type="radio" id="product_name" name="product_name" value="show">Show</td>
                <td><input type="radio" id="product_name" name="" value="hide">Hide</td><br>
            </tr>
            <tr>
                <td><b>Order Description</b></td>
                <td>:<input type="radio" id="order_description" name="order_description" value="show">Show</td>
                <td><input type="radio" id="order_description" name="" value="hide">Hide</td><br>
            </tr>
            <tr>
                <td><b>Payment Deadline</b></td>
                <td>: From <input type="date" id="payment_deadline" name="payment_deadline_from"></td>
                <td>- To <input type="date" id="payment_deadline" name="payment_deadline_to"></td>
            </tr>
            <tr>
                <td><b>Shipment Deadline</b></td>
                <td>: From <input type="date" id="shipment_deadline" name="shipment_deadline_from"></td>
                <td>- To <input type="date" id="shipment_deadline" name="shipment_deadline_to"></td>
            </tr>
            <tr>
                <td><b>Status</b></td>
                <td>: <input type="checkbox" id="status" name="status_1" value="AWAITING PAYMENT"> AWAITING PAYMENT </td>
                <td> <input type="checkbox" id="status" name="status_2" value="AWAITING SHIPMENT"> AWAITING SHIPMENT </td>
                <td> <input type="checkbox" id="status" name="status_3" value="COMPLETED"> COMPLETED </td>
                <td> <input type="checkbox" id="status" name="status_4" value="CANCELLED"> CANCELLED </td>
            </tr>
            <tr>
                <td><b>Pagination size (20-100)</b></td>
                <?php
                    $pagination_size = (isset($_POST['pagination_size'])) ? $_POST['pagination_size'] : 25;
                    echo "<td>: <input type='range' name='pagination_size' min='20' max='100' step='5' value=$pagination_size onchange='updateTextInput(this.value)'> <output id='textInput'>$pagination_size</output> </td>";
                ?>
            </tr>
            <tr>
                <td><b><label for="customer_search">Customer Id</label></b></td>
                <td>: <input type="search" id="customer_search" name="customer_id" /></td>
            </tr>
            <tr>
                <td><b><label for="product_search">Product Id</label></b></td>
                <td>: <input type="search" id="product_search" name="product_id" /></td>
            </tr>
        </table>
        <input type="submit" name="filter" value="Filter"><br>
        <script>
            function updateTextInput(val) {
                document.getElementById('textInput').value=val; 
            }
        </script>

    </form>
    <hr>

    <?php
        $select_query = "SELECT RECEIPT_NO, order_registration_date, orders.customer_id, 
                        orders.product_id, order_quantity, order_price, 
                        order_payment_deadline, order_shipment_deadline, order_status";
        $from_query = "FROM orders";
        $where_query = "WHERE employee_id='{$_SESSION["user_id"]}'";
        $order_by_query = "ORDER BY order_registration_date DESC";

        //new empty page
        if(empty($_SESSION["num_rows"])){
            if($conn -> connect_error) {
                die("Connection failed:".$conn -> connect_error);
            }
            // count total number of rows
            $count_query = implode(" ", array("SELECT COUNT(*) AS count_num_rows", $from_query, $where_query, $order_by_query));
            $count_rows_result = mysqli_query($conn, $count_query);
            $_SESSION["num_rows"] = mysqli_fetch_assoc($count_rows_result)["count_num_rows"];

            $full_query = implode(" ", array($select_query, $from_query, $where_query, $order_by_query));
            
            $_SESSION["page_number"] = isset($_SESSION["page_number"]) ? $_SESSION["page_number"] : 1;
            $_SESSION["pagination_size"] = isset($_SESSION["pagination_size"]) ? $_SESSION["pagination_size"] : ((isset($_POST['pagination_size'])) ? $_POST['pagination_size'] : 25);
            $starting_idx = ($_SESSION["page_number"]-1) * $_SESSION["pagination_size"];

            echo "{$_SESSION['pagination_size']} mevke";

            $_SESSION["full_query"] = $full_query;
            // echo "11111 {$_SESSION['full_query']} <br>";
            $full_query = "{$full_query} LIMIT {$starting_idx}, {$_SESSION['pagination_size']}";
        };
        // echo "22222 {$_SESSION['full_query']} <br>";

        if($_SERVER["REQUEST_METHOD"]=="POST") {
            if(isset($_POST["filter"])) {
                // echo "Filtered<br>";

                // filter: customer_name -> show
                if(isset($_POST['customer_name'])) {
                    $select_query = str_replace('orders.customer_id, ', 'orders.customer_id, customer_name, ', $select_query);
                    $from_query = "{$from_query} INNER JOIN customers ON orders.customer_id=customers.CUSTOMER_ID ";
                }
                // filter: product_name -> show
                if(isset($_POST['product_name'])) {
                    $select_query = str_replace('orders.product_id, ', 'orders.product_id, product_name, ', $select_query);
                    $from_query = "{$from_query} INNER JOIN products ON orders.product_id=products.PRODUCT_ID";
                }
                // filter: order_description -> show
                if(isset($_POST['order_description'])) {
                    $select_query = str_replace('order_price, ', 'order_price, order_description, ', $select_query);
                }
                // filter: payment_deadline_from and payment_deadline_to
                if($_POST['payment_deadline_from']!="") {
                    $where_query = "{$where_query} AND order_payment_deadline>='{$_POST['payment_deadline_from']}'";
                }
                if($_POST['payment_deadline_to']!="") {
                    $where_query = "{$where_query} AND order_payment_deadline<='{$_POST['payment_deadline_to']}'";
                } 
                // filter: shipment_deadline_from and shipment_deadline_to
                if($_POST['shipment_deadline_from']!="") {
                    $where_query = "{$where_query} AND order_shipment_deadline>='{$_POST['shipment_deadline_from']}'";
                }
                if($_POST['shipment_deadline_to']!="") {
                    $where_query = "{$where_query} AND order_shipment_deadline<='{$_POST['shipment_deadline_to']}'";
                }
                // filter: status
                $status_filters = "";
                if(isset($_POST['status_1'])) {
                    $status_filters = "ORDER_STATUS = '{$_POST['status_1']}'";
                }
                if(isset($_POST['status_2'])) {
                    if($status_filters=="") { $status_filters = "ORDER_STATUS = '{$_POST['status_2']}'"; }
                    else { $status_filters = "{$status_filters} OR ORDER_STATUS = '{$_POST['status_2']}'"; }
                }
                if(isset($_POST['status_3'])) {
                    if($status_filters=="") { $status_filters = "ORDER_STATUS = '{$_POST['status_3']}'"; }
                    else { $status_filters = "{$status_filters} OR ORDER_STATUS = '{$_POST['status_3']}'"; }
                }
                if(isset($_POST['status_4'])) {
                    if($status_filters=="") { $status_filters = "ORDER_STATUS = '{$_POST['status_4']}'"; }
                    else { $status_filters = "{$status_filters} OR ORDER_STATUS = '{$_POST['status_4']}'"; }
                }
                if($status_filters!="") {
                    $where_query = "{$where_query} AND ({$status_filters})";
                }
                // filter: customer_id
                if($_POST['customer_id']!="") {
                    if(isset($_POST['customer_name'])) {
                        $where_query = "{$where_query} AND customers.customer_id='{$_POST['customer_id']}'";
                    } else {
                        $where_query = "{$where_query} AND customer_id='{$_POST['customer_id']}'";
                    }
                }
                // filter: product_id
                if($_POST['product_id']!="") {
                    if(isset($_POST['product_name'])) {
                        $where_query = "{$where_query} AND products.product_id='{$_POST['product_id']}'";
                    } else {
                        $where_query = "{$where_query} AND product_id='{$_POST['product_id']}'";
                    }
                }
    
                $full_query = implode(" ", array($select_query, $from_query, $where_query, $order_by_query));
    
                if($conn -> connect_error) {
                    die("Connection failed:".$conn -> connect_error);
                }

                // count total number of rows
                $count_query = implode(" ", array("SELECT COUNT(*) AS count_num_rows", $from_query, $where_query, $order_by_query));
                $count_rows_result = mysqli_query($conn, $count_query);
                $num_rows = mysqli_fetch_assoc($count_rows_result)["count_num_rows"];
                // echo $num_rows;
                $_SESSION["page_number"] = 1;
                $_SESSION["num_rows"] = $num_rows;
                $_SESSION["pagination_size"] = $pagination_size;
                $_SESSION["full_query"] = $full_query;
                $full_query = "{$full_query} LIMIT 0, {$_SESSION['pagination_size']}";
                // echo $full_query;
            }
            elseif(isset($_POST["previous_page"])) {
                if($_SESSION["page_number"]>1) $_SESSION["page_number"]--;
            } elseif(isset($_POST["next_page"])) {
                if($_SESSION["page_number"]<ceil($_SESSION["num_rows"] / $_SESSION["pagination_size"])) $_SESSION["page_number"]++;
            }
        }

        $headers_dict = [
            "RECEIPT_NO" => "Receipt No.",
            "order_registration_date" => "Registration Date",
            "orders.customer_id" => "Customer Id",
            "customer_name" => "Customer Name",
            "orders.product_id" => "Product Code",
            "product_name" => "Product Name",
            "order_quantity" => "Order Quantity",
            "order_price" => "Order Price",
            "order_description" => "Order Description",
            "order_payment_deadline" => "Payment Deadline",
            "order_shipment_deadline" => "Shipment Deadline",
            "order_status" => "Order Status",
        ];

        // Create table
        echo "<table>";

        // Create table headers
        echo "<tr>";
        $select_query = explode("FROM ", $_SESSION["full_query"])[0];
        foreach($headers_dict as $k => $v) {
            if (str_contains($select_query, $k)) { 
                echo "<th>{$v}</th>";
            }
        }
        echo "</tr>";

        // Create table data
        $starting_idx = ($_SESSION['page_number']-1)*$_SESSION['pagination_size'];
        // echo "33333 {$_SESSION['full_query']}";
        $full_query = "{$_SESSION['full_query']} LIMIT {$starting_idx}, {$_SESSION['pagination_size']}";
        $result = mysqli_query($conn, $full_query);
        if(mysqli_num_rows($result)>0) {
            $row = mysqli_fetch_assoc($result);
            foreach($result as $row){
                echo "<tr>";
                foreach($row as $val){
                    echo "<td>{$val}</td>";
                }
                echo "</tr>";
            }
            
        }
        echo "</table>";
    ?>
    <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"])?>" method="POST">
        <input type="submit" name="previous_page" value="<">
        <input type="submit" name="next_page" value=">">
    </form>
    
    <?php
        if($_SERVER["REQUEST_METHOD"]=="POST") {
            $num_rows = $_SESSION["num_rows"];
            $page_number = $_SESSION["page_number"];
            $pagination_size = $_SESSION["pagination_size"];
            $total_page_number = ceil($num_rows / $pagination_size);
            echo "Page {$page_number}/{$total_page_number} - {$num_rows} results.<br>";
        } else {
            $num_rows = $_SESSION["num_rows"];
            $page_number = $_SESSION["page_number"];
            $pagination_size = $_SESSION["pagination_size"];
            $total_page_number = ceil($num_rows / $pagination_size);
            echo "Page {$page_number}/{$total_page_number} - {$num_rows} results.<br>";
        }
        
    ?>
</body>
</html>

<?php

    mysqli_close($conn);
    include("footer.html");
?>