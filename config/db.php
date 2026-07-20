<?php
// ===============================
// Database connection (XAMPP / MySQL)
// ===============================
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";          // default XAMPP MySQL password is empty
$DB_NAME = "elyzia_db";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
