<div class="pickup-shipping-toggle">
    <button type="button" id="btn-shipping" class="toggle-btn active">Ship to Address</button>
    <button type="button" id="btn-pickup" class="toggle-btn">Pickup from Store</button>
</div>

<!-- Pickup Fields (Hidden by Default) -->
<div id="pickup-fields" style="display: none;">
    <label for="wc_pickup_store">Select a Store</label>
    <input list="wc_pickup_store_list" name="wc_pickup_store" id="wc_pickup_store" placeholder="Search store...">
    <datalist id="wc_pickup_store_list">
        <?php foreach ($stores as $store) : ?> 
            <?php 
                $store_id = isset($store['id']) ? $store['id'] : '';
                $store_name = isset($store['name']) ? $store['name'] : 'Unnamed Store';
                $store_address = isset($store['address']) ? $store['address'] : 'Address not available';
            ?>
            <option value="<?php echo esc_attr($store_name . ' - ' . $store_address); ?>">
        <?php endforeach; ?>
    </datalist>

    <label for="wc_pickup_date">Select Pickup Date</label>
    <input type="date" name="wc_pickup_date" id="wc_pickup_date">
</div>

<input type="hidden" name="wc_shipping_option" id="wc_shipping_option" value="shipping">
