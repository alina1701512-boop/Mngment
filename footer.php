<?php
/**
 * Футер сайта
 */
?>
</main><!-- .main-content -->

<footer class="footer" id="footer">
    <div class="container">

        <!-- Верхняя часть футера: 4 колонки -->
        <div class="footer__grid">

            <!-- Колонка 1: Трафик -->
            <div class="footer__col">
                <h4 class="footer__col-title">Проблемы с трафиком</h4>
                <?php
                if (has_nav_menu('footer_1')) {
                    wp_nav_menu(array(
                        'theme_location' => 'footer_1',
                        'container'      => false,
                        'menu_class'     => 'footer__menu',
                        'fallback_cb'    => false,
                    ));
                } else {
                    echo '<ul class="footer__menu">';
                    echo '<li><a href="' . home_url('/services/seo-prodvizhenie/') . '">SEO-продвижение</a></li>';
                    echo '<li><a href="' . home_url('/services/kontekstnaya-reklama/') . '">Контекстная реклама</a></li>';
                    echo '<li><a href="' . home_url('/services/smm-target/') . '">SMM и таргет</a></li>';
                    echo '<li><a href="' . home_url('/services/parsing-lidogeneraciya/') . '">Парсинг и лидогенерация</a></li>';
                    echo '</ul>';
                }
                ?>
            </div>

            <!-- Колонка 2: Конверсия -->
            <div class="footer__col">
                <h4 class="footer__col-title">Проблемы с конверсией</h4>
                <?php
                if (has_nav_menu('footer_2')) {
                    wp_nav_menu(array(
                        'theme_location' => 'footer_2',
                        'container'      => false,
                        'menu_class'     => 'footer__menu',
                        'fallback_cb'    => false,
                    ));
                } else {
                    echo '<ul class="footer__menu">';
                    echo '<li><a href="' . home_url('/services/razrabotka-sajtov/') . '">Разработка сайтов</a></li>';
                    echo '<li><a href="' . home_url('/services/kontent-marketing/') . '">Контент-маркетинг</a></li>';
                    echo '<li><a href="' . home_url('/services/chat-boty/') . '">Чат-боты</a></li>';
                    echo '<li><a href="' . home_url('/services/brending-firmennyj-stil/') . '">Брендинг и фирменный стиль</a></li>';
                    echo '</ul>';
                }
                ?>
            </div>

            <!-- Колонка 3: Управление -->
            <div class="footer__col">
                <h4 class="footer__col-title">Проблемы с управлением</h4>
                <?php
                if (has_nav_menu('footer_3')) {
                    wp_nav_menu(array(
                        'theme_location' => 'footer_3',
                        'container'      => false,
                        'menu_class'     => 'footer__menu',
                        'fallback_cb'    => false,
                    ));
                } else {
                    echo '<ul class="footer__menu">';
                    echo '<li><a href="' . home_url('/services/vnedrenie-amocrm/') . '">Внедрение AmoCRM</a></li>';
                    echo '<li><a href="' . home_url('/services/veb-analitika/') . '">Веб-аналитика</a></li>';
                    echo '<li><a href="' . home_url('/services/marketing-automation/') . '">Автоматизация маркетинга</a></li>';
                    echo '<li><a href="' . home_url('/services/ai-agenty/') . '">AI-агенты</a></li>';
                    echo '</ul>';
                }
                ?>
            </div>

            <!-- Колонка 4: Всё сразу -->
            <div class="footer__col">
                <h4 class="footer__col-title">Всё сразу</h4>
                <?php
                if (has_nav_menu('footer_4')) {
                    wp_nav_menu(array(
                        'theme_location' => 'footer_4',
                        'container'      => false,
                        'menu_class'     => 'footer__menu',
                        'fallback_cb'    => false,
                    ));
                } else {
                    echo '<ul class="footer__menu">';
                    echo '<li><a href="' . home_url('/services/marketing-pod-kljuch/') . '">Комплексный маркетинг под ключ</a></li>';
                    echo '<li><a href="' . home_url('/cases/') . '">Кейсы</a></li>';
                    echo '<li><a href="' . home_url('/blog/') . '">Блог</a></li>';
                    echo '<li><a href="' . home_url('/contacts/') . '">Контакты</a></li>';
                    echo '</ul>';
                }
                ?>
            </div>

        </div>

        <!-- Нижняя часть футера -->
        <div class="footer__bottom">
            <p class="footer__copyright">
                &copy; <?php echo agency_get_current_year(); ?> <?php bloginfo('name'); ?>.
                Все права защищены.
            </p>
            <a href="<?php echo esc_url(agency_get_telegram_url()); ?>" class="footer__telegram" target="_blank" rel="noopener noreferrer">
                Telegram
            </a>
        </div>

    </div>
</footer>

<!-- КНОПКА «НАВЕРХ» -->
<button class="scroll-to-top" id="scrollToTop" aria-label="Наверх">
    ↑
</button>

<?php wp_footer(); ?>
</body>
</html>
