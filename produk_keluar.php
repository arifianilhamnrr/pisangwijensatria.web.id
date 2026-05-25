<?php
session_start();
if (!isset($_SESSION['id_user'])) { header("Location: login.php"); exit(); }
require_once 'koneksi.php';

$pesan = ""; $jenis = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_produk_masuk = (int)$_POST['id_produk_masuk'];
    $qty_kel         = (int)$_POST['qty_kel'];
    $tgl_keluar      = mysqli_real_escape_string($koneksi, $_POST['tgl_keluar']);
    $id_transaksi    = mysqli_real_escape_string($koneksi, date('Ymd') . strtoupper(substr(md5(uniqid()),0,8)));

    // Cek sisa stok di produk_masuk
    $cek = mysqli_query($koneksi, "SELECT sisa, id_produk FROM produk_masuk WHERE id_produk_masuk=$id_produk_masuk");
    $cd  = mysqli_fetch_assoc($cek);

    if (!$cd) {
        $pesan = "Data produk tidak ditemukan!"; $jenis = "error";
    } elseif ($qty_kel > $cd['sisa']) {
        $pesan = "Stok tidak cukup! Sisa hanya {$cd['sisa']} unit."; $jenis = "error";
    } elseif ($qty_kel <= 0) {
        $pesan = "Jumlah harus lebih dari 0!"; $jenis = "error";
    } else {
        $sisa_baru = $cd['sisa'] - $qty_kel;
        mysqli_query($koneksi,
            "INSERT INTO produk_keluar (id_produk_masuk, id_transaksi, qty_kel, tgl_keluar)
             VALUES ($id_produk_masuk, '$id_transaksi', $qty_kel, '$tgl_keluar')");
        mysqli_query($koneksi,
            "UPDATE produk_masuk SET sisa=$sisa_baru WHERE id_produk_masuk=$id_produk_masuk");
        mysqli_query($koneksi,
            "UPDATE produk SET stok_supp = stok_supp - $qty_kel WHERE id_produk = {$cd['id_produk']}");
        $pesan = "Produk keluar dicatat! Stok berkurang $qty_kel unit. ID: $id_transaksi";
        $jenis = "sukses";
    }
}

$masuk_ada = mysqli_query($koneksi,
    "SELECT pm.id_produk_masuk, p.nama_produk, pm.sisa, p.satuan
     FROM produk_masuk pm
     LEFT JOIN produk p ON pm.id_produk = p.id_produk
     WHERE pm.sisa > 0
     ORDER BY p.nama_produk ASC");

$keluar_list = mysqli_query($koneksi,
    "SELECT pk.*, p.nama_produk, p.satuan
     FROM produk_keluar pk
     LEFT JOIN produk_masuk pm ON pk.id_produk_masuk = pm.id_produk_masuk
     LEFT JOIN produk p ON pm.id_produk = p.id_produk
     ORDER BY pk.time DESC LIMIT 20");

include 'header_nav.php';
?>
<div class="page-head">
  <div>
    <h1 class="page-title">Produk Keluar</h1>
    <p class="page-sub">Catat barang yang keluar / terjual — stok otomatis berkurang</p>
  </div>
</div>

<?php if ($pesan): ?>
<div class="alert alert-<?= $jenis ?>"><?= htmlspecialchars($pesan) ?></div>
<?php endif; ?>

<div class="card mb">
  <div class="card-head"><span class="card-title">➖ Catat Produk Keluar</span></div>
  <form method="POST" action="produk_keluar.php">
    <div class="form-grid">
      <div class="form-group">
        <label class="form-label">Pilih Produk <span class="req">*</span></label>
        <select class="form-input" name="id_produk_masuk" required>
          <option value="">— Pilih Produk —</option>
          <?php while ($m = mysqli_fetch_assoc($masuk_ada)): ?>
          <option value="<?= $m['id_produk_masuk'] ?>">
            <?= htmlspecialchars($m['nama_produk']) ?> (Sisa: <?= $m['sisa'] ?> <?= $m['satuan'] ?>)
          </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Jumlah Keluar <span class="req">*</span></label>
        <input class="form-input" type="number" name="qty_kel" min="1" required placeholder="cth: 5">
      </div>
      <div class="form-group">
        <label class="form-label">Tanggal Keluar <span class="req">*</span></label>
        <input class="form-input" type="date" name="tgl_keluar" required value="<?= date('Y-m-d') ?>">
      </div>
    </div>
    <div style="margin-top:1rem">
      <button type="submit" class="btn btn-utama">💾 Simpan Produk Keluar</button>
    </div>
  </form>
</div>

<div class="card">
  <div class="card-head"><span class="card-title">📋 Riwayat Produk Keluar (20 Terakhir)</span></div>
  <div class="table-wrap">
  <table class="keluar-table">
    <thead><tr><th>#</th><th>Produk</th><th>Jumlah Keluar</th><th>Tgl Keluar</th><th>ID Transaksi</th><th>Waktu</th></tr></thead>
    <tbody>
      <?php $no=1; while ($k = mysqli_fetch_assoc($keluar_list)): ?>
      <tr>
        <td><?= $no++ ?></td>
        <td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($k['nama_produk'] ?? '-') ?></td>
        <td style="white-space:nowrap"><span class="badge badge-w">-<?= $k['qty_kel'] ?> <?= htmlspecialchars($k['satuan'] ?? '') ?></span></td>
        <td style="white-space:nowrap"><?= htmlspecialchars($k['tgl_keluar']) ?></td>
        <td style="white-space:nowrap"><code><?= htmlspecialchars($k['id_transaksi']) ?></code></td>
        <td style="font-size:.8rem;color:var(--muda);white-space:nowrap"><?= $k['time'] ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  </div>
</div>
<?php include 'footer_nav.php'; ?>