<?php
/*
    Template Name: Новости
*/
?>
<?php
get_header();
?>

    <section class="news">
        <div class="container">
            <div class="section_title">
                <h2>Новости</h2>
            </div>
            <div class="breadcrumbs">
                <ul>
                    <?php true_breadcrumbs(); ?>
                </ul>
            </div>
            <div id="news_ajax">
                <?php
                $page = 1;
                if (!empty($_GET['pagination'])) {
                    $page = intval($_GET['pagination']);
                    if ($page < 1) {
                        $page = 1;
                    }
                }
                $tag_id = '';
                if (!empty($_GET['tag_id'])) {
                    $tag_id = $_GET['tag_id'];
                }
                //vz-catalog-news.php
                VZNewsCatalog::print($page, $tag_id);
                ?>
            </div>
            <?php VZNewsCatalog::printAfter(); ?>
        </div>
    </section>


<?php
get_footer();
?>