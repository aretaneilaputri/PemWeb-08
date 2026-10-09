<?php
require __DIR__ . '/includes/koneksi.php';

$json = file_get_contents(__DIR__ . '/data/buku.json');
$data = json_decode($json, true);

if (!is_array($data)) {
    die("Gagal membaca data/buku.json");
}

$stmt = $pdo->prepare(
    "INSERT INTO buku (judul, pengarang, tahun, isbn, stok, kategori)
     VALUES (:judul, :pengarang, :tahun, :isbn, :stok, :kategori)"
);

$jumlah = 0;
$pdo->beginTransaction();
try {
    foreach ($data as $b) {
        $stmt->execute([
            'judul'     => $b['judul'],
            'pengarang' => $b['pengarang'],
            'tahun'     => (int) $b['tahun'],
            'isbn'      => $b['isbn'] ?? null,
            'stok'      => (int) ($b['stok'] ?? 0),
            'kategori'  => $b['kategori'] ?? null,
        ]);
        $jumlah++;
    }
    $pdo->commit();
    echo "Berhasil memigrasi $jumlah buku.";
} catch (PDOException $e) {
    $pdo->rollBack();
    echo "Migrasi gagal, semua perubahan dibatalkan: " . $e->getMessage();
}