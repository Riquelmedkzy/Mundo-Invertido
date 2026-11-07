<form action="<?= e(url('register_add')) ?>" method="post" class="stack-form">
<?php csrf_field() ?>
<label for="reg-name">Nome completo<input id="reg-name" name="name" autocomplete="name" required minlength="2" maxlength="100" value="<?= e($old['name']??'') ?>"></label>
<label for="reg-email">E-mail<input id="reg-email" name="email" type="email" autocomplete="email" required maxlength="150" value="<?= e($old['email']??'') ?>"></label>
<label for="reg-phone">Telefone com DDD<input id="reg-phone" name="phone" type="tel" autocomplete="tel" required minlength="8" maxlength="20" placeholder="(21) 99999-9999" value="<?= e($old['phone']??'') ?>"></label>
<label for="reg-password">Senha<input id="reg-password" name="password" type="password" autocomplete="new-password" required minlength="10" maxlength="72" aria-describedby="password-help"></label>
<p class="field-help" id="password-help">Use pelo menos 10 caracteres. Prefira uma senha única para esta conta.</p>
<label for="reg-confirm">Confirme a senha<input id="reg-confirm" name="password_confirm" type="password" autocomplete="new-password" required minlength="10" maxlength="72"></label>
<button type="submit" class="button red">Garantir meu lugar <span aria-hidden="true"><svg class="icon" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="M5 19 19 5M5 5h14v14"/></svg></span></button>
<p class="form-foot">Já faz parte? <a href="<?= e(url('auth_login')) ?>">Entre na sua conta.</a></p>
</form>
