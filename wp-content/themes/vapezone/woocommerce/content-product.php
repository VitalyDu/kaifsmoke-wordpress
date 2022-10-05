<?php

/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

global $product;

// Ensure visibility.
if (empty($product) || !$product->is_visible()) {
	return;
}
?>
<div class="productsWrapper_product product_miniCard" data-productid="<?php the_ID(); ?>" data-productName="<?= $product->get_title() ?>">
	<?php
	$link = apply_filters('woocommerce_loop_product_link', get_the_permalink(), $product);
	?>
	<div class="product_addToFavorites addToFavoritesIcon">
		<button>
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M19.2001 8.61987C19.2001 12.2199 12 18.7314 12 18.7314C12 18.7314 4.80005 12.2199 4.80005 8.61987C4.80005 4.69111 9.60005 3.16234 12 7.0911C14.4 3.16234 19.2 5.01987 19.2001 8.61987Z" stroke="#000000" />
			</svg>
		</button>
	</div>
	<div class="product_image">
		<?php
		/**
		 * Hook: woocommerce_before_shop_loop_item.
		 *
		 * @hooked woocommerce_template_loop_product_link_open - 10
		 */
		// do_action('woocommerce_before_shop_loop_item');
		do_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );

		/**
		 * Hook: woocommerce_before_shop_loop_item_title.
		 *
		 * @hooked woocommerce_show_product_loop_sale_flash - 10
		 * @hooked woocommerce_template_loop_product_thumbnail - 10
		 */
		remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
		do_action('woocommerce_before_shop_loop_item_title');
		remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
		do_action('woocommerce_after_shop_loop_item');
		/**
		 * Hook: woocommerce_after_shop_loop_item.
		 *
		 * @hooked woocommerce_template_loop_product_link_close - 5
		 * @hooked woocommerce_template_loop_add_to_cart - 10
		 */
		remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
		do_action('woocommerce_after_shop_loop_item');
		?>
	</div>
	<div class="product_nameRateProperties">
		<?
		/**
		 * Hook: woocommerce_shop_loop_item_title.
		 *
		 * @hooked woocommerce_template_loop_product_title - 10
		 */
		// do_action('woocommerce_shop_loop_item_title');
		?>
		<div class="product_rate">
			<ul class="product_rate__list">
				<li>
					<svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z" stroke="#000000" stroke-width="0.5" stroke-linejoin="round" />
					</svg>
				</li>
				<li>
					<svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z" stroke="#000000" stroke-width="0.5" stroke-linejoin="round" />
					</svg>
				</li>
				<li>
					<svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z" stroke="#000000" stroke-width="0.5" stroke-linejoin="round" />
					</svg>
				</li>
				<li>
					<svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z" stroke="#000000" stroke-width="0.5" stroke-linejoin="round" />
					</svg>
				</li>
				<li>
					<svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z" stroke="#000000" stroke-width="0.5" stroke-linejoin="round" />
					</svg>
				</li>
			</ul>
		</div>
		<a href="<?php echo $link ?>" class="product_name">
			<? the_title(); ?>
		</a>
		<div class="product_properties">
			<div class="product_properties__property">
				<span class="property_name">Вкус:</span>
				<span class="property_value">Десерт</span>
			</div>
			<div class="product_properties__property">
				<span class="property_name">Тип никотина:</span>
				<span class="property_value">Солевой</span>
			</div>
			<div class="product_properties__property">
				<span class="property_name">Никотин:</span>
				<span class="property_value">20</span>
			</div>
			<div class="product_properties__property">
				<span class="property_name">Соотношение Pg / Vg:</span>
				<span class="property_value">50 / 50</span>
			</div>
			<div class="product_properties__property">
				<span class="property_name">Тип никотина:</span>
				<span class="property_value">Солевой</span>
			</div>
			<div class="product_properties__property">
				<span class="property_name">Соотношение Pg / Vg:</span>
				<span class="property_value">50 / 50</span>
			</div>
		</div>
	</div>
	<div class="product_priceAddToCart">
		<?

		/**
		 * Hook: woocommerce_after_shop_loop_item_title.
		 *
		 * @hooked woocommerce_template_loop_rating - 5
		 * @hooked woocommerce_template_loop_price - 10
		 */
		remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
		do_action('woocommerce_after_shop_loop_item_title');

		/**
		 * Hook: woocommerce_after_shop_loop_item.
		 *
		 * @hooked woocommerce_template_loop_product_link_close - 5
		 * @hooked woocommerce_template_loop_add_to_cart - 10
		 */
		add_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
		do_action('woocommerce_after_shop_loop_item');
		?>
	</div>
</div>