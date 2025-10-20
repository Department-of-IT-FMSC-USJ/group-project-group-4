#!/usr/bin/env php
<?php
/**
 * Admin User Management Script
 * 
 * Usage:
 *   php create_admin.php
 * 
 * This script helps you create new admin users with secure password hashing.
 */

require_once __DIR__ . '/config/database.php';

echo "\n==================================\n";
echo "  OneID Admin User Creator\n";
echo "==================================\n\n";

// Get username
echo "Enter username: ";
$username = trim(fgets(STDIN));

if (empty($username)) {
    die("Error: Username cannot be empty\n");
}

// Check if username already exists
$stmt = $pdo->prepare("SELECT username FROM admins WHERE username = ?");
$stmt->execute([$username]);
if ($stmt->fetch()) {
    die("Error: Username already exists\n");
}

// Get full name
echo "Enter full name: ";
$fullName = trim(fgets(STDIN));

// Get email
echo "Enter email: ";
$email = trim(fgets(STDIN));

// Get password
echo "Enter password: ";
$password = trim(fgets(STDIN));

if (empty($password)) {
    die("Error: Password cannot be empty\n");
}

// Confirm password
echo "Confirm password: ";
$confirmPassword = trim(fgets(STDIN));

if ($password !== $confirmPassword) {
    die("Error: Passwords do not match\n");
}

// Hash password
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Insert into database
try {
    $stmt = $pdo->prepare("INSERT INTO admins (username, password_hash, full_name, email) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $passwordHash, $fullName, $email]);

    echo "\n✓ Admin user created successfully!\n";
    echo "\nLogin credentials:\n";
    echo "  Username: $username\n";
    echo "  Password: [hidden]\n";
    echo "  Full Name: $fullName\n";
    echo "  Email: $email\n\n";
} catch (PDOException $e) {
    die("Error: Failed to create admin user - " . $e->getMessage() . "\n");
}
