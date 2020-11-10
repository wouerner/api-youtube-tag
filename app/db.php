<?php
const PATH_DB = 'sqlite:' . __DIR__ . '/../db.sqlite3';

$con = new PDO(PATH_DB);
