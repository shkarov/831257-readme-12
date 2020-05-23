<div class="adding-post__input-wrapper form__input-wrapper <?= isset($errors[$heading]) ? "form__input-section--error" : ""; ?>">
    <label class="adding-post__label form__label" for=<?=$heading?> >Заголовок <span class="form__input-required">*</span></label>
    <div class="form__input-section">
        <input class="adding-post__input form__input" id=<?=$heading?> type="text" name=<?=$heading?> placeholder="Введите заголовок" value="<?= getPostVal($_POST, $heading); ?>">
        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
        <div class="form__error-text">
            <h3 class="form__error-title"><?= $errors[$heading]['header'] ?></h3>
            <p class="form__error-desc"><?= $errors[$heading]['description'] ?></p>
        </div>
    </div>
</div>
