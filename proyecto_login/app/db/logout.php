<?php

session_start();
unset($_SESSION["s_username"]);
session_destroy();
header("Location: ../login/index.php");
exit();
?>