<main class="page__main page__main--publication">
    <div class="container">
      <h1 class="page__title page__title--publication"><?= $post['heading'];?> </h1>
      <section class="post-details">
        <h2 class="visually-hidden">Публикация</h2>

        <div class="post-details__wrapper post-photo">

          <div class="post-details__main-block post post--details">

            <?php require_once "templates/".$post['class'].".php"; ?>

            <div class="post__indicators">
              <div class="post__buttons">
                <a class="post__indicator post__indicator--likes button" href="post.php?post_id=<?= $post['id'];?> & like_onClick" title="Лайк">
                  <svg class="post__indicator-icon" width="20" height="17">
                    <use xlink:href="#icon-heart"></use>
                  </svg>
                  <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                    <use xlink:href="#icon-heart-active"></use>
                  </svg>
                  <span><?=$post['likes'];?></span>
                  <span class="visually-hidden">количество лайков</span>
                </a>
                <a class="post__indicator post__indicator--comments button" href="#" title="Комментарии">
                  <svg class="post__indicator-icon" width="19" height="17">
                    <use xlink:href="#icon-comment"></use>
                  </svg>
                  <span><?=$post['comments'];?></span>
                  <span class="visually-hidden">количество комментариев</span>
                </a>
                <a class="post__indicator post__indicator--repost button" href="#" title="Репост">
                  <svg class="post__indicator-icon" width="19" height="17">
                    <use xlink:href="#icon-repost"></use>
                  </svg>
                  <span><?=$post['reposts'];?></span>
                  <span class="visually-hidden">количество репостов</span>
                </a>
              </div>
              <?php $views = (string) $post['views'];
                    $views .= " ". get_noun_plural_form($views, 'просмотр', 'просмотра', 'просмотров');
              ?>
              <span class="post__view"><?= $views;?></span>
            </div>

            <div class="comments">
              <form class="comments__form form" action="post.php" method="post">
                <input type="hidden" name="post_id" value="<?=$post['id'];?>">
                <div class="comments__my-avatar">
                  <img class="comments__picture <?=empty($user_login_avatar) ? 'visually-hidden' : '' ?>" src="<?=$user_login_avatar;?>" alt="Аватар пользователя">
                </div>
                <div class="form__input-section <?= isset($errors['comment']) ? "form__input-section--error" : ""; ?>">
                  <textarea class="comments__textarea form__textarea form__input" name="comment" placeholder="Ваш комментарий"><?= getPostVal($_POST, 'comment'); ?></textarea>
                  <label class="visually-hidden">Ваш комментарий</label>
                  <button class="form__error-button button" type="button">!</button>
                  <div class="form__error-text">
                    <h3 class="form__error-title"><?= $errors['comment']['header'] ?></h3>
                    <p class="form__error-desc"><?= $errors['comment']['description'] ?></p>
                  </div>
                </div>
                <button class="comments__submit button button--green" type="submit">Отправить</button>
              </form>
              <div class="comments__list-wrapper">
                <ul class="comments__list">

                 <?php foreach ($comments as $comment) : ?>
                  <li class="comments__item user">
                    <div class="comments__avatar">
                      <a class="user__avatar-link" href="#">
                        <img class="comments__picture <?=empty($comment['avatar']) ? 'visually-hidden' : '' ?>" src="<?= $comment['avatar'] ?>" alt="Аватар пользователя">
                      </a>
                    </div>
                    <div class="comments__info">
                      <div class="comments__name-wrapper">
                        <a class="comments__user-name" href="#">
                          <span><?= htmlspecialchars($comment['login']) ?></span>
                        </a>
                        <?php
                            $dateView = dateDifferent($comment['creation_time']);
                        ?>
                        <time class="comments__time" datetime="<?= $comment['creation_time'] ?>"><?= $dateView ?></time>
                      </div>
                      <p class="comments__text">
                        <?= htmlspecialchars($comment['text']) ?>
                      </p>
                    </div>
                  </li>
                 <?php endforeach; ?>

                </ul>
<!--
                <a class="comments__more-link" href="#">
                  <span>Показать все комментарии</span>
                  <sup class="comments__amount">45</sup>
                </a>
-->
              </div>
            </div>
          </div>

          <div class="post-details__user user">
            <div class="post-details__user-info user__info">
              <div class="post-details__avatar user__avatar">
                <a class="post-details__avatar-link user__avatar-link" href="profile.php?user_id=<?= $post['user_id'] ?>">
                  <img class="post-details__picture user__picture <?=empty($post['avatar']) ? 'visually-hidden' : '' ?>" src="<?= $post['avatar'] ?>" alt="Аватар пользователя">
                </a>
              </div>
              <div class="post-details__name-wrapper user__name-wrapper">
                <a class="post-details__name user__name" href="profile.php?user_id=<?= $post['user_id'] ?>">
                  <span><?= $post['login'] ?></span>
                </a>
                <?php
                    $dateView = dateDifferent($post['user_creation_time'], 'на сайте');
                ?>
                <time class="post-details__time user__time" datetime="<?= $post['user_creation_time'] ?>"><?= $dateView ?></time>
              </div>
            </div>
            <div class="post-details__rating user__rating">
              <p class="post-details__rating-item user__rating-item user__rating-item--subscribers">
                <span class="post-details__rating-amount user__rating-amount"><?= $post['subscribers'] ?></span>
                <span class="post-details__rating-text user__rating-text">подписчиков</span>
              </p>
              <p class="post-details__rating-item user__rating-item user__rating-item--publications">
                <span class="post-details__rating-amount user__rating-amount"><?= $post['posts'] ?></span>
                <span class="post-details__rating-text user__rating-text">публикаций</span>
              </p>
            </div>
            <div class="post-details__user-buttons user__buttons">
              <button class="user__button user__button--subscription button button--main" type="button">Подписаться</button>
              <a class="user__button user__button--writing button button--green" href="add.php">Сообщение</a>
            </div>
          </div>

        </div>
      </section>
    </div>
  </main>
