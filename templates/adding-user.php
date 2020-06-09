<main class="page__main page__main--registration">
  <div class="container">
    <h1 class="page__title page__title--registration">Регистрация</h1>
  </div>
  <section class="registration container">
    <h2 class="visually-hidden">Форма регистрации</h2>
    <form class="registration__form form" action="registration.php" method="post" enctype="multipart/form-data">
      <div class="form__text-inputs-wrapper">
        <div class="form__text-inputs">
          <div class="registration__input-wrapper form__input-wrapper <?= isset($errors['email']) ? "form__input-section--error" : ""; ?>">
            <label class="registration__label form__label" for="registration-email">Электронная почта <span class="form__input-required">*</span></label>
            <div class="form__input-section">
              <input class="registration__input form__input" id="registration-email" type="email" name="email" placeholder="Укажите эл.почту" value="<?= getPostVal($_POST, 'email'); ?>">
              <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
              <div class="form__error-text">
                <h3 class="form__error-title"><?= $errors['email']['header'] ?></h3>
                <p class="form__error-desc"><?= $errors['email']['description'] ?></p>
              </div>
            </div>
          </div>
          <div class="registration__input-wrapper form__input-wrapper <?= isset($errors['login']) ? "form__input-section--error" : ""; ?>">
            <label class="registration__label form__label" for="registration-login">Логин <span class="form__input-required">*</span></label>
            <div class="form__input-section">
              <input class="registration__input form__input" id="registration-login" type="text" name="login" placeholder="Укажите логин" value="<?= getPostVal($_POST, 'login'); ?>">
              <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
              <div class="form__error-text">
                <h3 class="form__error-title"><?= $errors['login']['header'] ?></h3>
                <p class="form__error-desc"><?= $errors['login']['description'] ?></p>
              </div>
            </div>
          </div>
          <div class="registration__input-wrapper form__input-wrapper <?= isset($errors['password']) ? "form__input-section--error" : ""; ?>">
            <label class="registration__label form__label" for="registration-password">Пароль<span class="form__input-required">*</span></label>
            <div class="form__input-section">
              <input class="registration__input form__input" id="registration-password" type="password" name="password" placeholder="Придумайте пароль" value="<?= getPostVal($_POST, 'password'); ?>">
              <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
              <div class="form__error-text">
                <h3 class="form__error-title"><?= $errors['password']['header'] ?></h3>
                <p class="form__error-desc"><?= $errors['password']['description'] ?></p>
              </div>
            </div>
          </div>
          <div class="registration__input-wrapper form__input-wrapper <?= isset($errors['password-repeat']) ? "form__input-section--error" : ""; ?>">
            <label class="registration__label form__label" for="registration-password-repeat">Повтор пароля<span class="form__input-required">*</span></label>
            <div class="form__input-section">
              <input class="registration__input form__input" id="registration-password-repeat" type="password" name="password-repeat" placeholder="Повторите пароль" value="<?= getPostVal($_POST, 'password-repeat'); ?>">
              <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
              <div class="form__error-text">
                <h3 class="form__error-title"><?= $errors['password-repeat']['header'] ?></h3>
                <p class="form__error-desc"><?= $errors['password-repeat']['description'] ?></p>
              </div>
            </div>
          </div>
        </div>

        <?php require 'templates/adding-errors.php'; ?>

      </div>

      <div class="registration__input-wrapper form__input-wrapper">
        <label class="registration__label form__label" for="userpic-file">Выбор аватара</label>
          <div class="adding-post__input-file-container form__input-container form__input-container--file">
            <input id="userpic-file" type="file" name="userpic-file" title=" ">
          </div>
      </div>

      <button class="registration__submit button button--main" type="submit">Отправить</button>
    </form>
  </section>
</main>
