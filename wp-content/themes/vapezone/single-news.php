<?php
/*
    Template Name: Новость
*/
?>
<?php
get_header();
?>
<section class="singleNews">
    <div class="container">
        <!--#include virtual="/parts/breadcrumbs.html" -->

        <div class="singleNews_categoryDateTime">
            <span class="singleNews_categoryDateTime_category">
                <?php the_tags('', '', ''); ?>
            </span>
            <span class="singleNews_categoryDateTime_dateTime">
                <span class="date">
                    <?php echo get_the_date(); ?>
                </span>
            </span>
        </div>
        <div class="singleNews_teaser">
            <img src="<?php the_field('banner'); ?>" alt="<?php the_field('title'); ?>">
        </div>
        <div class="singleNews_block">
            <div class="first_content singleNews_block__content">
                <h2 class="first_content__title">
                    <?php the_field('title'); ?>
                </h2>
                <div class="first_content__text singleNews_block__contentText">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
get_footer();
?>