<div class="adding-post__input-wrapper form__input-wrapper">
    <label class="adding-post__label form__label" for=<?=$tags?> >Теги</label>
    <div class="form__input-section">
        <input class="adding-post__input form__input" id=<?=$tags?> type="text" name=<?=$tags?> placeholder="Введите теги" value="<?= getPostVal($_POST, $tags); ?>">
        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
        <div class="form__error-text">
            <h3 class="form__error-title"><?= $errors[$tags]['header'] ?></h3>
            <p class="form__error-desc"><?= $errors[$tags]['description'] ?></p>
        </div>
    </div>
</div>
