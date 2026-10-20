<h1>Saioa hasi</h1>
<form method="post" class="form narrow" action="index.php?c=auth&amp;a=login">
    <?= csrfField() ?>
    <label>Emaila <input type="email" name="email" required autofocus></label>
    <label>Pasahitza <input type="password" name="pasahitza" required></label>
    <button class="btn">Sartu</button>
    <p class="muted">Kontua ez duzu? <a href="index.php?c=auth&amp;a=register">Erregistratu</a></p>
</form>
