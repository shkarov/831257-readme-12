<section class="adding-post__video tabs__content tabs__content--active">
    <h2 class="visually-hidden">Форма добавления видео</h2>
    <form class="adding-post__form form" action="add.php" method="post" enctype="multipart/form-data">
        <div class="form__text-inputs-wrapper">
            <div class="form__text-inputs">
                <div class="adding-post__input-wrapper form__input-wrapper <?= isset($errors['video-heading']) ? "form__input-section--error" : ""; ?>">
                    <label class="adding-post__label form__label" for="video-heading">Заголовок <span class="form__input-required">*</span></label>
                    <div class="form__input-section">
                        <input class="adding-post__input form__input" id="video-heading" type="text" name="video-heading" placeholder="Введите заголовок" value="<?= getPostVal($_POST, 'video-heading'); ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?= $errors['video-heading']['header'] ?></h3>
                            <p class="form__error-desc"><?= $errors['video-heading']['description'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__input-wrapper form__input-wrapper <?= isset($errors['video-url']) ? "form__input-section--error" : ""; ?>">
                    <label class="adding-post__label form__label" for="video-url">Ссылка youtube <span class="form__input-required">*</span></label>
                    <div class="form__input-section">
                        <input class="adding-post__input form__input" id="video-url" type="text" name="video-url" placeholder="Введите ссылку" value="<?= getPostVal($_POST, 'video-url'); ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?= $errors['video-url']['header'] ?></h3>
                            <p class="form__error-desc"><?= $errors['video-url']['description'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__input-wrapper form__input-wrapper <?= isset($errors['video-tags']) ? "form__input-section--error" : ""; ?>">
                    <label class="adding-post__label form__label" for="video-tags">Теги</label>
                    <div class="form__input-section">
                        <input class="adding-post__input form__input" id="video-tags" type="text" name="video-tags" placeholder="Введите ссылку" value="<?= getPostVal($_POST, 'video-tags'); ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?= $errors['video-tags']['header'] ?></h3>
                            <p class="form__error-desc"><?= $errors['video-tags']['description'] ?></p>
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
        <input type="hidden" name="type_id" value="2">
    </form>
</section>

