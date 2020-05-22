<section class="adding-post__text tabs__content tabs__content--active">
    <h2 class="visually-hidden">Форма добавления текста</h2>
    <form class="adding-post__form form" action="add.php" method="post">
        <div class="form__text-inputs-wrapper">
            <div class="form__text-inputs">
                <div class="adding-post__input-wrapper form__input-wrapper <?= isset($errors['text-heading']) ? "form__input-section--error" : ""; ?>">
                    <label class="adding-post__label form__label" for="text-heading">Заголовок <span class="form__input-required">*</span></label>
                    <div class="form__input-section">
                        <input class="adding-post__input form__input" id="text-heading" type="text" name="text-heading" placeholder="Введите заголовок" value="<?= getPostVal($_POST, 'text-heading'); ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?= $errors['text-heading']['header'] ?></h3>
                            <p class="form__error-desc"><?= $errors['text-heading']['description'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__textarea-wrapper form__textarea-wrapper <?= isset($errors['text-text']) ? "form__input-section--error" : ""; ?>">
                    <label class="adding-post__label form__label" for="text-text">Текст поста <span class="form__input-required">*</span></label>
                    <div class="form__input-section">
                        <textarea class="adding-post__textarea form__textarea form__input" id="text-text" name="text-text" placeholder="Введите текст публикации"><?= getPostVal($_POST, 'text-text'); ?></textarea>
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?= $errors['text-text']['header'] ?></h3>
                            <p class="form__error-desc"><?= $errors['text-text']['description'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__input-wrapper form__input-wrapper <?= isset($errors['text-tags']) ? "form__input-section--error" : ""; ?>">
                    <label class="adding-post__label form__label" for="text-tags">Теги</label>
                    <div class="form__input-section">
                        <input class="adding-post__input form__input" id="text-tags" type="text" name="text-tags" placeholder="Введите теги" value="<?= getPostVal($_POST, 'text-tags'); ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?= $errors['text-tags']['header'] ?></h3>
                            <p class="form__error-desc"><?= $errors['text-tags']['description'] ?></p>
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
        <input type="hidden" name="type_id" value="3">
    </form>
</section>
