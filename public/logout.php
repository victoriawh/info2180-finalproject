<?php

require '../src/auth.php';

logout();

header("Location: login.php");
exit();
?>