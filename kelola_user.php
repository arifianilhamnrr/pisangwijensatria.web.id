<?php
// ============================================
// KELOLA_USER.PHP
// Halaman ubah nama pemilik, admin, kasir
// ============================================
session_start();
if (!isset($_SESSION['id_user'])) { header("Location: login.php"); exit(); }
// Hanya pemilik (level 2) yang boleh kelola user
if ($_SESSION['level_user'] != 2) {
    header("Location: dashboard.php");
    exit();
}
require_once 'koneksi.php';

$pesan = ""; $jenis = "";

// ── HAPUS USER ────────────────────────────────
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    if ($id == $_SESSION['id_user']) {
        $pesan = "Tidak bisa menghapus akun yang sedang dipakai!";
        $jenis = "error";
    } else {
        mysqli_query($koneksi, "DELETE FROM user WHERE id_user=$id");
        $pesan = "Akun berhasil dihapus.";
        $jenis = "sukses";
    }
}

// ── SIMPAN TAMBAH / EDIT ──────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = (int)($_POST['id_user'] ?? 0);
    $nama     = mysqli_real_escape_string($koneksi, trim($_POST['nama_user']));
    $alamat   = mysqli_real_escape_string($koneksi, trim($_POST['alamat']));
    $no_hp    = mysqli_real_escape_string($koneksi, trim($_POST['no_hp']));
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $password = mysqli_real_escape_string($koneksi, trim($_POST['password']));
    $level    = (int)$_POST['level_user'];

    // Cek username sudah ada atau belum (kecuali diri sendiri saat edit)
    $cek_sql = "SELECT id_user FROM user WHERE username='$username'";
    if ($id > 0) $cek_sql .= " AND id_user != $id";
    $cek = mysqli_query($koneksi, $cek_sql);
    if (mysqli_num_rows($cek) > 0) {
        $pesan = "Username '$username' sudah dipakai! Gunakan username lain.";
        $jenis = "error";
    } else {
        if ($id > 0) {
            // Edit — kalau password dikosongkan, tidak diubah
            if (!empty($password)) {
                $sql = "UPDATE user SET nama_user='$nama', alamat='$alamat', no_hp='$no_hp',
                            username='$username', password='$password', level_user=$level
                        WHERE id_user=$id";
            } else {
                $sql = "UPDATE user SET nama_user='$nama', alamat='$alamat', no_hp='$no_hp',
                            username='$username', level_user=$level
                        WHERE id_user=$id";
            }
            mysqli_query($koneksi, $sql);
            $pesan = "Data akun berhasil diperbarui!";
        } else {
            if (empty($password)) { $pesan = "Password wajib diisi untuk akun baru!"; $jenis = "error"; }
            else {
                $sql = "INSERT INTO user (nama_user,alamat,no_hp,username,password,level_user)
                        VALUES ('$nama','$alamat','$no_hp','$username','$password',$level)";
                mysqli_query($koneksi, $sql);
                $pesan = "Akun baru berhasil ditambahkan!";
            }
        }
        if (empty($jenis)) $jenis = "sukses";
    }
}

// ── DATA EDIT ─────────────────────────────────
$edit_data = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $res = mysqli_query($koneksi, "SELECT * FROM user WHERE id_user=$eid");
    $edit_data = mysqli_fetch_assoc($res);
}

$user_list = mysqli_query($koneksi, "SELECT * FROM user ORDER BY level_user ASC, nama_user ASC");
$level_map = [1 => 'Admin', 2 => 'Pemilik', 3 => 'Kasir'];

include 'header_nav.php';
?>

<div class="page-head">
  <div>
    <h1 class="page-title">Kelola Akun Pengguna</h1>
    <p class="page-sub">Ubah nama pemilik, admin, kasir — atau tambah akun baru</p>
  </div>
</div>

<?php if ($pesan): ?>
<div class="alert alert-<?= $jenis ?>"><?= htmlspecialchars($pesan) ?></div>
<?php endif; ?>

