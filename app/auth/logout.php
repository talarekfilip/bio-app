<?php
require_once '../config/config.php';
require_once 'auth.php';

session_destroy();
header('Location: ../index.php');
exit; 