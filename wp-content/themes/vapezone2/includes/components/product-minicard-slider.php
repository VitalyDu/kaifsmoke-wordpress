 <?php
    $average = $post->get_average_rating();
    ?>
 <div class="productsBlock_carousel__item product_miniCard" data-productid="<? echo $post->get_id(); ?>" data-productName="<?= $post->get_title() ?>" data-productName="<?= $post->get_title() ?>">
     <div class="product_addToFavorites">
         <button>
             <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                 <path d="M19.2001 8.61987C19.2001 12.2199 12 18.7314 12 18.7314C12 18.7314 4.80005 12.2199 4.80005 8.61987C4.80005 4.69111 9.60005 3.16234 12 7.0911C14.4 3.16234 19.2 5.01987 19.2001 8.61987Z" stroke="#1D1D1B" />
             </svg>
         </button>
     </div>
     <a href="<?= $post->get_permalink() ?>" class="product_image">
         <?= $post->get_image('large', 'class=newProduct_image') ?>
     </a>
     <div class="product_rate">
         <ul>
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
     <div class="product_name">
         <a href="<?= $post->get_permalink() ?>"><?= $post->get_title() ?></a>
     </div>
     <div class="product_price">
         <span><?= $post->get_price() ?> ??????.</span>
     </div>
     <div class="product_buttons">
         <?
            //        $multisklad = get_post_meta($post->get_id(), 'multi_sklad', true);
            //        preg_match('/???????????????? ??????????&0;(\d*)&1;/', $multisklad, $matches);
            //        $product_real_stock_quantity = intval($matches[1]);

            if (is_user_logged_in()) { ?>
             <? if (strpos(get_user_meta(get_current_user_id(), 'cart', true), $post->get_id() . ',') !== false) {
                ?>
                 <a href="/reservation" class="btn tertiary active m goToCart_btn" data-productId="<? echo $post->get_id(); ?>" data-productName="<?= $post->get_title() ?>">???????????? ????????????????????????</a>
             <?
                } elseif ($post->is_in_stock()) { ?>
                 <button class="btn tertiary m addToCardBtn" data-productId="<? echo $post->get_id(); ?>" data-productName="<?= $post->get_title() ?>">??????????????????????????</button>
             <? } else { ?>
                 <a href="<?= $post->get_permalink() ?>" class="btn secondary m">????????????????????</a>
             <?php } ?>
         <? } else { ?>
             <button class="btn tertiary m addToCartNoAuth_btn">??????????????????????????</button>
         <? } ?>
     </div>
 </div>