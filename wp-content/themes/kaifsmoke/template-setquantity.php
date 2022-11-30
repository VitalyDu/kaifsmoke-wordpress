<?php
// Template Name: Request template for Set Quantity
?>
<?php
//the cart key stores information about cart
$cartKeySanitized = filter_var($_POST['cart_item_key'], FILTER_SANITIZE_STRING);

//the new qty you want for the product in cart
$cartQtySanitized = filter_var($_POST['cart_item_qty'], FILTER_SANITIZE_STRING);

//update the quantity
global $woocommerce;
ob_start();

$woocommerce->cart->set_quantity($cartKeySanitized,$cartQtySanitized);
ob_get_clean();
?>