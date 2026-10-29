<?php
require_once __DIR__ . '/../controller/BaseController.php';

$_SESSION = [];
session_regenerate_id(true);
session_destroy();
redirigir('login.php');
