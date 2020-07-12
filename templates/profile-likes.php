
<section class="profile__likes tabs__content tabs__content--active">
    <h2 class="visually-hidden">Лайки</h2>
    <ul class="profile__likes-list">

        <?php foreach ($posts as $key => $post) : ?>
            <?php $classOfPost = mb_substr($post['class'], mb_strpos($post['class'], '-') + 1) ?>
            <li class="post-mini post-mini--<?= $classOfPost; ?> post user">
                <div class="post-mini__user-info user__info">
                    <div class="post-mini__avatar user__avatar">
                        <a class="user__avatar-link" href="profile.php?user_id=<?= $post['user_id_subscriber']; ?>">
                            <img class="post-mini__picture user__picture <?=empty($post['avatar']) ? 'visually-hidden' : '' ?>" src="<?= $post['avatar']; ?>" alt="Аватар пользователя">
                        </a>
                    </div>
                    <div class="post-mini__name-wrapper user__name-wrapper">
                        <a class="post-mini__name user__name" href="profile.php?user_id=<?= $post['user_id_subscriber']; ?>">
                            <span><?= $post['login']; ?></span>
                        </a>
                        <div class="post-mini__action">
                            <span class="post-mini__activity user__additional">Лайкнул вашу публикацию</span>
                            <?php
                                $dateView = dateDifferent($post['creation_time']);
                            ?>
                            <time class="post-mini__time user__additional" datetime="<?= $post['creation_time'] ?>"><?= $dateView ?></time>
                        </div>
                    </div>
                </div>
                <div class="post-mini__preview">
                    <a class="post-mini__link" href="post.php?post_id=<?= $post['id']; ?>" title="Перейти на публикацию">
                        <?php if ($post['class'] === "post-photo") : ?>
                            <div class="post-mini__image-wrapper">
                                <img class="post-mini__image" src="<?= $post['picture'] ?>" width="109" height="109" alt="Превью публикации">
                            </div>
                            <span class="visually-hidden">Фото</span>
                        <?php elseif ($post['class'] === "post-text") : ?>
                            <span class="visually-hidden">Текст</span>
                            <svg class="post-mini__preview-icon" width="20" height="21">
                                <use xlink:href="#icon-filter-text"></use>
                            </svg>
                        <?php elseif ($post['class'] === "post-quote") : ?>
                            <span class="visually-hidden">Цитата</span>
                            <svg class="post-mini__preview-icon" width="21" height="20">
                                <use xlink:href="#icon-filter-quote"></use>
                                </svg>
                        <?php elseif ($post['class'] === "post-video") : ?>
                            <div class="post-mini__image-wrapper">
                                <img class="post-mini__image" src="img/coast-small.png" width="109" height="109" alt="Превью публикации">
                                <span class="post-mini__play-big">
                                    <svg class="post-mini__play-big-icon" width="12" height="13">
                                        <use xlink:href="#icon-video-play-big"></use>
                                    </svg>
                                </span>
                            </div>
                            <span class="visually-hidden">Видео</span>
                        <?php elseif ($post['class'] === "post-link") : ?>
                            <span class="visually-hidden">Ссылка</span>
                            <svg class="post-mini__preview-icon" width="21" height="18">
                                <use xlink:href="#icon-filter-link"></use>
                            </svg>
                        <?php endif ?>
                    </a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
