<main class="page__main page__main--profile">
    <h1 class="visually-hidden">Профиль</h1>
    <div class="profile profile--posts">
        <div class="profile__user-wrapper">
          <div class="profile__user user container">
            <div class="profile__user-info user__info">
              <div class="profile__avatar user__avatar">
                <img class="profile__picture user__picture" src="<?= $avatar ?>" alt="Аватар пользователя">
              </div>
              <div class="profile__name-wrapper user__name-wrapper">
                <span class="profile__name user__name"><?= $user ?></span>
                <?php
                    $dateView = dateDifferent($user_creation_time, 'на сайте');
                ?>
                <time class="profile__user-time user__time" datetime="<?= $user_creation_time ?>"><?= $dateView ?></time>
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

<!--
            <form action="profile.php" method="post">
                <div class="profile__user-buttons user__buttons">
                    <input type="hidden" name="user_id" value="<?= $user_id ?>">
                    <button class="profile__user-button user__button user__button--subscription button button--main" type="submit">Подписаться</button>

                    <a class="profile__user-button user__button user__button--writing button button--green" href="#">Сообщение</a>
                </div>
            </form>

-->
            <div class="profile__user-buttons user__buttons">
                <button class="profile__user-button user__button user__button--subscription button button--main" type="button">
                    <a href="profile.php?user_id=<?=$user_id?>&subscribeButton_onClick">
                        <?= ($subscribe) ? 'Отписаться' : 'Подписаться'?>
                    </a>
                </button>

                <a class="profile__user-button user__button user__button--writing button button--green" href="#">Сообщение</a>
            </div>
          </div>
        </div>
        <div class="profile__tabs-wrapper tabs">
          <div class="container">
            <div class="profile__tabs filters">
              <b class="profile__tabs-caption filters__caption">Показать:</b>
              <ul class="profile__tabs-list filters__list tabs__list">
                <li class="profile__tabs-item filters__item tabs__item">
                  <a class="profile__tabs-link filters__button filters__button--active button">Посты</a>
                </li>
                <li class="profile__tabs-item filters__item tabs__item">
                  <a class="profile__tabs-link filters__button button" href="#">Лайки</a>
                </li>
                <li class="profile__tabs-item filters__item tabs__item">
                  <a class="profile__tabs-link filters__button button" href="#">Подписки</a>
                </li>
              </ul>
            </div>
            <div class="profile__tab-content">
              <section class="profile__posts tabs__content tabs__content--active">
                <h2 class="visually-hidden">Публикации</h2>

                <?php foreach ($posts as $key => $post) : ?>
                    <article class="profile__post post <?= $post['class']; ?>">
                        <header class="post__header">
                            <a href="post.php?post_id=<?= $post['id'] ?>">
                                <h2><?= htmlspecialchars($post['heading']); ?></h2>
                            </a>
                        </header>
                        <div class="post__main">

                          <?php if ($post['class'] === "post-photo") : ?>
                            <div class="post-photo__image-wrapper">
                                <img src="<?= $post['picture']; ?>" alt="Фото от пользователя" width="760" height="396">
                            </div>
                          <?php elseif ($post['class'] === "post-text") : ?>
                            <p>
                                <?= htmlspecialchars(textTrim($post['text'])); ?>
                            </p>
                            <?php if (substr(textTrim($post['text']), -3) === "...") : ?>
                                <a class="post-text__more-link" href="post.php?post_id=<?=$post['id']?>">Читать далее</a>
                            <?php endif; ?>
                          <?php elseif ($post['class'] === "post-quote") : ?>
                            <blockquote>
                                <p>
                                    <?= htmlspecialchars($post['text']); ?>
                                </p>
                                <cite><?= htmlspecialchars(textTrim($post['author_quote'])); ?></cite>
                            </blockquote>
                          <?php elseif ($post['class'] === "post-video") : ?>
                            <div class="post-video__block">
                                <div class="post-video__preview">
                                    <img src="<?= $post['picture']; ?>" alt="Превью к видео" width="760" height="396">
                                </div>
                                <div class="post-video__control">
                                    <button class="post-video__play post-video__play--paused button button--video" type="button"><span class="visually-hidden">Запустить видео</span></button>
                                    <div class="post-video__scale-wrapper">
                                        <div class="post-video__scale">
                                            <div class="post-video__bar">
                                                <div class="post-video__toggle"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="post-video__fullscreen post-video__fullscreen--inactive button button--video" type="button"><span class="visually-hidden">Полноэкранный режим</span></button>
                                </div>
                                <button class="post-video__play-big button" type="button">
                                    <svg class="post-video__play-big-icon" width="27" height="28">
                                        <use xlink:href="#icon-video-play-big"></use>
                                    </svg>
                                    <span class="visually-hidden">Запустить проигрыватель</span>
                                </button>
                            </div>
                          <?php elseif ($post['class'] === "post-link") : ?>
                            <div class="post-link__wrapper">
                                <a class="post-link__external" href="http://<?= htmlspecialchars($post['link']); ?>" title="Перейти по ссылке">
                                    <div class="post-link__icon-wrapper">
                                        <img src="img/logo-vita.jpg" alt="Иконка">
                                    </div>
                                    <div class="post-link__info">
                                        <h3><?= htmlspecialchars($post['heading']); ?></h3>
                                        <p><?= $post['text']; ?></p>
                                        <span><?= htmlspecialchars($post['link']); ?></span>
                                    </div>
                                    <svg class="post-link__arrow" width="11" height="16">
                                        <use xlink:href="#icon-arrow-right-ad"></use>
                                    </svg>
                                </a>
                            </div>
                          <?php endif ?>

                        </div>

                  <footer class="post__footer">
                    <div class="post__indicators">
                      <div class="post__buttons">
                        <a class="post__indicator post__indicator--likes button" href="profile.php?post_id=<?= $post['id'];?> & user_id=<?= $user_id;?> & like_onClick" title="Лайк">
                          <svg class="post__indicator-icon" width="20" height="17">
                            <use xlink:href="#icon-heart"></use>
                          </svg>
                          <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                            <use xlink:href="#icon-heart-active"></use>
                          </svg>
                          <span><?= $post['likes'] ?></span>
                          <span class="visually-hidden">количество лайков</span>
                        </a>
                        <a class="post__indicator post__indicator--repost button" href="#" title="Репост">
                          <svg class="post__indicator-icon" width="19" height="17">
                            <use xlink:href="#icon-repost"></use>
                          </svg>
                          <span><?= $post['reposts'] ?></span>
                          <span class="visually-hidden">количество репостов</span>
                        </a>
                      </div>
                      <?php
                        $datePostView = dateDifferent($post['creation_time']);
                      ?>
                      <time class="post__time" datetime="<?= $post['creation_time'] ?>"><?= $datePostView ?></time>
                    </div>

                    <?php $hashtags = explode(' ', $post['hashtags']); ?>
                    <ul class="post__tags">
                        <?php foreach ($hashtags as $hashtag) : ?>
                            <li><a href="#">#<?= htmlspecialchars($hashtag) ?></a></li>
                        <?php endforeach; ?>
                    </ul>

                  </footer>
                  <div class="comments">
                    <a class="comments__button button" href="post.php?post_id=<?= $post['id'] ?>">Показать комментарии</a>
                  </div>
                </article>

                <?php endforeach; ?>
              </section>
            </div>
          </div>
        </div>
    </div>
</main>
