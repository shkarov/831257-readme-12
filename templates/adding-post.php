<main class="page__main page__main--adding-post">
    <div class="page__main-section">
        <div class="container">
            <h1 class="page__title page__title--adding-post">Добавить публикацию</h1>
        </div>
        <div class="adding-post container">
            <div class="adding-post__tabs-wrapper tabs">
                <div class="adding-post__tabs filters">
                    <ul class="adding-post__tabs-list filters__list tabs__list">

                        <?php foreach ($types as $type) :?>
                            <li class="adding-post__tabs-item filters__item">
                                <?php if ($type['class'] === 'post-photo') : ?>
                                    <a class="adding-post__tabs-link filters__button filters__button--photo tabs__item button <?= (is_null($type_id) || $type_id === 1) ? "filters__button--active tabs__item--active" : ""; ?>" href="add.php?type_id=1">
                                        <svg class="filters__icon" width="22" height="18">
                                            <use xlink:href="#icon-filter-photo"></use>
                                        </svg>
                                        <span><?= $type['name'] ?></span>
                                    </a>
                                <?php elseif ($type['class'] === 'post-video') : ?>
                                    <a class="adding-post__tabs-link filters__button filters__button--video tabs__item button <?= $type_id === 2 ? "filters__button--active tabs__item--active" : ""; ?>" href="add.php?type_id=2">
                                        <svg class="filters__icon" width="24" height="16">
                                            <use xlink:href="#icon-filter-video"></use>
                                        </svg>
                                        <span><?= $type['name'] ?></span>
                                    </a>
                                <?php elseif ($type['class'] === 'post-text') : ?>
                                    <a class="adding-post__tabs-link filters__button filters__button--text tabs__item button <?= $type_id === 3 ? "filters__button--active tabs__item--active" : ""; ?>" href="add.php?type_id=3">
                                        <svg class="filters__icon" width="20" height="21">
                                            <use xlink:href="#icon-filter-text"></use>
                                        </svg>
                                        <span><?= $type['name'] ?></span>
                                    </a>
                                <?php elseif ($type['class'] === 'post-quote') : ?>
                                    <a class="adding-post__tabs-link filters__button filters__button--quote tabs__item button <?= $type_id === 4 ? "filters__button--active tabs__item--active" : ""; ?>" href="add.php?type_id=4">
                                        <svg class="filters__icon" width="21" height="20">
                                            <use xlink:href="#icon-filter-quote"></use>
                                        </svg>
                                        <span><?= $type['name'] ?></span>
                                    </a>
                                <?php elseif ($type['class'] === 'post-link') : ?>
                                    <a class="adding-post__tabs-link filters__button filters__button--link tabs__item button <?= $type_id === 5 ? "filters__button--active tabs__item--active" : ""; ?>" href="add.php?type_id=5">
                                        <svg class="filters__icon" width="21" height="18">
                                            <use xlink:href="#icon-filter-link"></use>
                                        </svg>
                                        <span><?= $type['name'] ?></span>
                                    </a>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>

                    </ul>
                </div>

                <div class="adding-post__tab-content">
                    <?php if (is_null($type_id) || $type_id === 1) : ?>
                        <?php require 'templates/adding-photo.php'; ?>
                    <?php elseif ($type_id === 2) : ?>
                        <?php require 'templates/adding-video.php'; ?>
                    <?php elseif ($type_id === 3) : ?>
                        <?php require 'templates/adding-text.php'; ?>
                    <?php elseif ($type_id === 4) : ?>
                        <?php require 'templates/adding-quote.php'; ?>
                    <?php elseif ($type_id === 5) : ?>
                        <?php require 'templates/adding-link.php'; ?>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
 </main>
