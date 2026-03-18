<?php
declare(strict_types=1);

require_once(__DIR__ . "/../src/infrastructure/database/conn.php");
require_once(__DIR__ . "/../src/infrastructure/http/respond.php");

use app\infrastructure\database\DB;
use app\infrastructure\http\Respond;

$conn = DB::getConnection();

Respond::json($conn->query("SELECT * FROM test;")->fetchAll());
