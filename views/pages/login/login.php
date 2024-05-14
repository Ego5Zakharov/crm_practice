<?php

use App\Kernel\Csrf\Csrf;

?>
<form action="<?= app_url() . '/login/' ?>" method="POST">
    <div>email</div>
    <input type="hidden" name="csrf_token" value="<?= Csrf::getCsrfToken(); ?>">
    <input type="email" name="email">
    <div>password</div>
    <input type="password" name="password">
    <br>
    <button type="submit" style="margin-top: 5px">Submit</button>
</form>