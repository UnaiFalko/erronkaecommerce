<h1>Erregistratu</h1>
<form method="post" class="form narrow" action="index.php?c=auth&amp;a=register">
    <?= csrfField() ?>
    <label>Izena <input type="text" name="izena" required autofocus></label>
    <label>Emaila <input type="email" name="email" required></label>
    <label>Pasahitza (6+ karaktere) <input type="password" name="pasahitza" minlength="6" required></label>
    <button class="btn">Kontua sortu</button>
    <p class="muted">Badaukazu kontua? <a href="index.php?c=auth&amp;a=login">Hasi saioa</a></p>
</form>
