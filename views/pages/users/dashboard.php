<?php

use App\Kernel\Auth\Auth;

?>
<div>
    <?php

    if (Auth::isAuth()) {
        echo "<div>Привет, $user->name!</div>";
    } else {
        echo "<div>Вы не авторизованы.</div>";
    }
    ?>

</div>