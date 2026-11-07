<?php require INCLUDES_PATH.'/season_data.php'; ?>
<main id="main" class="seasons-main">
<section class="season-cinema" aria-labelledby="season-title" data-seasons aria-roledescription="carrossel">
<div class="season-ambient" aria-hidden="true"><img src="<?= e(img($seasons[1]['image'])) ?>" alt=""></div>
<div class="season-heading"><p>Stranger Things <span>As cinco temporadas</span></p><div class="season-arrows"><button type="button" data-season-prev aria-label="Temporada anterior"><svg class="icon" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="m14 6-6 6 6 6"/></svg></button><span data-season-counter>01 / 05</span><button type="button" data-season-next aria-label="Próxima temporada"><svg class="icon" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="m10 6 6 6-6 6"/></svg></button></div></div>
<div class="season-stage">
<?php foreach($seasons as $n=>$season): ?>
<div class="season-scene" data-season-scene="<?= $n ?>" <?= $n!==1?'hidden':'' ?>><img src="<?= e(img($season['image'])) ?>" alt="<?= e($season['alt']) ?>" <?= $n===1?'fetchpriority="high"':'loading="lazy"' ?> width="1440" height="810"><div class="season-shade"></div></div>
<?php endforeach ?>
<canvas class="atmosphere" data-rain aria-hidden="true"></canvas>
<div class="season-copy"><p class="season-meta" data-season-meta>Temporada 1 <span>2016 · 8 episódios</span></p><h1 id="season-title"><span class="title-mask"><span>O começo</span></span><span class="title-mask"><span>do impossível.</span></span></h1><p class="season-intro" data-season-intro><?= e($seasons[1]['intro']) ?></p><div class="season-actions"><a class="button red" href="#trailer-1" data-hero-trailer><svg class="icon" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="m8 4 12 8-12 8Z"/></svg>Assistir ao trailer</a><a class="text-link" href="#history-1" data-hero-story>Conheça a história <span aria-hidden="true">↓</span></a></div></div>
<div class="season-scene-number" aria-hidden="true" data-season-number>01</div>
</div>
<div class="season-selector" role="group" aria-label="Escolha a temporada">
<?php foreach($seasons as $n=>$season): ?>
<a class="season-choice <?= $n===1?'active':'' ?>" href="#season-<?= $n ?>" data-season="<?= $n ?>" data-lines="<?= e(str_replace('<br>','|',$season['title'])) ?>" data-title="<?= e($season['plain']) ?>" data-intro="<?= e($season['intro']) ?>" data-year="<?= e($season['year']) ?>" data-episodes="<?= $season['episodes'] ?>" aria-current="<?= $n===1?'true':'false' ?>" aria-label="Temporada <?= $n ?>, <?= e($season['year']) ?>"><span class="thumb-image"><img src="<?= e(img($season['image'])) ?>" alt="" loading="lazy" width="240" height="135"></span><span class="thumb-caption"><b><?= sprintf('%02d',$n) ?></b><span>Temporada <?= $n ?><small><?= e($season['year']) ?></small></span><svg class="icon" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M5 19 19 5M5 5h14v14"/></svg></span></a>
<?php endforeach ?>
</div>
<p class="season-hint">Escolha um capítulo da história.<span>Use também as setas do teclado</span></p>
<p class="sr-only" aria-live="polite" aria-atomic="true" data-season-announcement></p>
<p class="season-error" role="status" data-season-error hidden></p>
</section>
<div class="season-details">
<?php foreach($seasons as $n=>$season): ?>
<article id="season-<?= $n ?>" class="season-detail" data-season-detail="<?= $n ?>">
<section class="season-story" id="history-<?= $n ?>"><div><h2>Hawkins, <?= e($season['time']) ?>.</h2><p class="story-period">Temporada <?= $n ?> · <?= e($season['year']) ?> · <?= $season['episodes'] ?> episódios</p></div><div><p><?= e($season['story']) ?></p><a class="text-link" href="https://www.netflix.com/tudum/stranger-things" target="_blank" rel="noopener noreferrer">Explore no Netflix Tudum <svg class="icon" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M5 19 19 5M5 5h14v14"/></svg></a></div></section>
<section class="season-trailer" id="trailer-<?= $n ?>" aria-labelledby="trailer-title-<?= $n ?>"><div class="trailer-header"><h2 id="trailer-title-<?= $n ?>">Antes de atravessar,<br>aperte o play.</h2><p>Trailer<?= $n===3?' final':' oficial' ?> · Temporada <?= $n ?><?= $n===4?' · Volume 2':'' ?><br><?= in_array($n,[1,2,3])?'Áudio original em inglês.':'Áudio em português.' ?></p></div>
<div class="season-video" data-trailer-container>
<?php if($season['video']): ?>
<div class="trailer-preview" data-video-preview><img src="<?= e(img($season['image'])) ?>" alt="" loading="lazy" width="1280" height="720"><button type="button" class="trailer-play" data-video-id="<?= e($season['video']) ?>" aria-label="Reproduzir trailer da temporada <?= $n ?>"><svg class="icon" viewBox="0 0 24 24" width="32" height="32" aria-hidden="true"><path d="m8 4 12 8-12 8Z"/></svg><span>Reproduzir trailer</span></button></div>
<?php else: ?>
<video controls preload="none" poster="<?= e(img('content/serie-image-02.png')) ?>" aria-label="Trailer oficial da temporada 4, volume 2"><source src="<?= e(asset('videos/Stranger Things 4 _ Trailer do volume 2 _ Netflix.mp4')) ?>" type="video/mp4">Seu navegador não suporta vídeo HTML5.</video>
<?php endif ?>
</div><p class="video-help"><?= $n===3?'Trailer Netflix · Reprodução: 123 Trailers.':'Os trailers podem revelar acontecimentos da temporada.' ?> <a href="<?= e($season['official']) ?>" target="_blank" rel="noopener noreferrer">Assistir na fonte oficial <svg class="icon" viewBox="0 0 24 24" width="12" height="12" aria-hidden="true"><path d="M5 19 19 5M5 5h14v14"/></svg></a></p></section>
<section class="season-cast" aria-labelledby="cast-title-<?= $n ?>"><div class="cast-heading"><h2 id="cast-title-<?= $n ?>">Quem atravessa<br>com você.</h2><p>Personagens em destaque.<br>Fotos de divulgação da série.</p></div><div class="cast-grid">
<?php foreach($season['cast'] as $key): $char=$characters[$key]; ?>
<figure><img src="<?= e(img($char[2])) ?>" alt="<?= e($char[0]) ?> em Stranger Things" loading="lazy" width="495" height="279"><figcaption><h3><?= e($char[0]) ?></h3><p><?= e($char[1]) ?></p></figcaption></figure>
<?php endforeach ?>
</div></section>
</article>
<?php endforeach ?>
</div>
</main>
