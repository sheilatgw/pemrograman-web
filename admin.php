
<?php
require_once 'config.php';

if (!is_admin()) {
    redirect('login.php');
}

$error = '';
$success = '';

$uploadDir = __DIR__ . '/uploads/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);

    if ($name === '' || $price === false || $price === null || $price <= 0) {
        $error = 'Nama produk dan harga yang valid wajib diisi.';
    } else {
        $imageName = null;

        if (
            isset($_FILES['image']) &&
            $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
        ) {
            $file = $_FILES['image'];

            if (
                $file['error'] !== UPLOAD_ERR_OK ||
                $file['size'] > 3 * 1024 * 1024
            ) {
                $error = 'Upload gagal. Ukuran gambar maksimal 3 MB.';
            } else {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($file['tmp_name']);

                $allowed = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp'
                ];

                if (
                    !isset($allowed[$mime]) ||
                    !getimagesize($file['tmp_name'])
                ) {
                    $error = 'Format harus JPG, PNG, atau WEBP.';
                } else {
                    $imageName = bin2hex(random_bytes(16))
                        . '.' . $allowed[$mime];

                    if (!move_uploaded_file(
                        $file['tmp_name'],
                        $uploadDir . $imageName
                    )) {
                        $error = 'Gambar gagal disimpan.';
                    }
                }
            }
        }

        if ($error === '') {
            try {
                $stmt = $pdo->prepare(
                    'INSERT INTO products (name, description, price, image)
                     VALUES (?, ?, ?, ?)'
                );

                $stmt->execute([
                    $name,
                    $description,
                    $price,
                    $imageName
                ]);

                $success = 'Produk berhasil ditambahkan.';
            } catch (PDOException $e) {
                if ($imageName && file_exists($uploadDir . $imageName)) {
                    unlink($uploadDir . $imageName);
                }

                $error = 'Produk gagal disimpan ke database.';
            }
        }
    }
}

$products = $pdo->query(
    'SELECT * FROM products ORDER BY id DESC'
)->fetchAll();

$pageTitle = 'Admin';
require 'partials/header.php';
?>

<h1 class="mb-5 text-2xl font-bold">
    Admin - Kelola produk
</h1>

<div class="mb-8 max-w-xl border border-stone-400 bg-white p-5">
    <h2 class="mb-4 text-lg font-bold">Tambah produk</h2>

    <?php if ($error): ?>
        <p class="mb-4 border border-red-700 p-3 text-sm">
            <?= e($error) ?>
        </p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="mb-4 border border-green-700 p-3 text-sm">
            <?= e($success) ?>
        </p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="space-y-4">
        <label class="block text-sm">
            Nama produk
            <input
                type="text"
                name="name"
                maxlength="150"
                required
                class="mt-1 w-full border border-stone-400 px-3 py-2"
            >
        </label>

        <label class="block text-sm">
            Deskripsi
            <textarea
                name="description"
                rows="3"
                class="mt-1 w-full border border-stone-400 px-3 py-2"
            ></textarea>
        </label>

        <label class="block text-sm">
            Harga (Rupiah)
            <input
                type="number"
                name="price"
                min="1"
                step="1"
                required
                class="mt-1 w-full border border-stone-400 px-3 py-2"
            >
        </label>

        <label class="block text-sm">
            Gambar (JPG/PNG/WEBP, maksimal 3 MB)
            <input
                type="file"
                name="image"
                accept="image/jpeg,image/png,image/webp"
                class="mt-1 block w-full border border-stone-400 p-2"
            >
        </label>

        <button
            type="submit"
            class="border border-stone-900 bg-stone-900 px-4 py-2 text-white"
        >
            Simpan produk
        </button>
    </form>
</div>

<h2 class="mb-3 text-lg font-bold">Daftar produk</h2>

<div class="overflow-x-auto border border-stone-400 bg-white">
    <table class="w-full text-left text-sm">
        <thead class="border-b border-stone-400 bg-stone-200">
            <tr>
                <th class="p-3">Nama</th>
                <th class="p-3">Harga</th>
                <th class="p-3">Gambar</th>
                <th class="p-3">Status</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($products as $product): ?>
                <tr class="border-b border-stone-200">
                    <td class="p-3"><?= e($product['name']) ?></td>
                    <td class="p-3"><?= rupiah($product['price']) ?></td>
                    <td class="p-3">
                        <?= e($product['image'] ?? 'Tidak ada') ?>
                    </td>
                    <td class="p-3">
                        <?= $product['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require 'partials/footer.php'; ?>
