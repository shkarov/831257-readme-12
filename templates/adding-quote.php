<section class="adding-post__quote tabs__content tabs__content--active">
    <h2 class="visually-hidden">Форма добавления цитаты</h2>
    <form class="adding-post__form form" action="add.php" method="post">
        <div class="form__text-inputs-wrapper">
            <div class="form__text-inputs">
                <div class="adding-post__input-wrapper form__input-wrapper <?= isset($errors['quote-heading']) ? "form__input-section--error" : ""; ?>">
                    <label class="adding-post__label form__label" for="quote-heading">Заголовок <span class="form__input-required">*</span></label>
                    <div class="form__input-section">
                        <input class="adding-post__input form__input" id="quote-heading" type="text" name="quote-heading" placeholder="Введите заголовок" value="<?= getPostVal($_POST, 'quote-heading'); ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?= $errors['quote-heading']['header'] ?></h3>
                            <p class="form__error-desc"><?= $errors['quote-heading']['description'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__input-wrapper form__textarea-wrapper <?= isset($errors['quote-text']) ? "form__input-section--error" : ""; ?>">
                    <label class="adding-post__label form__label" for="quote-text">Текст цитаты <span class="form__input-required">*</span></label>
                    <div class="form__input-section">
                        <textarea class="adding-post__textarea adding-post__textarea--quote form__textarea form__input" id="quote-text" name="quote-text" placeholder="Текст цитаты"><?= getPostVal($_POST, 'quote-text'); ?></textarea>
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?= $errors['quote-text']['header'] ?></h3>
                            <p class="form__error-desc"><?= $errors['quote-text']['description'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__textarea-wrapper form__input-wrapper <?= isset($errors['quote-author']) ? "form__input-section--error" : ""; ?>">
                    <label class="adding-post__label form__label" for="quote-author">Автор <span class="form__input-required">*</span></label>
                    <div class="form__input-section">
                        <input class="adding-post__input form__input" id="quote-author" type="text" name="quote-author" value="<?= getPostVal($_POST, 'quote-author'); ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?= $errors['quote-author']['header'] ?></h3>
                            <p class="form__error-desc"><?= $errors['quote-author']['description'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__input-wrapper form__input-wrapper<?= isset($errors['quote-tags']) ? "form__input-section--error" : ""; ?>">
                    <label class="adding-post__label form__label" for="quote-tags">Теги</label>
                    <div class="form__input-section">
                        <input class="adding-post__input form__input" id="quote-tags" type="text" name="quote-tags" placeholder="Введите теги" value="<?= getPostVal($_POST, 'quote-tags'); ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?= $errors['quote-tags']['header'] ?></h3>
                            <p class="form__error-desc"><?= $errors['quote-tags']['description'] ?></p>
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
        <input type="hidden" name="type_id" value="4">
    </form>
</section>
