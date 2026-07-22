<?php
// Generates real bcrypt hashes for the seeded admin + client accounts.
// Run once after importing the schema:   php tools/seed_passwords.php

require_once __DIR__ . '/../config/database.php';

$db = Database::connect();
$accounts = [
    ['email' => 'admin@techhouse.local',  'password' => 'admin123'],
    ['email' => 'client@techhouse.local', 'password' => 'client123'],
];

foreach ($accounts as $a) {
    $hash = password_hash($a['password'], PASSWORD_BCRYPT);
    $stmt = $db->prepare('UPDATE users SET password_hash = ? WHERE email = ?');
    $stmt->execute([$hash, $a['email']]);
    echo "Updated: {$a['email']}\n";
}
echo "Done.\n";
