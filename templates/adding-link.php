<section class="adding-post__link tabs__content tabs__content--active">
    <h2 class="visually-hidden">Форма добавления ссылки</h2>
    <form class="adding-post__form form" action="add.php" method="post">
        <div class="form__text-inputs-wrapper">
            <div class="form__text-inputs">
                <div class="adding-post__input-wrapper form__input-wrapper <?= isset($errors['link-heading']) ? "form__input-section--error" : ""; ?>">
                    <label class="adding-post__label form__label" for="link-heading">Заголовок <span class="form__input-required">*</span></label>
                    <div class="form__input-section">
                        <input class="adding-post__input form__input" id="link-heading" type="text" name="link-heading" placeholder="Введите заголовок" value="<?= getPostVal($_POST, 'link-heading'); ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?= $errors['link-heading']['header'] ?></h3>
                            <p class="form__error-desc"><?= $errors['link-heading']['description'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__textarea-wrapper form__input-wrapper <?= isset($errors['link-url']) ? "form__input-section--error" : ""; ?>">
                    <label class="adding-post__label form__label" for="link-url">Ссылка <span class="form__input-required">*</span></label>
                    <div class="form__input-section">
                        <input class="adding-post__input form__input" id="link-url" type="text" name="link-url" value="<?= getPostVal($_POST, 'link-url'); ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?= $errors['link-url']['header'] ?></h3>
                            <p class="form__error-desc"><?= $errors['link-url']['description'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__input-wrapper form__input-wrapper <?= isset($errors['link-tags']) ? "form__input-section--error" : ""; ?>">
                    <label class="adding-post__label form__label" for="link-tags">Теги</label>
                    <div class="form__input-section">
                        <input class="adding-post__input form__input" id="link-tags" type="text" name="link-tags" placeholder="Введите ссылку" value="<?= getPostVal($_POST, 'link-tags'); ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?= $errors['link-tags']['header'] ?></h3>
                            <p class="form__error-desc"><?= $errors['link-tags']['description'] ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form__invalid-block <?= ($errors === []) ? "visually-hidden" : ""?>">
                <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                <ul class="form__invalid-list">
                    <?php foreach ($errors as $error) :?>
                        <li class="form__invalid-item"><?= $error['report'] ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="adding-post__buttons">
            <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
                <a class="adding-post__close" href="add.php">Закрыть</a>
        </div>
        <input type="hidden" name="type_id" value="5">
    </form>
</section>
