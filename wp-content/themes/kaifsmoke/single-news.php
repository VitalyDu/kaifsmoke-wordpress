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
        <div class="singleNews_block">
            <h1 class="singleNews_block__title">
                <?php the_field('title'); ?>
            </h1>
            <div class="breadcrumbs">
                <ul>
                    <?php true_breadcrumbs(); ?>
                </ul>
            </div>
            <div class="singleNews_block__categoryDateTime">
                <span class="singleNews_categoryDateTime_category">
                    <?php the_tags('', '', ''); ?>
                </span>
                <span class="singleNews_categoryDateTime_dateTime">
                    <span class="date">
                        <?php echo get_the_date(); ?>
                    </span>
                </span>
            </div>
            <div class="singleNews_block__text">
                <?php the_content(); ?>
            </div>
        </div>
    </div>
</section>
<?php
get_footer();
?>