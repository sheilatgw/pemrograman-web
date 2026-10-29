
<?php
require_once 'config.php';

if (isset($_SESSION['user'])) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (
        $name === '' ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($password) < 8
    ) {
        $error = 'Isi data dengan benar. Password minimal 8 karakter.';
    } else {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO users (name, email, password, role)
                 VALUES (?, ?, ?, "customer")'
            );

            $stmt->execute([
                $name,
                $email,
                password_hash($password, PASSWORD_DEFAULT)
            ]);

            session_regenerate_id(true);

            $_SESSION['user'] = [
                'id' => (int) $pdo->lastInsertId(),
                'name' => $name,
                'email' => $email,
                'role' => 'customer'
            ];

            redirect('index.php');

        } catch (PDOException $e) {
            $error = 'Email sudah digunakan atau registrasi gagal.';
        }
    }
}

$pageTitle = 'Register';
require 'partials/header.php';
?>

<div class="mx-auto max-w-md border border-stone-400 bg-white p-6">
    <h1 class="mb-5 text-2xl font-bold">Register pelanggan</h1>

    <?php if ($error): ?>
        <p class="mb-4 border border-red-700 p-3 text-sm">
            <?= e($error) ?>
        </p>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <label class="block text-sm">
            Nama
            <input
                name="name"
                maxlength="100"
                required
                value="<?= e($_POST['name'] ?? '') ?>"
                class="mt-1 w-full border border-stone-400 px-3 py-2"
            >
        </label>

        <label class="block text-sm">
            Email
            <input
                type="email"
                name="email"
                required
                value="<?= e($_POST['email'] ?? '') ?>"
                class="mt-1 w-full border border-stone-400 px-3 py-2"
            >
        </label>

        <label class="block text-sm">
            Password (minimal 8 karakter)
            <input
                type="password"
                name="password"
                minlength="8"
                required
                class="mt-1 w-full border border-stone-400 px-3 py-2"
            >
        </label>

        <button
            type="submit"
            class="w-full border border-stone-900 bg-stone-900 px-4 py-2 text-white"
        >
            Buat akun
        </button>
    </form>

    <p class="mt-4 text-sm">
        Sudah punya akun?
        <a href="login.php" class="underline">Login</a>
    </p>
</div>

<?php require 'partials/footer.php'; ?>