<!-- FORM -->
<div class="card mb">
  <div class="card-head">
    <span class="card-title"><?= $edit_data ? '✏️ Edit Akun' : '➕ Tambah Akun Baru' ?></span>
    <?php if ($edit_data): ?>
      <a href="kelola_user.php" class="btn btn-abu">✕ Batal</a>
    <?php endif; ?>
  </div>
  <form method="POST" action="kelola_user.php">
    <input type="hidden" name="id_user" value="<?= $edit_data['id_user'] ?? 0 ?>">
    <div class="form-grid">
      <div class="form-group">
        <label class="form-label">Nama Lengkap <span class="req">*</span></label>
        <input class="form-input" type="text" name="nama_user" required
               placeholder="cth: Satria Nugraha"
               value="<?= htmlspecialchars($edit_data['nama_user'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Username <span class="req">*</span></label>
        <input class="form-input" type="text" name="username" required
               placeholder="cth: satria123"
               value="<?= htmlspecialchars($edit_data['username'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">
          Password <?= $edit_data ? '<span class="hint">(kosongkan jika tidak ingin diubah)</span>' : '<span class="req">*</span>' ?>
        </label>
        <input class="form-input" type="password" name="password"
               placeholder="<?= $edit_data ? 'Isi hanya jika ingin mengubah password' : 'Buat password baru' ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Level / Jabatan <span class="req">*</span></label>
        <select class="form-input" name="level_user" required>
          <option value="">— Pilih Level —</option>
          <option value="2" <?= ($edit_data['level_user'] ?? '') == 2 ? 'selected' : '' ?>>Pemilik (akses penuh)</option>
        </select>
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
    </div>
    <div style="margin-top:1rem">
      <button type="submit" class="btn btn-utama">
        <?= $edit_data ? '💾 Simpan Perubahan' : '➕ Tambah Akun' ?>
      </button>
    </div>
  </form>
</div>

<!-- TABEL USER -->
<div class="card">
  <div class="card-head">
    <span class="card-title">👥 Daftar Semua Akun</span>
    <span class="badge-count"><?= mysqli_num_rows($user_list) ?> akun</span>
  </div>
  <div class="table-wrap">
  <table>
    <thead>
      <tr><th>#</th><th>Nama</th><th>Username</th><th>Level</th><th>No. HP</th><th>Alamat</th><th>Aksi</th></tr>
    </thead>
    <tbody>
      <?php $no=1; mysqli_data_seek($user_list,0); while ($u = mysqli_fetch_assoc($user_list)): ?>
      <tr <?= $u['id_user']==$_SESSION['id_user']?'style="background:#FFFDE7"':'' ?>>
        <td><?= $no++ ?></td>
        <td>
          <?= htmlspecialchars($u['nama_user']) ?>
          <?php if ($u['id_user']==$_SESSION['id_user']): ?>
            <span class="badge badge-o" style="font-size:.65rem">Anda</span>
          <?php endif; ?>
        </td>
        <td><code><?= htmlspecialchars($u['username']) ?></code></td>
        <td>
          <?php
          $lv = $u['level_user'];
          $cls = $lv==2 ? 'badge-h' : ($lv==1 ? 'badge-o' : 'badge-abu');
          ?>
          <span class="badge <?= $cls ?>"><?= $level_map[$lv] ?? '-' ?></span>
        </td>
        <td><?= htmlspecialchars($u['no_hp']) ?></td>
        <td><?= htmlspecialchars($u['alamat']) ?></td>
        <td>
          <a href="kelola_user.php?edit=<?= $u['id_user'] ?>" class="btn btn-kecil btn-biru">✏ Edit</a>
          <?php if ($u['id_user'] != $_SESSION['id_user']): ?>
          <a href="kelola_user.php?hapus=<?= $u['id_user'] ?>"
             class="btn btn-kecil btn-merah"
             onclick="return confirm('Yakin hapus akun <?= htmlspecialchars($u['nama_user']) ?>?')">🗑 Hapus</a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  </div>
</div>

<?php include 'footer_nav.php'; ?>