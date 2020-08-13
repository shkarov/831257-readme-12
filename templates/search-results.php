<main class="page__main page__main--search-results">
    <h1 class="visually-hidden">Страница результатов поиска</h1>
    <section class="search">
        <h2 class="visually-hidden">Результаты поиска</h2>
        <div class="search__query-wrapper">
          <div class="search__query container">
            <span>Вы искали:</span>
            <span class="search__query-text"><?= htmlspecialchars($search_string); ?></span>
          </div>
        </div>
        <div class="search__results-wrapper">
          <div class="container">
            <div class="search__content">

            <?php foreach ($posts as $key => $post) : ?>

              <article class="search__post post <?= $post['class']; ?>">
                <header class="post__header post__author">
                  <a class="post__author-link" href="profile.php?user_id=<?=$post['user_id']?>" title="Автор">
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

                    <?php if ($post['class'] === "post-photo") : ?>
                        <h2><a href="post.php?post_id=<?= $post['id'] ?>"><?= htmlspecialchars($post['heading']); ?></a></h2>
                        <div class="post-photo__image-wrapper">
                            <img src="<?= $post['picture']; ?>" alt="Фото от пользователя" width="760" height="396">
                        </div>
                    <?php elseif ($post['class'] === "post-text") : ?>
                        <h2><a href="post.php?post_id=<?= $post['id'] ?>"><?= htmlspecialchars($post['heading']); ?></a></h2>
                        <p>
                            <?= htmlspecialchars(textTrim($post['text'])); ?>
                        </p>
                        <?php if (substr(textTrim($post['text']), -3) === "...") : ?>
                            <a class="post-text__more-link" href="post.php?post_id=<?= $post['id'] ?>">Читать далее</a>
                        <?php endif; ?>
                    <?php elseif ($post['class'] === "post-quote") : ?>
                        <h2><a href="post.php?post_id=<?= $post['id'] ?>"><?= htmlspecialchars($post['heading']); ?></a></h2>
                        <blockquote>
                            <p>
                                <?= htmlspecialchars($post['text']); ?>
                            </p>
                            <cite><?= htmlspecialchars(textTrim($post['author_quote'])); ?></cite>
                        </blockquote>
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
                                    <span><?= htmlspecialchars($post['link']); ?></span>
                                </div>
                                <svg class="post-link__arrow" width="11" height="16">
                                    <use xlink:href="#icon-arrow-right-ad"></use>
                                </svg>
                            </a>
                        </div>
                    <?php endif ?>
                </div>
                <footer class="post__footer post__indicators">
                  <div class="post__buttons">
                    <a class="post__indicator post__indicator--likes button" href="search.php?search_string=<?= urlencode($search_string); ?>&like_onClick&post_id=<?=$post['id']?>" title="Лайк">
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
                  </div>
                </footer>
              </article>

            <?php endforeach; ?>

            </div>
          </div>
        </div>
    </section>
</main>
