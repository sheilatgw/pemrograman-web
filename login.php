
<?php
require_once 'config.php';

if (isset($_SESSION['user'])) {
    redirect(is_admin() ? 'admin.php' : 'index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare(
        'SELECT id, name, email, password, role
         FROM users WHERE email = ? LIMIT 1'
    );

    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        redirect($user['role'] === 'admin' ? 'admin.php' : 'index.php');
    }

    $error = 'Email atau password salah.';
}

$pageTitle = 'Login';
require 'partials/header.php';
?>

<div class="mx-auto max-w-md border border-stone-400 bg-white p-6">
    <h1 class="mb-5 text-2xl font-bold">Login</h1>

    <?php if ($error): ?>
        <p class="mb-4 border border-red-700 p-3 text-sm">
            <?= e($error) ?>
        </p>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <label class="block text-sm">
            Email
            <input
                type="email"
                name="email"
                required
                class="mt-1 w-full border border-stone-400 px-3 py-2"
            >
        </label>

        <label class="block text-sm">
            Password
            <input
                type="password"
                name="password"
                required
                class="mt-1 w-full border border-stone-400 px-3 py-2"
            >
        </label>

        <button
            type="submit"
            class="w-full border border-stone-900 bg-stone-900 px-4 py-2 text-white"
        >
            Login
        </button>
    </form>

    <p class="mt-4 text-sm">
        Belum punya akun?
        <a href="register.php" class="underline">Register</a>
    </p>
</div>

<?php require 'partials/footer.php'; ?>
