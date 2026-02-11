<?php
// This file redirects to the main order-details.php
// Admin can use the same order details page
header("Location: ../order-details.php?order_id=" . $_GET['order_id']);
exit();
?>
