<main class="page__main page__main--profile">
    <h1 class="visually-hidden">Профиль</h1>
    <div class="profile profile--posts">
        <div class="profile__user-wrapper">
          <div class="profile__user user container">
            <div class="profile__user-info user__info">
              <div class="profile__avatar user__avatar">
                <img class="profile__picture user__picture <?=empty($avatar) ? 'visually-hidden' : '' ?>" src="<?= $avatar ?>" alt="Аватар пользователя">
              </div>
              <div class="profile__name-wrapper user__name-wrapper">
                <span class="profile__name user__name"><?= $user ?></span>
                <?php
                    $date_view = dateDifferent($user_creation_time, 'на сайте');
                ?>
                <time class="profile__user-time user__time" datetime="<?= $user_creation_time ?>"><?= $date_view ?></time>
              </div>
            </div>
            <div class="profile__rating user__rating">
              <p class="profile__rating-item user__rating-item user__rating-item--publications">
                <span class="user__rating-amount"><?= $posts_count ?></span>
                <span class="profile__rating-text user__rating-text">публикаций</span>
              </p>
              <p class="profile__rating-item user__rating-item user__rating-item--subscribers">
                <span class="user__rating-amount"><?= $subscribers ?></span>
                <span class="profile__rating-text user__rating-text">подписчиков</span>
              </p>
            </div>
            <div class="profile__user-buttons user__buttons <?=$user_id === $user_id_login ? 'visually-hidden' : '' ?>">
                <button class="profile__user-button user__button user__button--subscription button button--main" type="button">
                    <a href="profile.php?user_id=<?=$user_id?>&subscribeButton_onClick">
                        <?= ($subscribe) ? 'Отписаться' : 'Подписаться'?>
                    </a>
                </button>

                <a class="profile__user-button user__button user__button--writing button button--green <?= !$subscribe ? 'visually-hidden' : '' ?>" href="messages.php?user_id=<?=$user_id?>">Сообщение</a>
            </div>
          </div>
        </div>
        <div class="profile__tabs-wrapper tabs">
          <div class="container">
            <div class="profile__tabs filters">
              <b class="profile__tabs-caption filters__caption">Показать:</b>
              <ul class="profile__tabs-list filters__list tabs__list">
                <li class="profile__tabs-item filters__item tabs__item">
                  <a class="profile__tabs-link filters__button <?= ($tab === 'posts') ? 'filters__button--active button' : '' ?>" href="profile.php?user_id=<?=$user_id?>">Посты</a>
                </li>
                <li class="profile__tabs-item filters__item tabs__item">
                  <a class="profile__tabs-link filters__button button <?= ($tab === 'likes') ? 'filters__button--active button' : '' ?>" href="profile.php?user_id=<?=$user_id?>&tab=likes">Лайки</a>
                </li>
                <li class="profile__tabs-item filters__item tabs__item">
                  <a class="profile__tabs-link filters__button button <?= ($tab === 'subscribes') ? 'filters__button--active button' : '' ?>" href="profile.php?user_id=<?=$user_id?>&tab=subscribes">Подписки</a>
                </li>
              </ul>
            </div>
            <div class="profile__tab-content">

                <?= $content ?>

            </div>
          </div>
        </div>
    </div>
</main>
