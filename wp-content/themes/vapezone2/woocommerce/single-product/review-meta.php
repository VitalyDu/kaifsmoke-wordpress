<?php

/**
 * The template to display the reviewers meta data (name, verified owner, review date)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/review-meta.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined('ABSPATH') || exit;

global $comment;
$verified = wc_review_is_from_verified_owner($comment->comment_ID);

if ('0' === $comment->comment_approved) { ?>

	<div class="reviews_block__reviewEmpty">
		<span class="label">Отзывов пока нет</span>
	</div>

<?php } else { ?>
	<? $average = get_comment_meta($comment->comment_ID, 'rating', true); ?>
	<? if ($comment->comment_parent == 0) { ?>
		<div class="review_user">
			<div class="review_user__nameRate">
				<span class="userName">
					<?php comment_author(); ?>
				</span>
				<ul class="productCard_rate__list">
					<li>
						<svg width="12" height="11" viewBox="0 0 12 11" fill="<? if ($average >= 0.5) { ?>#1D1D1B<? } else { ?>none<? } ?>" xmlns="http://www.w3.org/2000/svg">
							<path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z" stroke="#1D1D1B" stroke-width="0.5" stroke-linejoin="round" />
						</svg>
					</li>
					<li>
						<svg width="12" height="11" viewBox="0 0 12 11" fill="<? if ($average >= 1.5) { ?>#1D1D1B<? } else { ?>none<? } ?>" xmlns="http://www.w3.org/2000/svg">
							<path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z" stroke="#1D1D1B" stroke-width="0.5" stroke-linejoin="round" />
						</svg>
					</li>
					<li>
						<svg width="12" height="11" viewBox="0 0 12 11" fill="<? if ($average >= 2.5) { ?>#1D1D1B<? } else { ?>none<? } ?>" xmlns="http://www.w3.org/2000/svg">
							<path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z" stroke="#1D1D1B" stroke-width="0.5" stroke-linejoin="round" />
						</svg>
					</li>
					<li>
						<svg width="12" height="11" viewBox="0 0 12 11" fill="<? if ($average >= 3.5) { ?>#1D1D1B<? } else { ?>none<? } ?>" xmlns="http://www.w3.org/2000/svg">
							<path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z" stroke="#1D1D1B" stroke-width="0.5" stroke-linejoin="round" />
						</svg>
					</li>
					<li>
						<svg width="12" height="11" viewBox="0 0 12 11" fill="<? if ($average >= 4.5) { ?>#1D1D1B<? } else { ?>none<? } ?>" xmlns="http://www.w3.org/2000/svg">
							<path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z" stroke="#1D1D1B" stroke-width="0.5" stroke-linejoin="round" />
						</svg>
					</li>
				</ul>
			</div>
			<div class="review_user__text">
				<p>
					<?php comment_text() ?>
				</p>
			</div>
			<div class="review_user__dateTime">
				<span class="review_user__date">
					<?php comment_date('j F Y') ?>
				</span>
				<span class="review_user__time">
					<?php comment_date('H:i') ?>
				</span>
			</div>
		</div>
	<? } else { ?>
		<div class="review_admin">
			<div class="review_admin__answer">
				<img src="images/icons/answer.png" alt="">
				<span>
					Ответ
					<br>
					администратора
				</span>
			</div>
			<div class="review_admin__text">
				<p><?php comment_text() ?></p>
			</div>
			<div class="review_admin__dateTime">
				<span class="review_admin__date">
					<?php comment_date('j F Y') ?>
				</span>
				<span class="review_admin__time">
					<?php comment_date('H:i') ?>
				</span>
			</div>
		</div>
	<? } ?>
<?php
}
?>