<?php
include 'headers.php';

/* header('Content-Type: application/json'); */
/* header("Access-Control-Allow-Origin: *"); */
/* header("Access-Control-Allow-Headers: *"); */
/* header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS'); */

/* const PATH_DB = 'sqlite:' . __DIR__ . '/../db.sqlite3'; */

/* $con = new PDO(PATH_DB); */

include 'db.php';

$query =  "SELECT * FROM cols";

foreach ($con->query($query) as $row) {
    $rows[] = $row;
}

echo json_encode($rows);
die;
