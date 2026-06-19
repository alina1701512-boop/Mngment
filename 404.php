<?php
/**
 * Страница 404
 */

get_header();
?>

<main class="main-content">
    <section class="section section--gray error-404">
        <div class="container error-404__inner">
            <h1 class="error-404__title">Страница не найдена</h1>

            <p class="error-404__subtitle">
                Но лиды находятся всегда. Возможно, вы искали одно из наших решений:
            </p>

            <div class="error-404__links">
                <div class="error-404__col">
                    <h3>Трафик и лиды</h3>
                    <ul>
                        <li><a href="<?php echo home_url('/services/parsing-lidogeneraciya/'); ?>">Парсинг и лидогенерация</a></li>
                        <li><a href="<?php echo home_url('/services/seo-prodvizhenie/'); ?>">SEO-продвижение</a></li>
                        <li><a href="<?php echo home_url('/services/kontekstnaya-reklama/'); ?>">Контекстная реклама</a></li>
                        <li><a href="<?php echo home_url('/services/smm-target/'); ?>">SMM и таргет</a></li>
                    </ul>
                </div>

                <div class="error-404__col">
                    <h3>Автоматизация</h3>
                    <ul>
                        <li><a href="<?php echo home_url('/services/vnedrenie-amocrm/'); ?>">Внедрение AmoCRM</a></li>
                        <li><a href="<?php echo home_url('/services/chat-boty/'); ?>">Чат-боты</a></li>
                        <li><a href="<?php echo home_url('/services/ai-agenty/'); ?>">AI-агенты</a></li>
                        <li><a href="<?php echo home_url('/services/marketing-automation/'); ?>">Автоматизация маркетинга</a></li>
                    </ul>
                </div>

                <div class="error-404__col">
                    <h3>Сайты и брендинг</h3>
                    <ul>
                        <li><a href="<?php echo home_url('/services/razrabotka-sajtov/'); ?>">Разработка сайтов</a></li>
                        <li><a href="<?php echo home_url('/services/kontent-marketing/'); ?>">Контент-маркетинг</a></li>
                        <li><a href="<?php echo home_url('/services/brending-firmennyj-stil/'); ?>">Брендинг и фирменный стиль</a></li>
                    </ul>
                </div>
            </div>

            <a href="<?php echo home_url('/'); ?>" class="btn btn--primary error-404__btn">
                Вернуться на главную
            </a>
        </div>
    </section>
</main>

<?php
get_footer();
