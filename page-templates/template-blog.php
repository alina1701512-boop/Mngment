<?php
/**
 * Template Name: Шаблон блога (архив)
 */

get_header();
?>

<section class="section">
    <div class="container">
        <?php agency_breadcrumbs(); ?>

        <h1 class="section__title">Блог: гайды, кейсы и разборы по digital и автоматизации</h1>

        <div class="blog-layout">
            <div class="blog-posts">
                <?php
                $paged = get_query_var('paged') ? get_query_var('paged') : 1;
                $blog_query = new WP_Query(array(
                    'post_type'      => 'post',
                    'posts_per_page' => 9,
                    'paged'          => $paged,
                ));

                if ($blog_query->have_posts()) :
                    while ($blog_query->have_posts()) : $blog_query->the_post();
                ?>
                    <article class="post-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>" class="post-card__image">
                                <?php the_post_thumbnail('blog-thumbnail'); ?>
                            </a>
                        <?php endif; ?>

                        <div class="post-card__content">
                            <div class="post-card__meta">
                                <time datetime="<?php echo get_the_date('c'); ?>">
                                    <?php echo get_the_date(); ?>
                                </time>
                                <?php
                                $categories = get_the_category();
                                if ($categories) {
                                    echo ' · <a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . $categories[0]->name . '</a>';
                                }
                                ?>
                            </div>
                            <h2 class="post-card__title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            <div class="post-card__excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="post-card__read-more">
                                Читать далее →
                            </a>
                        </div>
                    </article>
                <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    echo '<p>Статей пока нет. Скоро здесь появятся полезные материалы.</p>';
                endif;
                ?>

                <?php if ($blog_query->max_num_pages > 1) : ?>
                    <div class="pagination">
                        <?php
                        echo paginate_links(array(
                            'total'   => $blog_query->max_num_pages,
                            'current' => $paged,
                        ));
                        ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php get_sidebar(); ?>
        </div>
    </div>
</section>

<?php
get_footer();
