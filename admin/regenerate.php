<?php
require_once __DIR__ . '/config.php';
require_login();
generate_all();
header('Location: index.php?ok=saved');
exit;
