<?php
session_start();
session_destroy();
header('Location: /sms/index.php');
exit();