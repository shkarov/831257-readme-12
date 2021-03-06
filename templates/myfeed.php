    <main class="page__main page__main--feed">
      <div class="container">
        <h1 class="page__title page__title--feed">Моя лента</h1>
      </div>
      <div class="page__main-wrapper container">
        <section class="feed">
          <h2 class="visually-hidden">Лента</h2>
          <div class="feed__main-wrapper">
            <div class="feed__wrapper">

            <?php foreach ($posts as $key => $post) : ?>

              <article class="feed__post post <?= $post['class']; ?>">
                <header class="post__header post__author">
                  <a class="post__author-link" href="profile.php?user_id=<?= $post['user_id'] ?>" title="Автор">
                    <div class="post__avatar-wrapper">
                      <img class="post__author-avatar <?=empty($post['avatar']) ? 'visually-hidden' : '' ?>" src="<?= $post['avatar']; ?>" alt="Аватар пользователя" width="60" height="60">
                    </div>
                    <div class="post__info">
                      <b class="post__author-name"><?= htmlspecialchars($post['login']); ?></b>
                      <?php
                        $dateView = dateDifferent($post['creation_time']);
                      ?>
                      <span class="post__time"><?= $dateView ?></span>
                    </div>
                  </a>
                </header>

                <div class="post__main">
                    <h2><a href="post.php?post_id=<?= $post['id'] ?>"><?= htmlspecialchars($post['heading']); ?></a></h2>

                    <?php if ($post['class'] === "post-quote") : ?>
                        <blockquote>
                            <p>
                                <?= htmlspecialchars($post['text']); ?>
                            </p>
                        </blockquote>
                    <?php elseif ($post['class'] === "post-text") : ?>
                        <p>
                            <?= htmlspecialchars(textTrim($post['text'])); ?>
                        </p>
                        <?php if (substr(textTrim($post['text']), -3) === "...") : ?>
                            <a class="post-text__more-link" href="post.php?post_id=<?=$post['id']?>">Читать далее</a>
                        <?php endif; ?>
                    <?php elseif ($post['class'] === "post-photo") : ?>
                        <div class="post-photo__image-wrapper">
                            <img src="<?= htmlspecialchars($post['picture']); ?>" alt="Фото от пользователя" width="760" height="396">
                        </div>
                    <?php elseif ($post['class'] === "post-video") : ?>
                        <div class="post-video__block">
                            <div class="post-video__preview">
                                <?= embed_youtube_video($post['video']); ?>
                            </div>
                        </div>
                    <?php elseif ($post['class'] === "post-link") : ?>
                        <div class="post-link__wrapper">
                            <a class="post-link__external" href="<?= htmlspecialchars($post['link']); ?>" title="Перейти по ссылке">
                                <div class="post-link__icon-wrapper">
                                    <img src="./img/icon-htmlacademy.svg" alt="Иконка">
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

                <footer class="post__footer post__indicators">
                  <div class="post__buttons">
                    <a class="post__indicator post__indicator--likes button" href="feed.php?post_id=<?= $post['id'];?>&like_onClick" title="Лайк">
                      <svg class="post__indicator-icon" width="20" height="17">
                        <use xlink:href="#icon-heart"></use>
                      </svg>
                      <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                        <use xlink:href="#icon-heart-active"></use>
                      </svg>
                      <span><?= $post['likes'] ?></span>
                      <span class="visually-hidden">количество лайков</span>
                    </a>
                    <a class="post__indicator post__indicator--comments button" href="post.php?post_id=<?= $post['id'] ?>" title="Комментарии">
                      <svg class="post__indicator-icon" width="19" height="17">
                        <use xlink:href="#icon-comment"></use>
                      </svg>
                      <span><?= $post['comments'] ?></span>
                      <span class="visually-hidden">количество комментариев</span>
                    </a>
                    <a class="post__indicator post__indicator--repost button" href="feed.php?post_id=<?= $post['id'];?>&repost_onClick" title="Репост">
                      <svg class="post__indicator-icon" width="19" height="17">
                        <use xlink:href="#icon-repost"></use>
                      </svg>
                      <span><?= $post['reposts'] ?></span>
                      <span class="visually-hidden">количество репостов</span>
                    </a>
                  </div>
                </footer>

                <!-- класс выбран мной -->
                <div class="post__footer">
                    <?php $hashtags = explode(' ', $post['hashtags']); ?>
                    <ul class="post__tags">
                        <?php foreach ($hashtags as $hashtag) : ?>
                            <li>
                                <a href="search.php?search_string=<?=urlencode('#'.$hashtag)?>">
                                    <?=empty($hashtag) ? '' : '#'.htmlspecialchars($hashtag) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

              </article>

            <?php endforeach ?>

            </div>
          </div>
          <ul class="feed__filters filters">
            <li class="feed__filters-item filters__item">
              <a class="filters__button <?= is_null($type_id) ? "filters__button--active" : ""; ?>" href="feed.php">
                <span>Все</span>
              </a>
            </li>
            <?php foreach ($data['types'] as $type) :?>
                <li class="feed__filters-item filters__item">
                    <?php if ($type['class'] === 'post-photo') : ?>
                        <a class="filters__button filters__button--photo button <?= ($type_id === 1) ? "filters__button--active" : ""; ?>" href="feed.php?type_id=1">
                            <span class="visually-hidden">Фото</span>
                            <svg class="filters__icon" width="22" height="18">
                                <use xlink:href="#icon-filter-photo"></use>
                            </svg>
                        </a>
                    <?php elseif ($type['class'] === 'post-video') : ?>
                        <a class="filters__button filters__button--video button <?= ($type_id === 2) ? "filters__button--active" : ""; ?>" href="feed.php?type_id=2">
                            <span class="visually-hidden">Видео</span>
                            <svg class="filters__icon" width="24" height="16">
                                <use xlink:href="#icon-filter-video"></use>
                            </svg>
                        </a>
                    <?php elseif ($type['class'] === 'post-text') : ?>
                        <a class="filters__button filters__button--text button <?= ($type_id === 3) ? "filters__button--active" : ""; ?>" href="feed.php?type_id=3">
                            <span class="visually-hidden">Текст</span>
                            <svg class="filters__icon" width="20" height="21">
                                <use xlink:href="#icon-filter-text"></use>
                            </svg>
                        </a>
                    <?php elseif ($type['class'] === 'post-quote') : ?>
                        <a class="filters__button filters__button--quote button <?= ($type_id === 4) ? "filters__button--active" : ""; ?>" href="feed.php?type_id=4">
                            <span class="visually-hidden">Цитата</span>
                            <svg class="filters__icon" width="21" height="20">
                                <use xlink:href="#icon-filter-quote"></use>
                            </svg>
                        </a>
                    <?php elseif ($type['class'] === 'post-link') : ?>
                        <a class="filters__button filters__button--link button <?= ($type_id === 5) ? "filters__button--active" : ""; ?>" href="feed.php?type_id=5">
                            <span class="visually-hidden">Ссылка</span>
                            <svg class="filters__icon" width="21" height="18">
                                <use xlink:href="#icon-filter-link"></use>
                            </svg>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
          </ul>
        </section>
        <aside class="promo">
          <article class="promo__block promo__block--barbershop">
            <h2 class="visually-hidden">Рекламный блок</h2>
            <p class="promo__text">
              Все еще сидишь на окладе в офисе? Открой свой барбершоп по нашей франшизе!
            </p>
            <a class="promo__link" href="#">
              Подробнее
            </a>
          </article>
          <article class="promo__block promo__block--technomart">
            <h2 class="visually-hidden">Рекламный блок</h2>
            <p class="promo__text">
              Товары будущего уже сегодня в онлайн-сторе Техномарт!
            </p>
            <a class="promo__link" href="#">
              Перейти в магазин
            </a>
          </article>
          <article class="promo__block">
            <h2 class="visually-hidden">Рекламный блок</h2>
            <p class="promo__text">
              Здесь<br> могла быть<br> ваша реклама
            </p>
            <a class="promo__link" href="#">
              Разместить
            </a>
          </article>
        </aside>
      </div>
    </main>
