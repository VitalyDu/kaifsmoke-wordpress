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
                         fill="<? if ($average >= 0.5) { ?>#000000<? } else { ?>none<? } ?>"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z"
                              stroke="#000000" stroke-width="0.5" stroke-linejoin="round"/>
                    </svg>
                </li>
                <li>
                    <svg width="12" height="11" viewBox="0 0 12 11"
                         fill="<? if ($average >= 1.5) { ?>#000000<? } else { ?>none<? } ?>"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z"
                              stroke="#000000" stroke-width="0.5" stroke-linejoin="round"/>
                    </svg>
                </li>
                <li>
                    <svg width="12" height="11" viewBox="0 0 12 11"
                         fill="<? if ($average >= 2.5) { ?>#000000<? } else { ?>none<? } ?>"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z"
                              stroke="#000000" stroke-width="0.5" stroke-linejoin="round"/>
                    </svg>
                </li>
                <li>
                    <svg width="12" height="11" viewBox="0 0 12 11"
                         fill="<? if ($average >= 3.5) { ?>#000000<? } else { ?>none<? } ?>"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z"
                              stroke="#000000" stroke-width="0.5" stroke-linejoin="round"/>
                    </svg>
                </li>
                <li>
                    <svg width="12" height="11" viewBox="0 0 12 11"
                         fill="<? if ($average >= 4.5) { ?>#000000<? } else { ?>none<? } ?>"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 1L7.12257 4.45492H10.7553L7.81636 6.59017L8.93893 10.0451L6 7.90983L3.06107 10.0451L4.18364 6.59017L1.24472 4.45492H4.87743L6 1Z"
                              stroke="#000000" stroke-width="0.5" stroke-linejoin="round"/>
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
                            <button class="btn l primary content_send__form">Отправить</button>
                        </div>
                        <div class="content_reviewSend">
                            <span class="label">Отправлено!</span>
                            <img src="/wp-content/themes/vapezone/assets/images/okey.png" alt="">
                            <span class="description">Отзыв появится на странице после того, как пройдет модерацию</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>