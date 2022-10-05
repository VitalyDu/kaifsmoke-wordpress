<?php

/**
 * Product Loop Start
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-start.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.3.0
 */

if (!defined('ABSPATH')) {
	exit;
}
?>

<section class="allProducts">
	<div class="container">
		<div class="section_title">
			<h2>
				Поиск по запросу:
				<?php if (isset($_GET['s'])) { ?>
					<?= urldecode($_GET['s']) ?>
				<?php } ?>
			</h2>
		</div>
		<div class="allProducts_view hidden-mobile">
			<ul>
				<li class="allProducts_view__showTable active">
					<a>
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<rect width="24" height="24" fill="white" />
							<rect x="3" y="3" width="8" height="8" stroke="#000000" stroke-linejoin="round" />
							<rect x="3" y="13" width="8" height="8" stroke="#000000" stroke-linejoin="round" />
							<rect x="13" y="3" width="8" height="8" stroke="#000000" stroke-linejoin="round" />
							<rect x="13" y="13" width="8" height="8" stroke="#000000" stroke-linejoin="round" />
						</svg>
						Таблица
					</a>
				</li>
				<li class="allProducts_view__showList">
					<a>
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<rect width="24" height="24" fill="white" />
							<path d="M3 3H21V11H3V3Z" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" />
							<path d="M3 13H21V21H3V13Z" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" />
						</svg>
						Список
					</a>
				</li>
			</ul>
		</div>
		<div class="allProducts_view hidden-desktop">
			<ul>
				<li class="allProducts_view__showTable active">
					<a>
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<rect width="24" height="24" fill="white" />
							<rect x="13" y="3" width="8" height="18" stroke="#000000" stroke-linejoin="round" />
							<rect x="3" y="3" width="8" height="18" stroke="#000000" stroke-linejoin="round" />
						</svg>
						Два товара
					</a>
				</li>
				<li class="allProducts_view__showList">
					<a>
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<rect x="6" y="3" width="12" height="18" stroke="#000000" stroke-linejoin="round" />
						</svg>
						Один товар
					</a>
				</li>
			</ul>
		</div>
		<div class="productsWrapper table ">
			<div class="productsWrapper_block">