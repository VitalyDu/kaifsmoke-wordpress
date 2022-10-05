<?php

/**
 * Pagination - Show numbered pagination for catalog pages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/pagination.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.3.1
 */

if (!defined('ABSPATH')) {
	exit;
}

$total   = isset($total) ? $total : wc_get_loop_prop('total_pages');
$current = isset($current) ? $current : wc_get_loop_prop('current_page');
$base    = isset($base) ? $base : esc_url_raw(str_replace(999999999, '%#%', remove_query_arg('add-to-cart', get_pagenum_link(999999999, false))));
$format  = isset($format) ? $format : '';

if ($total <= 1) {
	return;
}
?>
<?
remove_action('woocommerce_before_shop_loop', 'woocommerce_output_all_notices',  10);
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering',  30);
do_action('woocommerce_before_shop_loop');
?>
<div class="container">
	<div class="vz-pagination vz-pagination_catalog woocommerce-pagination">
		<?php
		echo paginate_links(
			apply_filters(
				'woocommerce_pagination_args',
				array( // WPCS: XSS ok.
					'base'      => $base,
					'format'    => $format,
					'add_args'  => false,
					'current'   => max(1, $current),
					'total'     => $total,
					'prev_text' => is_rtl() ? '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.7535 7.55375C14.9487 7.35849 14.9487 7.0419 14.7535 6.84664C14.5582 6.65138 14.2416 6.65138 14.0463 6.84664L14.7535 7.55375ZM9.5999 12.0002L9.24635 11.6466L8.8928 12.0002L9.24635 12.3537L9.5999 12.0002ZM14.0463 17.1537C14.2416 17.349 14.5582 17.349 14.7535 17.1537C14.9487 16.9585 14.9487 16.6419 14.7535 16.4466L14.0463 17.1537ZM14.0463 6.84664L9.24635 11.6466L9.95346 12.3537L14.7535 7.55375L14.0463 6.84664ZM9.24635 12.3537L14.0463 17.1537L14.7535 16.4466L9.95346 11.6466L9.24635 12.3537Z" fill="#000000"/></svg>' : '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.7535 7.55375C14.9487 7.35849 14.9487 7.0419 14.7535 6.84664C14.5582 6.65138 14.2416 6.65138 14.0463 6.84664L14.7535 7.55375ZM9.5999 12.0002L9.24635 11.6466L8.8928 12.0002L9.24635 12.3537L9.5999 12.0002ZM14.0463 17.1537C14.2416 17.349 14.5582 17.349 14.7535 17.1537C14.9487 16.9585 14.9487 16.6419 14.7535 16.4466L14.0463 17.1537ZM14.0463 6.84664L9.24635 11.6466L9.95346 12.3537L14.7535 7.55375L14.0463 6.84664ZM9.24635 12.3537L14.0463 17.1537L14.7535 16.4466L9.95346 11.6466L9.24635 12.3537Z" fill="#000000"/></svg>',
					'next_text' => is_rtl() ? '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.24654 7.55375C9.05128 7.35849 9.05128 7.0419 9.24654 6.84664C9.44181 6.65138 9.75839 6.65138 9.95365 6.84664L9.24654 7.55375ZM14.4001 12.0002L14.7537 11.6466L15.1072 12.0002L14.7537 12.3537L14.4001 12.0002ZM9.95365 17.1537C9.75839 17.349 9.44181 17.349 9.24654 17.1537C9.05128 16.9585 9.05128 16.6419 9.24654 16.4466L9.95365 17.1537ZM9.95365 6.84664L14.7537 11.6466L14.0465 12.3537L9.24654 7.55375L9.95365 6.84664ZM14.7537 12.3537L9.95365 17.1537L9.24654 16.4466L14.0465 11.6466L14.7537 12.3537Z" fill="#000000"/></svg>' : '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.24654 7.55375C9.05128 7.35849 9.05128 7.0419 9.24654 6.84664C9.44181 6.65138 9.75839 6.65138 9.95365 6.84664L9.24654 7.55375ZM14.4001 12.0002L14.7537 11.6466L15.1072 12.0002L14.7537 12.3537L14.4001 12.0002ZM9.95365 17.1537C9.75839 17.349 9.44181 17.349 9.24654 17.1537C9.05128 16.9585 9.05128 16.6419 9.24654 16.4466L9.95365 17.1537ZM9.95365 6.84664L14.7537 11.6466L14.0465 12.3537L9.24654 7.55375L9.95365 6.84664ZM14.7537 12.3537L9.95365 17.1537L9.24654 16.4466L14.0465 11.6466L14.7537 12.3537Z" fill="#000000"/></svg>',
					'type'      => 'list',
					'end_size'  => 3,
					'mid_size'  => 3,
				)
			)
		);
		?>
	</div>
</div>