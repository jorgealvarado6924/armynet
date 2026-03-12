<?php
require_once __DIR__ . '/helpers.php';

ensure_session_started();
$_SESSION = [];
session_destroy();

redirect_to('login.php');
