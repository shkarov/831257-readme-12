<section class="page__main page__main--popular">
    <div class="container">
        <h1 class="page__title page__title--popular">Популярное</h1>
    </div>
    <div class="popular container">
        <div class="popular__filters-wrapper">
            <div class="popular__sorting sorting">
                <b class="popular__sorting-caption sorting__caption">Сортировка:</b>
                <ul class="popular__sorting-list sorting__list">
                    <li class="sorting__item sorting__item--popular">
                        <a class="sorting__link <?= is_null($data['sort']) ? "sorting__link--active" : ""; ?>" href="index.php">
                            <span>Популярность</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="sorting__item">
                        <a class="sorting__link <?= ($data['sort'] === 'likes') ? "sorting__link--active" : ""; ?>" href="index.php?sort=likes">
                            <span>Лайки</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="sorting__item">
                        <a class="sorting__link <?= ($data['sort'] === 'creation_time') ? "sorting__link--active" : ""; ?>" href='index.php?sort=creation_time'>
                            <span>Дата</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="popular__filters filters">
                <b class="popular__filters-caption filters__caption">Тип контента:</b>
                <ul class="popular__filters-list filters__list">
                    <li class="popular__filters-item popular__filters-item--all filters__item filters__item--all">
                        <a class="filters__button filters__button--ellipse filters__button--all <?= is_null($data['type_id']) ? "filters__button--active" : ""; ?>" href="index.php">
                            <span>Все</span>
                        </a>
                    </li>

                    <?php foreach ($data['types'] as $type) :?>
                        <li class="popular__filters-item filters__item">
                            <?php if ($type['class'] === 'post-photo') : ?>
                                <a class="filters__button filters__button--photo button <?= ($data['type_id'] === 3) ? "filters__button--active" : ""; ?>" href="index.php?type_id=3">
                                    <span class="visually-hidden">Фото</span>
                                    <svg class="filters__icon" width="22" height="18">
                                        <use xlink:href="#icon-filter-photo"></use>
                                    </svg>
                                </a>
                            <?php elseif ($type['class'] === 'post-video') : ?>
                                <a class="filters__button filters__button--video button <?= ($data['type_id'] === 4) ? "filters__button--active" : ""; ?>" href="index.php?type_id=4">
                                    <span class="visually-hidden">Видео</span>
                                    <svg class="filters__icon" width="24" height="16">
                                        <use xlink:href="#icon-filter-video"></use>
                                    </svg>
                                </a>
                            <?php elseif ($type['class'] === 'post-text') : ?>
                                <a class="filters__button filters__button--text button <?= ($data['type_id'] === 1) ? "filters__button--active" : ""; ?>" href="index.php?type_id=1">
                                    <span class="visually-hidden">Текст</span>
                                    <svg class="filters__icon" width="20" height="21">
                                        <use xlink:href="#icon-filter-text"></use>
                                    </svg>
                                </a>
                            <?php elseif ($type['class'] === 'post-quote') : ?>
                                <a class="filters__button filters__button--quote button <?= ($data['type_id'] === 2) ? "filters__button--active" : ""; ?>" href="index.php?type_id=2">
                                    <span class="visually-hidden">Цитата</span>
                                    <svg class="filters__icon" width="21" height="20">
                                        <use xlink:href="#icon-filter-quote"></use>
                                    </svg>
                                </a>
                            <?php elseif ($type['class'] === 'post-link') : ?>
                                <a class="filters__button filters__button--link button <?= ($data['type_id'] === 5) ? "filters__button--active" : ""; ?>" href="index.php?type_id=5">
                                    <span class="visually-hidden">Ссылка</span>
                                    <svg class="filters__icon" width="21" height="18">
                                        <use xlink:href="#icon-filter-link"></use>
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>

                </ul>
            </div>
        </div>
        <div class="popular__posts">

            <?php foreach ($data['posts'] as $key => $post) : ?>
               <article class="popular__post post <?= $post['class']; ?>">
                <header class="post__header">
                    <a href="post.php?post_id=<?= $post['id'] ?>"><h2><?= htmlspecialchars($post['heading']); ?></h2></a>
                </header>
                <div class="post__main">
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
                            <a class="post-text__more-link" href="#">Читать далее</a>
                        <?php endif; ?>
                    <?php elseif ($post['class'] === "post-photo") : ?>
                        <div class="post-photo__image-wrapper">
                            <img src="<?= htmlspecialchars($post['picture']); ?>" alt="Фото от пользователя" width="360" height="240">
                        </div>
                    <?php elseif ($post['class'] === "post-link") : ?>
                        <div class="post-link__wrapper">
                            <a class="post-link__external" href="http://<?= htmlspecialchars($post['link']); ?>" title="Перейти по ссылке">
                                <div class="post-link__info-wrapper">
                                    <div class="post-link__icon-wrapper">
                                        <img src="https://www.google.com/s2/favicons?domain=htmlacademy.ru" alt="Иконка">
                                    </div>
                                    <div class="post-link__info">
                                        <h3><?= $post['heading']; ?></h3>
                                    </div>
                                </div>
                                <span><?= $post['text']; ?></span>
                            </a>
                        </div>
                    <?php endif ?>
                </div>

                <footer class="post__footer">
                    <div class="post__author">
                        <a class="post__author-link" href="#" title="Автор">
                            <div class="post__avatar-wrapper">
                                <img class="post__author-avatar" src="<?= $post['avatar']; ?>" alt="Аватар пользователя">
                            </div>
                            <div class="post__info">
                                <b class="post__author-name"><?= htmlspecialchars($post['login']); ?></b>
                                <?php
                                    $dateTitle = strftime('%d.%m.%Y %H:%M', strtotime($post['creation_time']));
                                    $dateView = dateDifferent($post['creation_time']);
                                ?>
                                <time class="post__time" datetime="<?= $post['creation_time'] ?>" title="<?= $dateTitle ?>"><?= $dateView ?></time>
                            </div>
                        </a>
                    </div>
                    <div class="post__indicators">
                        <div class="post__buttons">
                            <a class="post__indicator post__indicator--likes button" href="#" title="Лайк">
                                <svg class="post__indicator-icon" width="20" height="17">
                                    <use xlink:href="#icon-heart"></use>
                                </svg>
                                <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                                    <use xlink:href="#icon-heart-active"></use>
                                </svg>
                                <span><?= $post['likes'] ?></span>
                                <span class="visually-hidden">количество лайков</span>
                            </a>
                            <a class="post__indicator post__indicator--comments button" href="#" title="Комментарии">
                                <svg class="post__indicator-icon" width="19" height="17">
                                    <use xlink:href="#icon-comment"></use>
                                </svg>
                                <span><?= $post['comments'] ?></span>
                                <span class="visually-hidden">количество комментариев</span>
                            </a>
                        </div>
                    </div>
                </footer>
               </article>

            <?php endforeach; ?>
        </div>
    </div>
</section>
