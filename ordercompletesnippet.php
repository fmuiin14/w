<?
function update_order_status_on_zero_total($order_id) {
    // Check if the order ID is valid
    if (!$order_id) {
        return;
    }

    // Get the order
    $order = wc_get_order($order_id);

    // Check if the order is valid
    if (!$order) {
        return;
    }

    // Get order total, applied coupons, and order items
    $order_total = $order->get_total();
    $applied_coupons = $order->get_coupon_codes();
    $order_items = $order->get_items();

    // Check if any non-zero priced product exists in the order
    $non_zero_priced_product = false;
    foreach ($order_items as $item) {
        $product = $item->get_product();
        if ($product->get_price() != 0) {
            $non_zero_priced_product = true;
            break;
        }
    }

    // If there's a non-zero priced product and either the order total is zero or a coupon is applied
    if ($non_zero_priced_product && ($order_total == 0 || !empty($applied_coupons))) {
        // Update the order status to completed
        $order->update_status('completed', 'Order total is zero or a coupon is applied.');

        // Redirect to custom order completed page
        wp_safe_redirect(home_url('/order-completed/'));
        exit;
    }
}

add_action('woocommerce_thankyou', 'update_order_status_on_zero_total', 1, 1);