<?php
/**
 * Template Name: Шаблон одной статьи
 * Используется для single post
 */

get_header();

while (have_posts()) : the_post();
?>

<article class="post-page">
    <section class="section">
        <div class="container">
            <?php agency_breadcrumbs(); ?>

            <div class="post-page__header">
                <div class="post-page__meta">
                    <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
                    <?php
                    $categories = get_the_category();
                    if ($categories) {
                        echo ' · <a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . $categories[0]->name . '</a>';
                    }
                    ?>
                </div>
                <h1 class="post-page__title"><?php the_title(); ?></h1>
            </div>

            <div class="blog-layout">
                <div class="post-page__content">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-page__image">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="post-page__text">
                        <?php the_content(); ?>
                    </div>

                    <!-- Теги -->
                    <?php $tags = get_the_tags(); ?>
                    <?php if ($tags) : ?>
                        <div class="post-page__tags">
                            <span>Теги:</span>
                            <?php foreach ($tags as $tag) : ?>
                                <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="post-tag">
                                    <?php echo esc_html($tag->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- CTA после статьи -->
                    <div class="post-page__cta">
                        <h3>Нужна помощь с внедрением?</h3>
                        <p>Мы не просто пишем статьи — мы внедряем описанные механики для бизнеса. Оставьте заявку на бесплатный аудит.</p>
                        <a href="#auditForm" class="btn btn--accent">Получить аудит</a>
                    </div>
                </div>

                <?php get_sidebar(); ?>
            </div>
        </div>
    </section>
</article>

<?php
endwhile;

get_footer();
