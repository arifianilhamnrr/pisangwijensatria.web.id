<?php
session_start();
if (!isset($_SESSION['id_user'])) { header("Location: login.php"); exit(); }
if ($_SESSION['level_user'] == 3) { header("Location: dashboard.php"); exit(); }
require_once 'koneksi.php';

$pesan = ""; $jenis = "";

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $cek = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM produk WHERE id_kategori=$id");
    $cd  = mysqli_fetch_assoc($cek);
    if ($cd['jml'] > 0) {
        $pesan = "Tidak bisa hapus! Kategori ini masih dipakai oleh {$cd['jml']} produk.";
        $jenis = "error";
    } else {
        mysqli_query($koneksi, "DELETE FROM kategori WHERE id_kategori=$id");
        $pesan = "Kategori berhasil dihapus."; $jenis = "sukses";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id   = (int)($_POST['id_kategori'] ?? 0);
    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama_kategori']));
    if (empty($nama)) { $pesan = "Nama kategori tidak boleh kosong!"; $jenis = "error"; }
    else {
        if ($id > 0) {
            mysqli_query($koneksi, "UPDATE kategori SET nama_kategori='$nama' WHERE id_kategori=$id");
            $pesan = "Kategori berhasil diperbarui!";
        } else {
            mysqli_query($koneksi, "INSERT INTO kategori (nama_kategori) VALUES ('$nama')");
            $pesan = "Kategori baru berhasil ditambahkan!";
        }
        $jenis = "sukses";
    }
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $res = mysqli_query($koneksi, "SELECT * FROM kategori WHERE id_kategori=$eid");
    $edit_data = mysqli_fetch_assoc($res);
}

$kat_list = mysqli_query($koneksi,
    "SELECT k.*, COUNT(p.id_produk) as jml_produk
     FROM kategori k
     LEFT JOIN produk p ON k.id_kategori = p.id_kategori
     GROUP BY k.id_kategori
     ORDER BY k.nama_kategori ASC");

include 'header_nav.php';
?>

<div class="page-head">
  <div>
    <h1 class="page-title">Kelola Kategori</h1>
    <p class="page-sub">Tambah dan ubah kategori produk</p>
  </div>
</div>

<?php if ($pesan): ?>
<div class="alert alert-<?= $jenis ?>"><?= htmlspecialchars($pesan) ?></div>
<?php endif; ?>

<div class="card mb" style="max-width:480px">
  <div class="card-head">
    <span class="card-title"><?= $edit_data ? '✏️ Edit Kategori' : '➕ Tambah Kategori' ?></span>
    <?php if ($edit_data): ?><a href="kelola_kategori.php" class="btn btn-abu">✕ Batal</a><?php endif; ?>
  </div>
  <form method="POST" action="kelola_kategori.php" style="display:flex;gap:10px;align-items:flex-end">
    <input type="hidden" name="id_kategori" value="<?= $edit_data['id_kategori'] ?? 0 ?>">
    <div class="form-group" style="flex:1;margin:0">
      <label class="form-label">Nama Kategori <span class="req">*</span></label>
      <input class="form-input" type="text" name="nama_kategori" required
             placeholder="cth: Pisang Wijen Coklat"
             value="<?= htmlspecialchars($edit_data['nama_kategori'] ?? '') ?>">
    </div>
    <button type="submit" class="btn btn-utama" style="white-space:nowrap">
      <?= $edit_data ? '💾 Simpan' : '➕ Tambah' ?>
    </button>
  </form>
</div>

<div class="card">
  <div class="card-head">
    <span class="card-title">📂 Daftar Kategori</span>
    <span class="badge-count"><?= mysqli_num_rows($kat_list) ?> kategori</span>
  </div>
  <div class="table-wrap">
  <table>
    <thead><tr><th>#</th><th>Nama Kategori</th><th>Jumlah Produk</th><th>Aksi</th></tr></thead>
    <tbody>
      <?php $no=1; while ($k = mysqli_fetch_assoc($kat_list)): ?>
      <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($k['nama_kategori']) ?></td>
        <td><span class="badge badge-o"><?= $k['jml_produk'] ?> produk</span></td>
        <td>
          <a href="kelola_kategori.php?edit=<?= $k['id_kategori'] ?>" class="btn btn-kecil btn-biru">✏ Edit</a>
          <?php if ($k['jml_produk'] == 0): ?>
          <a href="kelola_kategori.php?hapus=<?= $k['id_kategori'] ?>"
             class="btn btn-kecil btn-merah"
             onclick="return confirm('Hapus kategori ini?')">🗑 Hapus</a>
          <?php else: ?>
          <span class="btn btn-kecil" style="opacity:.4;cursor:default">🔒 Dipakai</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  </div>
</div>

<?php include 'footer_nav.php'; ?>