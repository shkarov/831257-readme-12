<main class="page__main page__main--messages">
    <h1 class="visually-hidden">Личные сообщения</h1>
    <section class="messages tabs">
        <h2 class="visually-hidden">Сообщения</h2>
        <div class="messages__contacts">
            <ul class="messages__contacts-list tabs__list">

                <?php foreach ($contacts as $contact) : ?>

                    <li class="messages__contacts-item">
                        <a class="messages__contacts-tab tabs__item <?= ($contact['user_id'] === $user_id_active) ? "messages__contacts-tab--active tabs__item tabs__item--active" : ""; ?>" href="messages.php?user_id=<?= $contact['user_id'] ?>">
                            <div class="messages__avatar-wrapper">
                                <img class="messages__avatar <?=empty($contact['avatar']) ? 'visually-hidden' : '' ?>" src="<?= $contact['avatar']; ?>" alt="Аватар пользователя">
                            </div>
                            <div class="messages__info">
                                <span class="messages__contact-name">
                                    <?= htmlspecialchars($contact['login']); ?>
                                </span>
                                <div class="messages__preview">
                                    <p class="messages__preview-text">
                                        <?= htmlspecialchars($contact['text']); ?>
                                    </p>
                                    <?php
                                       $dateTitle = strftime('%d.%m.%Y %H:%M', strtotime($contact['creation_time']));
                                       $dateView = dateDifferent($contact['creation_time']);
                                    ?>
                                    <time class="messages__preview-time" datetime="<?= $contact['creation_time'] ?>">
                                        <?= $dateView == '0 минут назад' ? '' : $dateView ?>
                                    </time>
                                </div>
                            </div>
                        </a>
                    </li>

                <?php endforeach ?>

            </ul>
        </div>

        <div class="messages__chat">
          <div class="messages__chat-wrapper">
            <ul class="messages__list tabs__content tabs__content--active">

                <?php foreach ($messages as $message) : ?>

                    <li class="messages__item <?= ($message['user_id'] === $user_id_login) ? "messages__item--my" : ""; ?>">
                        <div class="messages__info-wrapper">
                            <div class="messages__item-avatar">
                                <a class="messages__author-link" href="profile.php?user_id=<?=$message['user_id']?>">
                                    <img class="messages__avatar <?=empty($message['avatar']) ? 'visually-hidden' : '' ?>" src="<?= $message['avatar']; ?>" alt="Аватар пользователя">
                                </a>
                            </div>
                            <div class="messages__item-info">
                                <a class="messages__author" href="profile.php?user_id=<?=$message['user_id']?>">
                                    <?= htmlspecialchars($message['login']); ?>
                                </a>
                                <?php
                                    $dateView = dateDifferent($message['creation_time']);
                                ?>
                                <time class="messages__time" datetime="<?= $message['creation_time'] ?>">
                                    <?= $dateView ?>
                                </time>
                            </div>
                        </div>
                        <p class="messages__text">
                            <?= htmlspecialchars($message['text']); ?>
                        </p>
                    </li>

                <?php endforeach ?>

            </ul>

        </div>
        <div class="comments">
            <form class="comments__form form <?= is_null($user_id_active) ? 'visually-hidden' : '' ?>" action="messages.php" method="post">
              <input type="hidden" name="user_id" value="<?=$user_id_active;?>">
              <div class="comments__my-avatar">
                <img class="comments__picture <?=empty($user_avatar_login) ? 'visually-hidden' : '' ?>" src="<?= $user_avatar_login; ?>" alt="Аватар пользователя">
              </div>
              <div class="form__input-section <?= isset($errors['message']) ? "form__input-section--error" : ""; ?>">
                <textarea class="comments__textarea form__textarea form__input" name="message"
                          placeholder="Ваше сообщение"><?= getPostVal($_POST, 'message'); ?></textarea>
                <label class="visually-hidden">Ваше сообщение</label>
                <button class="form__error-button button" type="button">!</button>
                <div class="form__error-text">
                  <h3 class="form__error-title"><?= $errors['message']['header'] ?></h3>
                  <p class="form__error-desc"><?= $errors['message']['description'] ?></p>
                </div>
              </div>
              <button class="comments__submit button button--green" type="submit">Отправить</button>
            </form>

          </div>
        </div>
      </section>
    </main>
