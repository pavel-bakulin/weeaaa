<?php

$captcha_check = FALSE;
if (!isset($_SESSION)) session_start();
if (isset($_POST['random_string']))
if (isset($_SESSION['random_string']))
if ($_POST['random_string'])
if ($_POST['random_string']==$_SESSION['random_string']) {
$captcha_check = TRUE;
unset($_SESSION['random_string']);
}

if (isset($_POST['random_string']) && $captcha_check) {
  echo "<div>Код введен правильно!</div>";
} 
elseif (isset($_POST['random_string'])) {
  echo "<div>Код введен НЕПРАВИЛЬНО поробуйте еще раз!</div>";
}
?>
<form method="POST" action="<?php $_SERVER['PHP_SELF'] ?>">
<img src='/test/capcha/captcha.php' border=1><br />
<input type=text name=random_string><br />
<input type="submit" class="form-submit" value="Проверить" />
</form>
