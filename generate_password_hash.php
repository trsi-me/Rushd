<?php
// Helper script to generate password hashes
// Run this file once to get the correct password hashes

$passwords = [
    'admin123' => 'admin',
    'user123' => 'user',
    'test123' => 'test'
];

echo "Password Hashes:\n";
echo "================\n\n";

foreach ($passwords as $password => $username) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    echo "Username: $username\n";
    echo "Password: $password\n";
    echo "Hash: $hash\n\n";
}

// Output SQL INSERT statements
echo "\nSQL INSERT Statements:\n";
echo "======================\n\n";

$hashes = [
    'admin' => password_hash('admin123', PASSWORD_DEFAULT),
    'user' => password_hash('user123', PASSWORD_DEFAULT),
    'test' => password_hash('test123', PASSWORD_DEFAULT)
];

echo "INSERT INTO `users` (`username`, `email`, `password`) VALUES\n";
$values = [];
foreach ($hashes as $username => $hash) {
    $email = $username . '@rushd.com';
    $values[] = "('$username', '$email', '$hash')";
}
echo implode(",\n", $values) . ";\n";

?>
