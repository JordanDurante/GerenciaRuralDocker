<?php
session_start();
session_unset();
session_destroy();
header("Location: ../../classes/login/login.php");
exit;
?>