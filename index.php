<?php
/**
 * Главный фолбэк-шаблон
 */

get_header();
?>

<main class="main-content">
    <div class="container">
        <?php agency_breadcrumbs(); ?>

        <?php if (have_posts()) : ?>
            <div class="posts-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <article <?php post_class('post-card'); ?>>
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>" class="post-card__image">
                                <?php the_post_thumbnail('blog-thumbnail'); ?>
                            </a>
                        <?php endif; ?>

                        <div class="post-card__content">
                            <h2 class="post-card__title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>

                            <div class="post-card__meta">
                                <time datetime="<?php echo get_the_date('c'); ?>">
                                    <?php echo get_the_date(); ?>
                                </time>
                            </div>

                            <div class="post-card__excerpt">
                                <?php the_excerpt(); ?>
                            </div>

                            <a href="<?php the_permalink(); ?>" class="post-card__read-more">
                                Читать далее →
                            </a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <?php the_posts_pagination(array(
                'prev_text' => '← Назад',
                'next_text' => 'Вперёд →',
            )); ?>

        <?php else : ?>
            <p>Записей не найдено.</p>
        <?php endif; ?>
    </div>
</main>

<?php
get_sidebar();
get_footer();
