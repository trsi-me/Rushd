<?php
// Helper script to generate password hashes
// Run this file once to get the correct password hashes

$passwords = [
    '' => 'admin',
    '' => 'user',
    '' => 'test'
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
    'admin' => password_hash('', PASSWORD_DEFAULT),
    'user' => password_hash('', PASSWORD_DEFAULT),
    'test' => password_hash('', PASSWORD_DEFAULT)
];

echo "INSERT INTO `users` (`username`, `email`, `password`) VALUES\n";
$values = [];
foreach ($hashes as $username => $hash) {
    $email = $username . '@rushd.com';
    $values[] = "('$username', '$email', '$hash')";
}
echo implode(",\n", $values) . ";\n";

?>
