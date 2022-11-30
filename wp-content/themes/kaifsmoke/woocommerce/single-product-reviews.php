<?php

/**
 * Display single product reviews (comments)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product-reviews.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.3.0
 */

defined('ABSPATH') || exit;

global $product;

if (!comments_open()) {
    return;
}

$comments = get_comments([
    'post_id' => $product->get_id()
]);
$comments_count = count($comments);
$comments_rating_sum = 0;
foreach ($comments as $comment) {
    $comments_rating_sum += intval(get_comment_meta($comment->comment_ID, 'rating', true));
}
$average = ($comments_count > 0) ? $comments_rating_sum / $comments_count : 0;

?>
<section class="reviews" id="reviews">
    <div class="container">
        <div class="section_title">
            <h2>Отзывы</h2>
        </div>
        <div class="reviews_quantity">
            <? echo $product->get_review_count(); ?> отзывов
        </div>
        <div class="reviews_rate">
            <ul class="productCard_rate__list">
                <li>
                    <svg width="12" height="11" viewBox="0 0 12 11"
                         fill="<? if ($average >= 0.5) { ?>#1D1D1B<? } else { ?>none<? } ?>"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z"
                              stroke="#1D1D1B" stroke-width="0.5" stroke-linejoin="round"/>
                    </svg>
                </li>
                <li>
                    <svg width="12" height="11" viewBox="0 0 12 11"
                         fill="<? if ($average >= 1.5) { ?>#1D1D1B<? } else { ?>none<? } ?>"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z"
                              stroke="#1D1D1B" stroke-width="0.5" stroke-linejoin="round"/>
                    </svg>
                </li>
                <li>
                    <svg width="12" height="11" viewBox="0 0 12 11"
                         fill="<? if ($average >= 2.5) { ?>#1D1D1B<? } else { ?>none<? } ?>"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z"
                              stroke="#1D1D1B" stroke-width="0.5" stroke-linejoin="round"/>
                    </svg>
                </li>
                <li>
                    <svg width="12" height="11" viewBox="0 0 12 11"
                         fill="<? if ($average >= 3.5) { ?>#1D1D1B<? } else { ?>none<? } ?>"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z"
                              stroke="#1D1D1B" stroke-width="0.5" stroke-linejoin="round"/>
                    </svg>
                </li>
                <li>
                    <svg width="12" height="11" viewBox="0 0 12 11"
                         fill="<? if ($average >= 4.5) { ?>#1D1D1B<? } else { ?>none<? } ?>"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z"
                              stroke="#1D1D1B" stroke-width="0.5" stroke-linejoin="round"/>
                    </svg>
                </li>
            </ul>
        </div>
        <div class="reviews_block">
            <?php if (have_comments()) : ?>
                <?php wp_list_comments(apply_filters('woocommerce_product_review_list_args', array('callback' => 'woocommerce_comments'))); ?>

                <?php
                if (get_comment_pages_count() > 1 && get_option('page_comments')) :
                    echo '<nav class="woocommerce-pagination">';
                    paginate_comments_links(
                        apply_filters(
                            'woocommerce_comment_pagination_args',
                            array(
                                'prev_text' => is_rtl() ? '&rarr;' : '&larr;',
                                'next_text' => is_rtl() ? '&larr;' : '&rarr;',
                                'type' => 'list',
                            )
                        )
                    );
                    echo '</nav>';
                endif;
                ?>
            <?php else : ?>
                <div class="reviews_block__reviewEmpty">
                    <span class="label">Отзывов пока нет</span>
                </div>
            <?php endif; ?>
            <div class="reviews_block_writeReview">
                <div class="reviewWrite_form" data-productId="<?php echo $product->get_id() ?>">
                    <div class="reviewWrite_form__content">
                        <div class="content_fields">
                            <div class="content_fields__nameRating">
                                <input type="text" placeholder="Имя" name="review_firstName"
                                       class="content_fields__text" maxlength="50">
                                <div class="starRating">
                                    <span class="starRating__label">Ваша оценка</span>
                                    <div class="starRating__wrap">
                                        <input class="starRating__input" id="starRating-5" type="radio" name="rating"
                                               value="5"/>
                                        <label class="starRating__ico" for="starRating-5" title=""></label>
                                        <input class="starRating__input" id="starRating-4" type="radio" name="rating"
                                               value="4"/>
                                        <label class="starRating__ico" for="starRating-4" title=""></label>
                                        <input class="starRating__input" id="starRating-3" type="radio" name="rating"
                                               value="3"/>
                                        <label class="starRating__ico" for="starRating-3" title=""></label>
                                        <input class="starRating__input" id="starRating-2" type="radio" name="rating"
                                               value="2"/>
                                        <label class="starRating__ico" for="starRating-2" title=""></label>
                                        <input class="starRating__input" id="starRating-1" type="radio" name="rating"
                                               value="1"/>
                                        <label class="starRating__ico" for="starRating-1" title=""></label>
                                    </div>
                                </div>
                            </div>
                            <textarea name="review_message" placeholder="Написать отзыв" cols="30" rows="10" maxlength="1000"
                                      class="content_fields__textarea"></textarea>
                        </div>
                        <div class="content_send">
                            <button class="content_send__form">Отправить</button>
                        </div>
                        <div class="content_reviewSend">
                            <span class="label">Отправлено!</span>
                            <img src="/wp-content/themes/kaifsmoke/assets/images/okey.png" alt="">
                            <span class="description">Отзыв появится на странице после того, как пройдет модерацию</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="reviews_block_writeReview">
				<a class="writeReview btnOrange">Написать отзыв</a>
				<div class="reviewWrite form" style="display: flex;">
					<div class="reviewWrite_block form_block" id="review_form_wrapper">
						<div class="reviewWrite_block__form form_block__form" id="review_form">
							<div class="reviewWriteBlock_form__title formBlock_form__title">
								<h2>Отзыв на</h2>
								<span><?php the_title(); ?></span>
							</div>
							<div class="reviewWriteBlock_form__content">
								<div class="content_fields">
									<input type="text" placeholder="Имя" name="firstName" class="content_fields__text">
									<div class="starRating">
										<div class="starRating__wrap">
											<input class="starRating__input" id="starRating-5" type="radio" name="rating" value="5" />
											<label class="starRating__ico fa fa-star-o fa-lg" for="starRating-5" title=""></label>
											<input class="starRating__input" id="starRating-4" type="radio" name="rating" value="4" />
											<label class="starRating__ico fa fa-star-o fa-lg" for="starRating-4" title=""></label>
											<input class="starRating__input" id="starRating-3" type="radio" name="rating" value="3" />
											<label class="starRating__ico fa fa-star-o fa-lg" for="starRating-3" title=""></label>
											<input class="starRating__input" id="starRating-2" type="radio" name="rating" value="2" />
											<label class="starRating__ico fa fa-star-o fa-lg" for="starRating-2" title=""></label>
											<input class="starRating__input" id="starRating-1" type="radio" name="rating" value="1" />
											<label class="starRating__ico fa fa-star-o fa-lg" for="starRating-1" title=""></label>
										</div>
									</div>
									<textarea name="message" placeholder="Написать отзыв" cols="30" rows="10"></textarea>
								</div>
								<div class="content_send">
									<button class="content_send__form">Отправить</button>
								</div>
							</div>
							<div class="callBack form" method="post">
								<div class="callBack_block form_block">
									<div class="callBack_block__form form_block__form">
										<div class="starRating">
											<div class="starRating__wrap">
												<input class="starRating__input" id="starRating-5" type="radio" name="rating" value="5" />
												<label class="starRating__ico fa fa-star-o fa-lg" for="starRating-5" title=""></label>
												<input class="starRating__input" id="starRating-4" type="radio" name="rating" value="4" />
												<label class="starRating__ico fa fa-star-o fa-lg" for="starRating-4" title=""></label>
												<input class="starRating__input" id="starRating-3" type="radio" name="rating" value="3" />
												<label class="starRating__ico fa fa-star-o fa-lg" for="starRating-3" title=""></label>
												<input class="starRating__input" id="starRating-2" type="radio" name="rating" value="2" />
												<label class="starRating__ico fa fa-star-o fa-lg" for="starRating-2" title=""></label>
												<input class="starRating__input" id="starRating-1" type="radio" name="rating" value="1" />
												<label class="starRating__ico fa fa-star-o fa-lg" for="starRating-1" title=""></label>
											</div>
										</div>

										<div class="callBackBlock_form__fields formBlock_form__fields">
											<div class="callBackBlockForm_fields__left formBlockForm_fields__left">
												<div>
													<input type="text" name="name" class="userName" placeholder="Имя*" />
												</div>

												<div>
													<input type="phone" name="phone" class="userPhone" placeholder="Телефон*" />
												</div>

												<div>
													<input type="email" name="email" class="userEmail" placeholder="Электронная почта*" />
												</div>
											</div>
											<div class="callBackBlockForm_fields__right formBlockForm_fields__right">
												<textarea name="message" id="" cols="30" rows="9" placeholder="Текст сообщения*"></textarea>
											</div>
										</div>
										<div class="callBackBlock_form_sendBtn formBlock_form_sendBtn">
											<button class="bttOrange">Отправить</button>
										</div>
									</div>
									<div class="callBack_block__send form_block__send">
										<h2>Отправлено!</h2>
										<img src="/wp-content/themes/kaifsmoke/assets/images/okey.png" alt="" />
										<span>Мы свяжемся с Вами в ближайшее время</span>
									</div>
								</div>
							</div>
							<?php
            // 	$comment_form = array(
            // 		/* translators: %s is product title */
            // 		'title_reply'         => have_comments() ? esc_html__('Add a review', 'woocommerce') : sprintf(esc_html__('Be the first to review &ldquo;%s&rdquo;', 'woocommerce'), get_the_title()),
            // 		/* translators: %s is product title */
            // 		'title_reply_to'      => esc_html__('Leave a Reply to %s', 'woocommerce'),
            // 		'title_reply_before'  => '<span id="reply-title" class="comment-reply-title">',
            // 		'title_reply_after'   => '</span>',
            // 		'comment_notes_after' => '',
            // 		'label_submit'        => esc_html__('Submit', 'woocommerce'),
            // 		'logged_in_as'        => '',
            // 		'comment_field'       => '',
            // 	);

            // 	$comment_form['fields'] = array();

            // 	foreach ($fields as $key => $field) {
            // 		$field_html  = '<p class="comment-form-' . esc_attr($key) . '">';
            // 		$field_html .= '<label for="' . esc_attr($key) . '">' . esc_html($field['label']);

            // 		if ($field['required']) {
            // 			$field_html .= '&nbsp;<span class="required">*</span>';
            // 		}

            // 		$field_html .= '</label><input id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" type="' . esc_attr($field['type']) . '" value="' . esc_attr($field['value']) . '" size="30" ' . ($field['required'] ? 'required' : '') . ' /></p>';

            // 		$comment_form['fields'][$key] = $field_html;
            // 	}

            // 	$account_page_url = wc_get_page_permalink('myaccount');
            // 	if ($account_page_url) {
            // 		/* translators: %s opening and closing link tags respectively */
            // 		$comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf(esc_html__('You must be %1$slogged in%2$s to post a review.', 'woocommerce'), '<a href="' . esc_url($account_page_url) . '">', '</a>') . '</p>';
            // 	}

            // 	if (wc_review_ratings_enabled()) {
            // 		$comment_form['comment_field'] = '<div class="comment-form-rating"><label for="rating">' . esc_html__('Your rating', 'woocommerce') . (wc_review_ratings_required() ? '&nbsp;<span class="required">*</span>' : '') . '</label><select name="rating" id="rating" required>
            // 	<option value="">' . esc_html__('Rate&hellip;', 'woocommerce') . '</option>
            // 	<option value="5">' . esc_html__('Perfect', 'woocommerce') . '</option>
            // 	<option value="4">' . esc_html__('Good', 'woocommerce') . '</option>
            // 	<option value="3">' . esc_html__('Average', 'woocommerce') . '</option>
            // 	<option value="2">' . esc_html__('Not that bad', 'woocommerce') . '</option>
            // 	<option value="1">' . esc_html__('Very poor', 'woocommerce') . '</option>
            // </select></div>';
            // 	}

            // 	$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__('Your review', 'woocommerce') . '&nbsp;<span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" required></textarea></p>';

            // 	comment_form(apply_filters('woocommerce_product_review_comment_form_args', $comment_form));
            ?>
						</div>
					</div>
				</div>
			</div> -->
        </div>
    </div>
</section>