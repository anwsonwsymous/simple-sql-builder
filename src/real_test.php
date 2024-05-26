<?php

use FpDbTest\Database;

require 'vendor/autoload.php';

$mysqli = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE'], $_ENV['DB_PORT']);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$db = new Database($mysqli);

// Build real queries
$queries = [
    $db->buildQuery('SELECT name FROM users WHERE user_id = 1'),
    $db->buildQuery('SELECT * FROM users WHERE name = ? AND block = 0', ['Charlie']),
    $db->buildQuery('SELECT ?# FROM users WHERE user_id = ?d AND block = ?d', [['name', 'email'], 2, true]),
    $db->buildQuery('SELECT name FROM users WHERE ?# IN (?a){ AND block = ?d}', ['user_id', [1, 2, 3], $db->skip()]),
    $db->buildQuery('SELECT name FROM users WHERE ?# IN (?a){ AND block = ?d}', ['user_id', [1, 2, 3], true]),
];

// Execute queries
foreach ($queries as $query) {
    echo "Executing query: $query\n";
    $result = $db->execute($query);
    if ($result instanceof mysqli_result) {
        while ($row = $result->fetch_assoc()) {
            print_r($row);
        }
        $result->free();
    } else {
        echo "Query executed successfully.\n";
    }
}

$mysqli->close();