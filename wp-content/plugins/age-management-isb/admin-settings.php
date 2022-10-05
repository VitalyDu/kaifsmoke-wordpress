 <h1>Настройки управления ограниченным просмотром содержимого</h1>
<span>Выберите категории, которые не должны быть заблюрены на сайте для неавторизованного пользователя</span>


 <form>
   <select name="select" size="20" multiple>
      <?php
      $args = array('taxonomy' => 'product_cat');
      $list_of_categories = get_categories( $args );
    

    foreach ($list_of_categories as $category){
    echo '<option  value="'.$category->category_nicename.'">';
      print_r($category->name);

    echo '</option>';
    }
  ?>


   </select>

   <?php 
    // $args = array(
    //     'taxonomy' => 'product_cat',
    //     'hide_empty' => false,
    //   );
    //   $product_categories = get_terms( $args );

    //   $count = count($product_categories);

    //   if ( $count > 0 ){
    //     foreach ( $product_categories as $product_category ) {
    //         $thumbnail_id = get_woocommerce_term_meta( $product_category->term_id, 'thumbnail_id', true );
    //         echo '<div class="col-md-6 col-sm-12 col-xs-12">';
    //         echo '<article class="type-post">';
    //         echo '<div class="entry-cover" style="width: 388px; height: 295px;">';
    //         echo  '<a href="' . get_term_link( $product_category ) . '"><img style="background-image: url('.  wp_get_attachment_url( $thumbnail_id ) .')!important;background-size: 100%;background-repeat: no-repeat;background-size: cover; width: 100%; height: 100%;" /></a>';
    //         echo  '</div>';
    //         echo  '<div class="entry-block">';
    //         echo  '<div class="entry-title">';
    //         echo  '<a href="' . get_term_link( $product_category ) . '" title="' . $product_category->name . '"><h3>' . $product_category->name . '</h3></a>';
    //         echo  '</div>';
    //         echo  '<hr>';
    //         echo  '<div class="entry-content">';
    //         echo  '<p>' . $product_category->description . '</p>';
    //         echo  '</div>';
    //         echo  '</div>';
    //         echo  '</article>';
    //         echo  '</div>';
            

    //     }
    //   }
   ?>
   <input type="submit" value="Отправить">
  </form>



