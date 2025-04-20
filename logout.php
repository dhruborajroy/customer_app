<?php
// pin.php
session_start();

unset($_SESSION['authenticated']);
header("location: pin.php");