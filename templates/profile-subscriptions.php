<section class="profile__subscriptions tabs__content tabs__content--active">
    <h2 class="visually-hidden">Подриски</h2>
    <ul class="profile__subscriptions-list">

        <?php foreach ($posts as $key => $post) : ?>
            <li class="post-mini post-mini--photo post user">
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
                        <?php
                            $date_view = dateDifferent($post['creation_time_user']);
                        ?>
                        <time class="post-mini__time user__additional" datetime="<?= $post['creation_time_user'] ?>"><?= $date_view ?></time>
                    </div>
                </div>
                <div class="post-mini__rating user__rating">
                    <p class="post-mini__rating-item user__rating-item user__rating-item--publications">
                        <span class="post-mini__rating-amount user__rating-amount"><?= $post['posts'] ?></span>
                        <span class="post-mini__rating-text user__rating-text">публикаций</span>
                    </p>
                    <p class="post-mini__rating-item user__rating-item user__rating-item--subscribers">
                        <span class="post-mini__rating-amount user__rating-amount"><?= $post['subscribers'] ?></span>
                        <span class="post-mini__rating-text user__rating-text">подписчиков</span>
                    </p>
                </div>
                <div class="post-mini__user-buttons user__buttons">
                    <?php
                        $subscribe_mutual = ($post['mutual_subscribe']) ? true : false;
                    ?>
                    <button class="post-mini__user-button user__button user__button--subscription button <?= $subscribe_mutual ? 'button--quartz' : 'button--main'?>" type="button">
                        <a href="profile.php?user_id=<?=$user_id?>&user_id_subscriber=<?= $post['user_id_subscriber'] ?>&subscribeButtonMutual_onClick=<?= ($subscribe_mutual) ? 'del' : 'add' ?>">
                            <?= $subscribe_mutual ? 'Отписаться' : 'Подписаться'?>
                        </a>
                    </button>
                </div>
            </li>
        <?php endforeach; ?>

    </ul>
</section>
