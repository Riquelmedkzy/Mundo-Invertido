<?php
$viewer=empty($safeError) ? current_user() : null;
$route=is_string($_GET['route']??null)?$_GET['route']:'home';
$notice=$_SESSION['flash']??null;unset($_SESSION['flash']);
?><!doctype html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e(($title??'Mundo Invertido').' | Projeto Invertido') ?></title>
<meta name="description" content="Atravesse o portal de Hawkins. Explore o Mundo Invertido, conheça o universo de Stranger Things e entre para o clube de D&D.">
<meta name="theme-color" content="#b50920"><meta name="robots" content="noindex,nofollow">
<link rel="icon" href="<?= e(img('banner/stranger-things-logo.svg')) ?>" type="image/svg+xml">
<link rel="stylesheet" href="<?= e(css('site')) ?>?v=20261107-3">
<link rel="stylesheet" href="<?= e(css('cinematic')) ?>?v=20261107-3">
<script src="<?= e(js('motion-preference')) ?>"></script>
<script src="<?= e(js('vendor/gsap.min')) ?>" defer></script>
<script src="<?= e(js('site')) ?>?v=20261107-3" defer></script>
<script src="<?= e(js('atmosphere')) ?>?v=20261107-3" defer></script>
<?php if(in_array($route,['portfolio','temporadas'],true)): ?><script src="<?= e(js('seasons')) ?>?v=20261107-3" defer></script><?php endif ?>
</head>
<body class="<?= e($pageClass??'inner-page') ?>" data-world="inverted">
<a class="skip" href="#main">Ir para o conteúdo</a>
<header class="site-header">
<a class="brand" href="<?= e(url()) ?>" aria-label="Stranger Things — início"><img class="brand-logo" src="<?= e(img('banner/stranger-things-logo.svg')) ?>" alt="Stranger Things" width="160" height="58"></a>
<button class="motion-toggle" data-motion-toggle aria-pressed="false"><svg class="icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path d="M8 5v14M16 5v14"/></svg><span>Desativar animações</span></button>
<button class="menu-toggle" aria-expanded="false" aria-controls="navigation">Menu <svg class="icon" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="M3 6h18M3 12h18M3 18h18"/></svg></button>
<nav id="navigation" aria-label="Menu principal">
<a href="<?= e(url()) ?>" <?= $route==='home'?'aria-current="page"':'' ?>>Início</a>
<a href="<?= e(url('temporadas')) ?>" <?= in_array($route,['portfolio','temporadas'],true)?'aria-current="page"':'' ?>>Temporadas</a>
<a href="<?= e(url('contact')) ?>" <?= $route==='contact'?'aria-current="page"':'' ?>>Contato</a>
<?php if($viewer): ?>
<a href="<?= e(url('profile')) ?>">Meu perfil</a>
<?php if($viewer['user_type']==='adm'): ?><a href="<?= e(url('admin')) ?>">Administração</a><?php endif ?>
<form action="<?= e(url('auth_logout')) ?>" method="post" class="inline-form"><?php csrf_field() ?><button class="nav-cta" type="submit">Sair</button></form>
<?php else: ?><a class="nav-cta" href="<?= e(url('auth_login')) ?>">Área Restrita <span aria-hidden="true"><svg class="icon" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="M5 19 19 5M5 5h14v14"/></svg></span></a><?php endif ?>
</nav>
</header>
<?php if($notice): ?><div class="notice <?= e($notice['kind']) ?>" role="status"><?= e($notice['message']) ?></div><?php endif ?>
<?php if(!empty($errors)): ?><div class="notice error" role="alert"><strong>Vamos revisar:</strong><ul><?php foreach($errors as $error): ?><li><?= e($error) ?></li><?php endforeach ?></ul></div><?php endif ?>
