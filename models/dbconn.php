<?php
$conn = mysqli_connect('localhost', 'root', '', 'home_service_pro') or die('Connection Failed');
mysqli_set_charset($conn, "utf8mb4");
date_default_timezone_set('Asia/Manila');
