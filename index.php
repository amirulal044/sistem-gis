<?php
session_start();
if(isset($_SESSION['user_id'])) {
    header("Location: pages/user/user.php");
    exit;
}
header("Location: auth/login.php");
