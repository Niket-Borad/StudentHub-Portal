<?php

require_once __DIR__ . "/auth.php";
startAppSession();

header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store");

echo json_encode(["token" => ensureCsrfToken()], JSON_THROW_ON_ERROR);
