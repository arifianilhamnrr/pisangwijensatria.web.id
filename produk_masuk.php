<?php
session_start();
if (!isset($_SESSION['id_user'])) { header("Location: login.php"); exit(); }
require_once 'koneksi.php';

$pesan = ""; $jenis = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_produk    = (int)$_POST['id_produk'];
    $id_tran_supp = 0;
    $qty          = (int)$_POST['qty'];
    $tgl_masuk    = mysqli_real_escape_string($koneksi, $_POST['tgl_masuk']);

    if ($qty <= 0) {
        $pesan = "Jumlah harus lebih dari 0!"; $jenis = "error";
    } else {
        // Tambah ke produk_masuk
        mysqli_query($koneksi,
            "INSERT INTO produk_masuk (id_produk, id_tran_supp, qty, sisa, tgl_masuk)
             VALUES ($id_produk, $id_tran_supp, $qty, $qty, '$tgl_masuk')");
        // Update stok di tabel produk
        mysqli_query($koneksi,
            "UPDATE produk SET stok_supp = stok_supp + $qty WHERE id_produk = $id_produk");
        $pesan = "Produk masuk berhasil dicatat! Stok bertambah $qty unit.";
        $jenis = "sukses";
    }
}

$produk_list  = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY nama_produk ASC");
$masuk_list   = mysqli_query($koneksi,
    "SELECT pm.*, p.nama_produk, p.satuan
     FROM produk_masuk pm
     LEFT JOIN produk p ON pm.id_produk = p.id_produk
     ORDER BY pm.create_time DESC LIMIT 20");

include 'header_nav.php';
?>
<div class="page-head">
  <div>
    <h1 class="page-title">Produk Masuk</h1>
    <p class="page-sub">Catat barang yang baru diterima dari supplier — stok otomatis bertambah</p>
  </div>
</div>

<?php if ($pesan): ?>
<div class="alert alert-<?= $jenis ?>"><?= htmlspecialchars($pesan) ?></div>
<?php endif; ?>

<div class="card mb">
  <div class="card-head"><span class="card-title">➕ Catat Produk Masuk</span></div>
  <form method="POST" action="produk_masuk.php">
    <div class="form-grid">
      <div class="form-group">
        <label class="form-label">Pilih Produk <span class="req">*</span></label>
        <select class="form-input" name="id_produk" required>
          <option value="">— Pilih Produk —</option>
          <?php while ($p = mysqli_fetch_assoc($produk_list)): ?>
          <option value="<?= $p['id_produk'] ?>">
            <?= htmlspecialchars($p['nama_produk']) ?> (Stok: <?= $p['stok_supp'] ?> <?= $p['satuan'] ?>)
          </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Jumlah Masuk <span class="req">*</span></label>
        <input class="form-input" type="number" name="qty" min="1" required placeholder="cth: 50">
      </div>
      <div class="form-group">
        <label class="form-label">Tanggal Masuk <span class="req">*</span></label>
        <input class="form-input" type="date" name="tgl_masuk" required value="<?= date('Y-m-d') ?>"> 
    </div>
    <div style="margin-top:1rem">
      <button type="submit" class="btn btn-utama">💾 Simpan Produk Masuk</button>
    </div>
  </form>
</div>

<div class="card">
  <div class="card-head">
    <span class="card-title">📋 Riwayat Produk Masuk (20 Terakhir)</span>
  </div>
  <div class="table-wrap">
  <table>
    <thead><tr><th>#</th><th>Produk</th><th>Jumlah</th><th>Sisa</th><th>Tgl Masuk</th><th>Waktu Input</th></tr></thead>
    <tbody>
      <?php $no=1; while ($m = mysqli_fetch_assoc($masuk_list)): ?>
      <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($m['nama_produk'] ?? '-') ?></td>
        <td><span class="badge badge-h">+<?= $m['qty'] ?> <?= htmlspecialchars($m['satuan'] ?? '') ?></span></td>
        <td><?= $m['sisa'] ?></td>
        <td><?= htmlspecialchars($m['tgl_masuk'] ?? '-') ?></td>
        <td style="font-size:.8rem;color:var(--muda)"><?= $m['create_time'] ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  </div>
</div>
<?php include 'footer_nav.php'; ?>