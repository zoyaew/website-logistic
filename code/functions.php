<?php



    function valid_token($expiration) {
        $current_datetime = date("Y-m-d H:i:s");
        if($current_datetime <= $expiration) {
            return true;
        } else {
            return false;
        }
    }

?>