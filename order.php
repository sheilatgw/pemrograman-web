
<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

$productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

if (!$productId || !$quantity || $quantity < 1 || $quantity > 20) {
    exit('Produk atau jumlah pesanan tidak valid.');
}

$stmt = $pdo->prepare(
    'SELECT id, name, price
     FROM products
     WHERE id = ? AND is_active = 1'
);

$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    exit('Produk tidak ditemukan.');
}

// Cek status login
$loggedIn = is_logged_in();

// Harga dari database, bukan input browser
$unitPrice = (float) $product['price'];
$subtotal = $unitPrice * $quantity;

// Diskon hanya untuk pelanggan yang login
$discountRate = $loggedIn ? 0.10 : 0;
$discount = round($subtotal * $discountRate, 2);
$total = $subtotal - $discount;

// Simpan pesanan
$stmt = $pdo->prepare(
    'INSERT INTO orders (
        user_id,
        customer_name,
        product_id,
        product_name,
        quantity,
        unit_price,
        discount_rate,
        discount_amount,
        total_price
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
);

$stmt->execute([
    $loggedIn ? (int) $_SESSION['user']['id'] : null,
    $loggedIn ? $_SESSION['user']['name'] : 'Pelanggan tanpa login',
    $product['id'],
    $product['name'],
    $quantity,
    $unitPrice,
    $discountRate,
    $discount,
    $total
]);

$orderId = (int) $pdo->lastInsertId();

$pageTitle = 'Pesanan berhasil';
require 'partials/header.php';
?>

<div class="mx-auto max-w-xl border border-stone-400 bg-white p-6">
    <h1 class="mb-2 text-2xl font-bold">
        Pesanan berhasil dibuat
    </h1>

    <p class="mb-5 text-sm text-stone-600">
        Nomor pesanan: #<?= $orderId ?>
    </p>

    <div class="space-y-3 border-y border-stone-300 py-4">
        <div class="flex justify-between gap-4">
            <span>Produk</span>
            <strong><?= e($product['name']) ?></strong>
        </div>

        <div class="flex justify-between">
            <span>Jumlah</span>
            <span><?= $quantity ?></span>
        </div>

        <div class="flex justify-between">
            <span>Harga satuan</span>
            <span><?= rupiah($unitPrice) ?></span>
        </div>

        <div class="flex justify-between">
            <span>Subtotal</span>
            <span><?= rupiah($subtotal) ?></span>
        </div>

        <div class="flex justify-between">
            <span>Diskon (<?= $discountRate * 100 ?>%)</span>
            <span>-<?= rupiah($discount) ?></span>
        </div>

        <div class="flex justify-between text-lg font-bold">
            <span>Total</span>
            <span><?= rupiah($total) ?></span>
        </div>
    </div>

    <p class="my-4 text-sm">
        <?php if ($loggedIn): ?>
            Diskon 10% diberikan karena kamu login.
        <?php else: ?>
            Kamu memesan tanpa login, jadi harga normal berlaku.
        <?php endif; ?>
    </p>

    <a
        href="index.php"
        class="inline-block border border-stone-900 bg-stone-900 px-4 py-2 text-white"
    >
        Kembali ke menu
    </a>

    <?php if ($loggedIn): ?>
        <a href="orders.php" class="ml-2 underline">
            Riwayat pesanan
        </a>
    <?php endif; ?>
</div>

<?php require 'partials/footer.php'; ?>
