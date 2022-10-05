<?php

/**
 * Product attributes
 *
 * Used by list_attributes() in the products class.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-attributes.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

if (!$product_attributes) {
	return;
}
?>
<div class="productCard_characteristics">
	<h4>Характеристики</h4>
	<div class="productCard_characteristics__blocks">
		<?php
        $allowed_attr_filters = explode(';', get_field('allowed_product_attr', 'option'));
        foreach ($product_attributes as $product_attribute_key => $product_attribute) : ?>
			<? if ($product_attribute['label'] != 'Штрихкод' && $product_attribute['label'] != 'Код ТН ВЭД' && in_array(str_replace('attribute_pa_', '', $product_attribute_key), $allowed_attr_filters)) { ?>
				<div class="productCard_characteristicsBlocks__block woocommerce-product-attributes-item woocommerce-product-attributes-item--<?php echo esc_attr($product_attribute_key); ?>">
					<span class="productCard_characteristic"><?php echo wp_kses_post($product_attribute['label']); ?></span>
					<span class="productCard_characteristicValue"><?php echo wp_kses_post($product_attribute['value']); ?></span>
				</div>
			<? } ?>
		<?php endforeach; ?>
	</div>
</div>