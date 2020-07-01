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
                    $dateTitle = strftime('%d.%m.%Y %H:%M', strtotime($post['creation_time']));
                    $dateView = dateDifferent($post['creation_time']);
                ?>
                                <time class="post__time" datetime="<?= $post['creation_time'] ?>" title="<?= $dateTitle ?>"><?= $dateView ?></time>


                <time class="profile__user-time user__time" datetime="2014-03-20">5 лет на сайте</time>
              </div>
            </div>
            <div class="profile__rating user__rating">
              <p class="profile__rating-item user__rating-item user__rating-item--publications">
                <span class="user__rating-amount">556</span>
                <span class="profile__rating-text user__rating-text">публикаций</span>
              </p>
              <p class="profile__rating-item user__rating-item user__rating-item--subscribers">
                <span class="user__rating-amount">1856</span>
                <span class="profile__rating-text user__rating-text">подписчиков</span>
              </p>
            </div>
            <div class="profile__user-buttons user__buttons">
              <button class="profile__user-button user__button user__button--subscription button button--main" type="button">Подписаться</button>
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
                <article class="profile__post post post-photo">
                  <header class="post__header">
                    <h2>Наконец, обработал фотки!</h2>
                  </header>
                  <div class="post__main">
                    <div class="post-photo__image-wrapper">
                      <img src="img/rock.jpg" alt="Фото от пользователя" width="760" height="396">
                    </div>
                  </div>
                  <footer class="post__footer">
                    <div class="post__indicators">
                      <div class="post__buttons">
                        <a class="post__indicator post__indicator--likes button" href="#" title="Лайк">
                          <svg class="post__indicator-icon" width="20" height="17">
                            <use xlink:href="#icon-heart"></use>
                          </svg>
                          <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                            <use xlink:href="#icon-heart-active"></use>
                          </svg>
                          <span>250</span>
                          <span class="visually-hidden">количество лайков</span>
                        </a>
                        <a class="post__indicator post__indicator--repost button" href="#" title="Репост">
                          <svg class="post__indicator-icon" width="19" height="17">
                            <use xlink:href="#icon-repost"></use>
                          </svg>
                          <span>5</span>
                          <span class="visually-hidden">количество репостов</span>
                        </a>
                      </div>
                      <time class="post__time" datetime="2019-01-30T23:41">15 минут назад</time>
                    </div>
                    <ul class="post__tags">
                      <li><a href="#">#nature</a></li>
                      <li><a href="#">#globe</a></li>
                      <li><a href="#">#photooftheday</a></li>
                      <li><a href="#">#canon</a></li>
                      <li><a href="#">#landscape</a></li>
                      <li><a href="#">#щикарныйвид</a></li>
                    </ul>
                  </footer>
                  <div class="comments">
                    <a class="comments__button button" href="#">Показать комментарии</a>
                  </div>
                </article>
                <article class="profile__post post post-text">
                  <header class="post__header">
                    <div class="post__author">
                      <a class="post__author-link" href="#" title="Автор">
                        <div class="post__avatar-wrapper post__avatar-wrapper--repost">
                          <img class="post__author-avatar" src="img/userpic-tanya.jpg" alt="Аватар пользователя">
                        </div>
                        <div class="post__info">
                          <b class="post__author-name">Репост: Таня Фирсова</b>
                          <time class="post__time" datetime="2019-03-30T14:31">25 минут назад</time>
                        </div>
                      </a>
                    </div>
                  </header>
                  <div class="post__main">
                    <h2>Полезный пост про Байкал</h2>
                    <p>
                      Озеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы. Байкал считается самым глубоким озером в мире. Он окружен сетью пешеходных маршрутов, называемых Большой байкальской тропой. Деревня Листвянка, расположенная на западном берегу озера, – популярная отправная точка для летних экскурсий. Зимой здесь можно кататься на коньках и собачьих упряжках.
                    </p>
                    <a class="post-text__more-link" href="#">Читать далее</a>
                  </div>
                  <footer class="post__footer">
                    <div class="post__indicators">
                      <div class="post__buttons">
                        <a class="post__indicator post__indicator--likes button" href="#" title="Лайк">
                          <svg class="post__indicator-icon" width="20" height="17">
                            <use xlink:href="#icon-heart"></use>
                          </svg>
                          <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                            <use xlink:href="#icon-heart-active"></use>
                          </svg>
                          <span>250</span>
                          <span class="visually-hidden">количество лайков</span>
                        </a>
                        <a class="post__indicator post__indicator--repost button" href="#" title="Репост">
                          <svg class="post__indicator-icon" width="19" height="17">
                            <use xlink:href="#icon-repost"></use>
                          </svg>
                          <span>5</span>
                          <span class="visually-hidden">количество репостов</span>
                        </a>
                      </div>
                      <time class="post__time" datetime="2019-01-30T23:41">15 минут назад</time>
                    </div>
                    <ul class="post__tags">
                      <li><a href="#">#nature</a></li>
                      <li><a href="#">#globe</a></li>
                      <li><a href="#">#photooftheday</a></li>
                      <li><a href="#">#canon</a></li>
                      <li><a href="#">#landscape</a></li>
                      <li><a href="#">#щикарныйвид</a></li>
                    </ul>
                  </footer>
                  <div class="comments">
                    <div class="comments__list-wrapper">
                      <ul class="comments__list">
                        <li class="comments__item user">
                          <div class="comments__avatar">
                            <a class="user__avatar-link" href="#">
                              <img class="comments__picture" src="img/userpic-larisa.jpg" alt="Аватар пользователя">
                            </a>
                          </div>
                          <div class="comments__info">
                            <div class="comments__name-wrapper">
                              <a class="comments__user-name" href="#">
                                <span>Лариса Роговая</span>
                              </a>
                              <time class="comments__time" datetime="2019-03-20">1 ч назад</time>
                            </div>
                            <p class="comments__text">
                              Красота!!!1!
                            </p>
                          </div>
                        </li>
                        <li class="comments__item user">
                          <div class="comments__avatar">
                            <a class="user__avatar-link" href="#">
                              <img class="comments__picture" src="img/userpic-larisa.jpg" alt="Аватар пользователя">
                            </a>
                          </div>
                          <div class="comments__info">
                            <div class="comments__name-wrapper">
                              <a class="comments__user-name" href="#">
                                <span>Лариса Роговая</span>
                              </a>
                              <time class="comments__time" datetime="2019-03-18">2 дня назад</time>
                            </div>
                            <p class="comments__text">
                              Озеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы. Байкал считается самым глубоким озером в мире. Он окружен сетью пешеходных маршрутов, называемых Большой байкальской тропой. Деревня Листвянка, расположенная на западном берегу озера, – популярная отправная точка для летних экскурсий. Зимой здесь можно кататься на коньках и собачьих упряжках.
                            </p>
                          </div>
                        </li>
                      </ul>
                      <a class="comments__more-link" href="#">
                        <span>Показать все комментарии</span>
                        <sup class="comments__amount">45</sup>
                      </a>
                    </div>
                  </div>
                  <form class="comments__form form" action="#" method="post">
                    <div class="comments__my-avatar">
                      <img class="comments__picture" src="img/userpic-medium.jpg" alt="Аватар пользователя">
                    </div>
                    <textarea class="comments__textarea form__textarea" placeholder="Ваш комментарий"></textarea>
                    <label class="visually-hidden">Ваш комментарий</label>
                    <button class="comments__submit button button--green" type="submit">Отправить</button>
                  </form>
                </article>
              </section>
            </div>
          </div>
        </div>
    </div>
</main>
