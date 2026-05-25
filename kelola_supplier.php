<?php
// ============================================
// KELOLA_SUPPLIER.PHP
// Halaman ubah nama & info supplier
// ============================================
session_start();
if (!isset($_SESSION['id_user'])) { header("Location: login.php"); exit(); }
if ($_SESSION['level_user'] == 3) { header("Location: dashboard.php"); exit(); }
require_once 'koneksi.php';

$pesan = ""; $jenis = "";

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM supplier WHERE id_supplier=$id");
    $pesan = "Supplier berhasil dihapus."; $jenis = "sukses";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int)($_POST['id_supplier'] ?? 0);
    $nama   = mysqli_real_escape_string($koneksi, trim($_POST['nama_supplier']));
    $toko   = mysqli_real_escape_string($koneksi, trim($_POST['nama_toko']));
    $alamat = mysqli_real_escape_string($koneksi, trim($_POST['alamat']));
    $hp     = mysqli_real_escape_string($koneksi, trim($_POST['no_hp']));

    if ($id > 0) {
        if (empty($pass)) {
            $sql = "UPDATE supplier SET nama_supplier='$nama',nama_toko='$toko',
                        alamat='$alamat',no_hp='$hp'
                    WHERE id_supplier=$id";
        } else {
            $sql = "UPDATE supplier SET nama_supplier='$nama',nama_toko='$toko',
                        alamat='$alamat',no_hp='$hp'
                    WHERE id_supplier=$id";
        }
        $pesan = "Data supplier berhasil diperbarui!";
    } else {
        $sql = "INSERT INTO supplier (nama_supplier,nama_toko,alamat,no_hp)
                VALUES ('$nama','$toko','$alamat','$hp')";
        $pesan = "Supplier baru berhasil ditambahkan!";
    }
    mysqli_query($koneksi, $sql);
    $jenis = "sukses";
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $res = mysqli_query($koneksi, "SELECT * FROM supplier WHERE id_supplier=$eid");
    $edit_data = mysqli_fetch_assoc($res);
}

$supp_list = mysqli_query($koneksi, "SELECT * FROM supplier ORDER BY nama_toko ASC");
include 'header_nav.php';
?>

<div class="page-head">
  <div>
    <h1 class="page-title">Kelola Supplier</h1>
    <p class="page-sub">Tambah dan ubah data pemasok / supplier barang</p>
  </div>
</div>

<?php if ($pesan): ?>
<div class="alert alert-<?= $jenis ?>"><?= htmlspecialchars($pesan) ?></div>
<?php endif; ?>

<div class="card mb">
  <div class="card-head">
    <span class="card-title"><?= $edit_data ? '✏️ Edit Supplier' : '➕ Tambah Supplier' ?></span>
    <?php if ($edit_data): ?><a href="kelola_supplier.php" class="btn btn-abu">✕ Batal</a><?php endif; ?>
  </div>
  <form method="POST" action="kelola_supplier.php">
    <input type="hidden" name="id_supplier" value="<?= $edit_data['id_supplier'] ?? 0 ?>">
    <div class="form-grid">
      <div class="form-group">
        <label class="form-label">Nama Pemilik <span class="req">*</span></label>
        <input class="form-input" type="text" name="nama_supplier" required
               placeholder="cth: Bapak Suryo"
               value="<?= htmlspecialchars($edit_data['nama_supplier'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Nama Toko / Perusahaan <span class="req">*</span></label>
        <input class="form-input" type="text" name="nama_toko" required
               placeholder="cth: UD Pisang Makmur"
               value="<?= htmlspecialchars($edit_data['nama_toko'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">No. HP</label>
        <input class="form-input" type="text" name="no_hp"
               placeholder="cth: 081234567890"
               value="<?= htmlspecialchars($edit_data['no_hp'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Alamat</label>
        <input class="form-input" type="text" name="alamat"
               placeholder="cth: Malang, Jawa Timur"
               value="<?= htmlspecialchars($edit_data['alamat'] ?? '') ?>">
    </div>
    <div style="margin-top:1rem">
      <button type="submit" class="btn btn-utama">
        <?= $edit_data ? '💾 Simpan Perubahan' : '➕ Tambah Supplier' ?>
      </button>
    </div>
  </form>
</div>

<div class="card">
  <div class="card-head">
    <span class="card-title">🚚 Daftar Supplier</span>
    <span class="badge-count"><?= mysqli_num_rows($supp_list) ?> supplier</span>
  </div>
  <div class="table-wrap">
  <table>
    <thead>
      <tr><th>#</th><th>Nama Pemilik</th><th>Nama Toko</th><th>No. HP</th><th>Alamat</th><th>Aksi</th></tr>
    </thead>
    <tbody>
      <?php $no=1; while ($s = mysqli_fetch_assoc($supp_list)): ?>
      <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($s['nama_supplier']) ?></td>
        <td><strong><?= htmlspecialchars($s['nama_toko']) ?></strong></td>
        <td><?= htmlspecialchars($s['no_hp']) ?></td>
        <td><?= htmlspecialchars($s['alamat']) ?></td>
        <td>
          <a href="kelola_supplier.php?edit=<?= $s['id_supplier'] ?>" class="btn btn-kecil btn-biru">✏ Edit</a>
          <a href="kelola_supplier.php?hapus=<?= $s['id_supplier'] ?>"
             class="btn btn-kecil btn-merah"
             onclick="return confirm('Yakin hapus supplier ini?')">🗑 Hapus</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  </div>
</div>

<?php include 'footer_nav.php'; ?>