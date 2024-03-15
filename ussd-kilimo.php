<?php

require_once 'config.php';  // Assuming `config.php` contains database connection details

// **Do not directly include database credentials in code**
// Use environment variables or a secure configuration system.

// Replace placeholders with actual environment variable names
define('DB_HOST', getenv('DB_HOST'));
define('DB_USERNAME', getenv('DB_USERNAME'));
define('DB_PASSWORD', getenv('DB_PASSWORD'));
define('DB_NAME', getenv('DB_NAME'));

// Database connection (replace with your library's functions)
try {
    $conn = new PDO("pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set error handling mode
} catch (PDOException $e) {
    // Handle connection errors gracefully (e.g., log the error, display a user-friendly message)
    echo 'Database connection error: ' . $e->getMessage();
    exit;
}

// USSD session management (replace with your library's functions)
use Your\Ussd\Library;

$session = new Library();

$userInput = $session->getInput();

if (!$userInput) {
    // Initial menu
    $session->send("KARIBU KILIMO TAARRIFA\n"
        . "1. MARKET PRICE\n"
        . "2. FIND BUYER\n"
        . "3. PROJECTS\n"
        . "0. EXIT");
} else {
    switch ($userInput) {
        case '1':
            // Handle market price inquiry
            $response = getMarketPrices();
            $session->send($response);
            break;
        case '2':
            // Handle buyer discovery
            $response = getBuyers();
            $session->send($response);
            break;
        case '3':
            // Handle project and opportunity exploration
            $response = getProjects();
            $session->send($response);
            break;
        case '0':
            $session->end('Thank you for using the service.');
            break;
        default:
            $session->send('Invalid option. Please try again.');
    }
}

// Function to retrieve market prices (using database)
function getMarketPrices() {
    global $conn;

    $sql = "SELECT crop, price FROM market_prices";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $response = "Current market prices:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $response .= "- " . $row['crop'] . ": " . $row['price'] . "/kg\n";
    }

    return $response;
}

// Function to retrieve buyers (using database)
function getBuyers() {
    global $conn;

    $sql = "SELECT name, contact, crops FROM buyers";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $response = "List of potential buyers:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $response .= "- " . $row['name'] . " (" . $row['crops'] . "): " . $row['contact'] . "\n";
    }

    return $response;
}

// Function to retrieve projects (using database)
function getProjects() {
    global $conn;

    $sql = "SELECT title, description FROM projects";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $response = "Current agricultural projects:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $response .= "- " . $row['title'] . ": " . $row['description'] . "\n";
    }

    return $response;
}

