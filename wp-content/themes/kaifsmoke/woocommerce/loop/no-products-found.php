<?php
/**
 * Displayed when no products are found matching the current query
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/no-products-found.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

?>

  <div class="productsWrapper table">
    <div class="productsEmpty">
      <span class="productsEmpty_label">Товары не найдены! 
      
    </span></br>
    <a href="./catalog/">Продолжить просмотр</a>
      
    </div>
   
  
</div>


<style>
  .productsWrapper {
    min-height: calc(100vh - 273px);
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .productsWrapper a{
    width: auto;
    border-radius: 5px;
    color: #fff;
    background: #ef7d00;
    padding: 10px 20px;
  }

  .productsEmpty{
    display: flex;
    flex-direction: column;
  }
  

</style>
