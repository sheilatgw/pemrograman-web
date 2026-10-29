
<?php
require_once 'config.php';

$email = 'admin@coffee.test';
$password = 'password';

$stmt = $pdo->prepare(
    'SELECT id FROM users WHERE email = ?'
);
$stmt->execute([$email]);

if (!$stmt->fetch()) {
    $stmt = $pdo->prepare(
        'INSERT INTO users (name, email, password, role)
         VALUES (?, ?, ?, ?)'
    );

    $stmt->execute([
        'Admin KopiKita',
        $email,
        password_hash($password, PASSWORD_DEFAULT),
        'admin'
    ]);

    echo 'Admin berhasil dibuat.';
} else {
    echo 'Akun admin sudah tersedia.';
}
