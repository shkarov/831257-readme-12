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

                        <section class="adding-post__photo tabs__content tabs__content--active">
                            <h2 class="visually-hidden">Форма добавления фото</h2>
                            <form class="adding-post__form form" action="add.php" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="type_id" value="1">
                                <div class="form__text-inputs-wrapper">
                                    <div class="form__text-inputs ">

                                        <?php $heading = getHeadingAddPost($type_id); ?>
                                        <?php require 'templates/adding-heading.php'; ?>

                                        <div class="adding-post__input-wrapper form__input-wrapper <?= isset($errors['photo-url']) ? "form__input-section--error" : ""; ?>">
                                            <label class="adding-post__label form__label" for="photo-url">Ссылка из интернета</label>
                                            <div class="form__input-section">
                                                <input class="adding-post__input form__input" id="photo-url" type="text" name="photo-url" placeholder="Введите ссылку" value="<?= getPostVal($_POST, 'photo-url'); ?>">
                                                <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                                <div class="form__error-text">
                                                    <h3 class="form__error-title"><?= $errors['photo-url']['header'] ?></h3>
                                                    <p class="form__error-desc"><?= $errors['photo-url']['description'] ?></p>
                                                </div>
                                            </div>
                                        </div>

                                        <?php $tags = getTagsAddPost($type_id); ?>
                                        <?php require 'templates/adding-tags.php'; ?>

                                    </div>

                                    <?php require 'templates/adding-errors.php'; ?>

                                </div>
                                <div class="adding-post__input-file-container form__input-container form__input-container--file">
                                    <input id="userpic-file-photo" type="file" name="userpic-file-photo" title=" ">
                                </div>

                                <?php require 'templates/adding-publish.php'; ?>

                            </form>
                        </section>

                    <?php elseif ($type_id === 2) : ?>

                        <section class="adding-post__video tabs__content tabs__content--active">
                            <h2 class="visually-hidden">Форма добавления видео</h2>
                            <form class="adding-post__form form" action="add.php" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="type_id" value="2">
                                <div class="form__text-inputs-wrapper">
                                    <div class="form__text-inputs">

                                        <?php $heading = getHeadingAddPost($type_id); ?>
                                        <?php require 'templates/adding-heading.php'; ?>

                                        <div class="adding-post__input-wrapper form__input-wrapper <?= isset($errors['video-url']) ? "form__input-section--error" : ""; ?>">
                                            <label class="adding-post__label form__label" for="video-url">Ссылка youtube <span class="form__input-required">*</span></label>
                                            <div class="form__input-section">
                                                <input class="adding-post__input form__input" id="video-url" type="text" name="video-url" placeholder="Введите ссылку" value="<?= getPostVal($_POST, 'video-url'); ?>">
                                                <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                                <div class="form__error-text">
                                                    <h3 class="form__error-title"><?= $errors['video-url']['header'] ?></h3>
                                                    <p class="form__error-desc"><?= $errors['video-url']['description'] ?></p>
                                                </div>
                                            </div>
                                        </div>

                                        <?php $tags = getTagsAddPost($type_id); ?>
                                        <?php require 'templates/adding-tags.php'; ?>

                                    </div>

                                    <?php require 'templates/adding-errors.php'; ?>

                                </div>

                                <?php require 'templates/adding-publish.php'; ?>

                            </form>
                        </section>

                    <?php elseif ($type_id === 3) : ?>

                        <section class="adding-post__text tabs__content tabs__content--active">
                            <h2 class="visually-hidden">Форма добавления текста</h2>
                            <form class="adding-post__form form" action="add.php" method="post">
                                <input type="hidden" name="type_id" value="3">
                                <div class="form__text-inputs-wrapper">
                                    <div class="form__text-inputs">

                                        <?php $heading = getHeadingAddPost($type_id); ?>
                                        <?php require 'templates/adding-heading.php'; ?>

                                        <div class="adding-post__textarea-wrapper form__textarea-wrapper <?= isset($errors['text-text']) ? "form__input-section--error" : ""; ?>">
                                            <label class="adding-post__label form__label" for="text-text">Текст поста <span class="form__input-required">*</span></label>
                                            <div class="form__input-section">
                                                <textarea class="adding-post__textarea form__textarea form__input" id="text-text" name="text-text" placeholder="Введите текст публикации"><?= getPostVal($_POST, 'text-text'); ?></textarea>
                                                <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                                <div class="form__error-text">
                                                    <h3 class="form__error-title"><?= $errors['text-text']['header'] ?></h3>
                                                    <p class="form__error-desc"><?= $errors['text-text']['description'] ?></p>
                                                </div>
                                            </div>
                                        </div>

                                        <?php $tags = getTagsAddPost($type_id); ?>
                                        <?php require 'templates/adding-tags.php'; ?>

                                    </div>

                                    <?php require 'templates/adding-errors.php'; ?>

                                </div>

                                <?php require 'templates/adding-publish.php'; ?>

                            </form>
                        </section>

                    <?php elseif ($type_id === 4) : ?>

                        <section class="adding-post__quote tabs__content tabs__content--active">
                            <h2 class="visually-hidden">Форма добавления цитаты</h2>
                            <form class="adding-post__form form" action="add.php" method="post">
                                <input type="hidden" name="type_id" value="4">
                                <div class="form__text-inputs-wrapper">
                                    <div class="form__text-inputs">

                                        <?php $heading = getHeadingAddPost($type_id); ?>
                                        <?php require 'templates/adding-heading.php'; ?>

                                        <div class="adding-post__input-wrapper form__textarea-wrapper <?= isset($errors['quote-text']) ? "form__input-section--error" : ""; ?>">
                                            <label class="adding-post__label form__label" for="quote-text">Текст цитаты <span class="form__input-required">*</span></label>
                                            <div class="form__input-section">
                                                <textarea class="adding-post__textarea adding-post__textarea--quote form__textarea form__input" id="quote-text" name="quote-text" placeholder="Текст цитаты"><?= getPostVal($_POST, 'quote-text'); ?></textarea>
                                                <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                                <div class="form__error-text">
                                                    <h3 class="form__error-title"><?= $errors['quote-text']['header'] ?></h3>
                                                    <p class="form__error-desc"><?= $errors['quote-text']['description'] ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="adding-post__textarea-wrapper form__input-wrapper <?= isset($errors['quote-author']) ? "form__input-section--error" : ""; ?>">
                                            <label class="adding-post__label form__label" for="quote-author">Автор <span class="form__input-required">*</span></label>
                                            <div class="form__input-section">
                                                <input class="adding-post__input form__input" id="quote-author" type="text" name="quote-author" value="<?= getPostVal($_POST, 'quote-author'); ?>">
                                                <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                                <div class="form__error-text">
                                                    <h3 class="form__error-title"><?= $errors['quote-author']['header'] ?></h3>
                                                    <p class="form__error-desc"><?= $errors['quote-author']['description'] ?></p>
                                                </div>
                                            </div>
                                        </div>

                                        <?php $tags = getTagsAddPost($type_id); ?>
                                        <?php require 'templates/adding-tags.php'; ?>

                                    </div>

                                    <?php require 'templates/adding-errors.php'; ?>

                                </div>

                                <?php require 'templates/adding-publish.php'; ?>

                            </form>
                        </section>

                    <?php elseif ($type_id === 5) : ?>

                        <section class="adding-post__link tabs__content tabs__content--active">
                            <h2 class="visually-hidden">Форма добавления ссылки</h2>
                            <form class="adding-post__form form" action="add.php" method="post">
                                <input type="hidden" name="type_id" value="5">
                                <div class="form__text-inputs-wrapper">
                                    <div class="form__text-inputs">

                                        <?php $heading = getHeadingAddPost($type_id); ?>
                                        <?php require 'templates/adding-heading.php'; ?>

                                        <div class="adding-post__textarea-wrapper form__input-wrapper <?= isset($errors['link-url']) ? "form__input-section--error" : ""; ?>">
                                            <label class="adding-post__label form__label" for="link-url">Ссылка <span class="form__input-required">*</span></label>
                                            <div class="form__input-section">
                                                <input class="adding-post__input form__input" id="link-url" type="text" name="link-url" value="<?= getPostVal($_POST, 'link-url'); ?>">
                                                <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                                                <div class="form__error-text">
                                                    <h3 class="form__error-title"><?= $errors['link-url']['header'] ?></h3>
                                                    <p class="form__error-desc"><?= $errors['link-url']['description'] ?></p>
                                                </div>
                                            </div>
                                        </div>

                                        <?php $tags = getTagsAddPost($type_id); ?>
                                        <?php require 'templates/adding-tags.php'; ?>

                                    </div>

                                    <?php require 'templates/adding-errors.php'; ?>

                                </div>

                                <?php require 'templates/adding-publish.php'; ?>

                            </form>
                        </section>

                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
 </main>
