<!-- пост-ссылка -->
<div class="post__main">
  <div class="post-link__wrapper">
    <a class="post-link__external" href="<?= htmlspecialchars($post['link']); ?>" title="Перейти по ссылке">
	  <div class="post-link__info-wrapper">
		<div class="post-link__icon-wrapper">
            <img src="./img/icon-htmlacademy.svg" alt="Иконка">
        </div>
		<div class="post-link__info">
		  <h3><?=$post['link'];?></h3>
        </div>
      </div>
	</a>
  </div>
</div>
