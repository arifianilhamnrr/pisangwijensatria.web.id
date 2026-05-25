<?php
session_start();
if (!isset($_SESSION['id_user'])) { header("Location: login.php"); exit(); }
require_once 'koneksi.php';

$pesan = ""; $jenis = "";

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM produk WHERE id_produk = $id");
    $pesan = "Produk berhasil dihapus."; $jenis = "sukses";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = (int)($_POST['id_produk'] ?? 0);
    $nama        = mysqli_real_escape_string($koneksi, trim($_POST['nama_produk']));
    $harga       = mysqli_real_escape_string($koneksi, trim($_POST['harga_produk']));
    $stok        = (int)$_POST['stok_supp'];
    $stok_min    = (int)$_POST['stok_min'];
    $satuan      = mysqli_real_escape_string($koneksi, trim($_POST['satuan']));
    $id_kategori = (int)$_POST['id_kategori'];
    $id_supplier = (int)$_POST['id_supplier'];

    // Cek apakah kategori yang dipilih adalah Bahan Baku
    $cek_kat  = mysqli_query($koneksi, "SELECT nama_kategori FROM kategori WHERE id_kategori=$id_kategori");
    $data_kat = mysqli_fetch_assoc($cek_kat);
    $is_bahan_baku = stripos($data_kat['nama_kategori'] ?? '', 'Bahan Baku') !== false;

    // Kalau bahan baku, kode dikosongkan. Kalau bukan, ambil dari POST
    $kode = $is_bahan_baku ? '-' : mysqli_real_escape_string($koneksi, trim($_POST['kode_produk'] ?? '-'));

    if ($id > 0) {
        $sql = "UPDATE produk SET
                    nama_produk='$nama', harga_produk='$harga',
                    stok_supp=$stok, stok_min=$stok_min,
                    satuan='$satuan', kode_produk='$kode',
                    id_kategori=$id_kategori, id_supplier=$id_supplier
                WHERE id_produk=$id";
        mysqli_query($koneksi, $sql);
        $pesan = "Data produk berhasil diperbarui!";
    } else {
        $sql = "INSERT INTO produk
                    (id_kategori,id_supplier,kode_produk,stok_min,nama_produk,harga_produk,satuan,stok_supp)
                VALUES
                    ($id_kategori,$id_supplier,'$kode',$stok_min,'$nama','$harga','$satuan',$stok)";
        mysqli_query($koneksi, $sql);
        $pesan = "Produk baru berhasil ditambahkan!";
    }
    $jenis = "sukses";
}

$kategori_list = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori");
$supplier_list = mysqli_query($koneksi, "SELECT * FROM supplier ORDER BY nama_toko");

// Ambil semua id kategori Bahan Baku untuk dikirim ke JavaScript
$kb_query = mysqli_query($koneksi, "SELECT id_kategori FROM kategori WHERE nama_kategori LIKE '%Bahan Baku%'");
$id_bahan_baku = [];
while ($r = mysqli_fetch_assoc($kb_query)) $id_bahan_baku[] = (int)$r['id_kategori'];

$edit_data = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $res = mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk=$eid");
    $edit_data = mysqli_fetch_assoc($res);
}

$produk_list = mysqli_query($koneksi,
    "SELECT p.*, k.nama_kategori, s.nama_toko
     FROM produk p
     LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
     LEFT JOIN supplier s ON p.id_supplier = s.id_supplier
     ORDER BY p.id_produk ASC");

include 'header_nav.php';
?>

<div class="page-head">
  <div>
    <h1 class="page-title">Kelola Produk</h1>
    <p class="page-sub">Tambah, ubah nama, harga, dan stok barang</p>
  </div>
</div>

<?php if ($pesan): ?>
<div class="alert alert-<?= $jenis ?>"><?= htmlspecialchars($pesan) ?></div>
<?php endif; ?>

<div class="card mb">
  <div class="card-head">
    <span class="card-title"><?= $edit_data ? '✏️ Edit Produk' : '➕ Tambah Produk Baru' ?></span>
    <?php if ($edit_data): ?>
      <a href="kelola_produk.php" class="btn btn-abu">✕ Batal Edit</a>
    <?php endif; ?>
  </div>
  <form method="POST" action="kelola_produk.php" id="form-produk">
    <input type="hidden" name="id_produk" value="<?= $edit_data['id_produk'] ?? 0 ?>">
    <div class="form-grid">

      <!-- Nama Produk -->
      <div class="form-group">
        <label class="form-label">Nama Produk <span class="req">*</span></label>
        <input class="form-input" type="text" name="nama_produk" required
               placeholder="cth: Pisang Wijen Satria Original"
               value="<?= htmlspecialchars($edit_data['nama_produk'] ?? '') ?>">
      </div>

      <!-- Ukuran — disembunyikan otomatis jika Bahan Baku -->
      <div class="form-group" id="kolom-ukuran">
        <label class="form-label">Ukuran <span class="req">*</span></label>
        <select class="form-input" name="kode_produk" id="select-ukuran">
          <option value="">— Pilih Ukuran —</option>
          <option value="Small"  <?= ($edit_data['kode_produk'] ?? '') == 'Small'  ? 'selected' : '' ?>>Small</option>
          <option value="Large"  <?= ($edit_data['kode_produk'] ?? '') == 'Large'  ? 'selected' : '' ?>>Large</option>
        </select>
        <small class="hint">Tidak perlu diisi untuk produk Bahan Baku</small>
      </div>

      <!-- Harga -->
      <div class="form-group">
        <label class="form-label">Harga (Rp) <span class="req">*</span></label>
        <input class="form-input" type="number" name="harga_produk" required min="0"
               placeholder="cth: 10000"
               value="<?= htmlspecialchars($edit_data['harga_produk'] ?? '') ?>">
      </div>

      <!-- Satuan -->
      <div class="form-group">
        <label class="form-label">Satuan</label>
        <input class="form-input" type="text" name="satuan"
               placeholder="cth: pcs / kg / box"
               value="<?= htmlspecialchars($edit_data['satuan'] ?? '') ?>">
      </div>

      <!-- Stok -->
      <div class="form-group">
        <label class="form-label">Stok Saat Ini <span class="req">*</span></label>
        <input class="form-input" type="number" name="stok_supp" required min="0"
               value="<?= htmlspecialchars($edit_data['stok_supp'] ?? 0) ?>">
      </div>

      <!-- Stok Minimum -->
      <div class="form-group">
        <label class="form-label">Stok Minimum</label>
        <input class="form-input" type="number" name="stok_min" min="0"
               value="<?= htmlspecialchars($edit_data['stok_min'] ?? 5) ?>">
        <small class="hint">Akan ditandai ⚠ jika stok di bawah angka ini</small>
      </div>

      <!-- Kategori -->
      <div class="form-group">
        <label class="form-label">Kategori <span class="req">*</span></label>
        <select class="form-input" name="id_kategori" required
                id="pilih-kategori" onchange="cekKategoriDanSupplier(this.value)">
          <option value="">— Pilih Kategori —</option>
          <?php
          mysqli_data_seek($kategori_list, 0);
          while ($k = mysqli_fetch_assoc($kategori_list)):
          ?>
          <option value="<?= $k['id_kategori'] ?>"
            <?= ($edit_data['id_kategori'] ?? '') == $k['id_kategori'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($k['nama_kategori']) ?>
          </option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- Supplier -->
      <div class="form-group" id="kolom-supplier">
        <label class="form-label">Supplier <span class="req">*</span></label>
        <select class="form-input" name="id_supplier" required>
          <option value="">— Pilih Supplier —</option>
          <?php
          mysqli_data_seek($supplier_list, 0);
          while ($s = mysqli_fetch_assoc($supplier_list)):
          ?>
          <option value="<?= $s['id_supplier'] ?>"
            <?= ($edit_data['id_supplier'] ?? '') == $s['id_supplier'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($s['nama_toko']) ?>
          </option>
          <?php endwhile; ?>
        </select>
      </div>

    </div>
    <div style="margin-top:1rem">
      <button type="submit" class="btn btn-utama">
        <?= $edit_data ? '💾 Simpan Perubahan' : '➕ Tambah Produk' ?>
      </button>
    </div>
  </form>
</div>

<!-- TABEL DENGAN TAB FILTER -->
<div class="card">
  <div class="card-head">
    <span class="card-title">📦 Daftar Semua Produk</span>
    <span class="badge-count" id="jumlah-produk"><?= mysqli_num_rows($produk_list) ?> produk</span>
  </div>

  <!-- TOMBOL TAB FILTER -->
  <div style="display:flex;gap:8px;margin-bottom:1rem;flex-wrap:wrap">
    <button onclick="filterTab('semua')" id="tab-semua"
      style="padding:7px 18px;border-radius:20px;border:1.5px solid var(--kuning);
             background:linear-gradient(135deg,var(--kuning),var(--oranye));
             color:#fff;font-family:'DM Sans',sans-serif;font-size:.875rem;
             font-weight:600;cursor:pointer">
      Semua
    </button>
    <button onclick="filterTab('pisang')" id="tab-pisang"
      style="padding:7px 18px;border-radius:20px;border:1.5px solid var(--border);
             background:#fff;color:var(--coklat);font-family:'DM Sans',sans-serif;
             font-size:.875rem;font-weight:500;cursor:pointer">
      Pisang Wijen
    </button>
    <button onclick="filterTab('bahan')" id="tab-bahan"
      style="padding:7px 18px;border-radius:20px;border:1.5px solid var(--border);
             background:#fff;color:var(--coklat);font-family:'DM Sans',sans-serif;
             font-size:.875rem;font-weight:500;cursor:pointer">
      Bahan Baku
    </button>
  </div>

  <div class="table-wrap">
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Ukuran</th>
        <th>Nama Produk</th>
        <th>Kategori</th>
        <th>Harga</th>
        <th>Stok</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody id="tbody-produk">
      <?php $no=1; while ($p = mysqli_fetch_assoc($produk_list)):
        $jenis_tab = (stripos($p['nama_kategori'],'Bahan Baku') !== false) ? 'bahan' : 'pisang';
      ?>
      <tr data-tab="<?= $jenis_tab ?>">
        <td class="nomor"><?= $no++ ?></td>
        <td>
          <?php
          $kode = $p['kode_produk'];
          if ($kode && $kode !== '-') {
              $warna = $kode === 'Large' ? 'badge-b' : 'badge-h';
              echo "<span class='badge $warna'>$kode</span>";
          } else {
              echo '<span style="color:var(--muda);font-size:.8rem">—</span>';
          }
          ?>
        </td>
        <td><?= htmlspecialchars($p['nama_produk']) ?></td>
        <td>
          <span class="badge <?= $jenis_tab==='bahan' ? 'badge-abu' : 'badge-o' ?>">
            <?= htmlspecialchars($p['nama_kategori'] ?? '-') ?>
          </span>
        </td>
        <td>Rp <?= number_format((int)$p['harga_produk'],0,',','.') ?></td>
        <td><?= $p['stok_supp'] ?> <?= htmlspecialchars($p['satuan']) ?></td>
        <td>
          <?php if ($p['stok_supp'] == 0): ?>
            <span class="badge badge-w">&#10005; Habis</span>
          <?php elseif ($p['stok_supp'] <= $p['stok_min']): ?>
            <span class="badge badge-w">&#9888; Menipis</span>
          <?php else: ?>
            <span class="badge badge-h">&#10003; Normal</span>
          <?php endif; ?>
        </td>
        <td>
          <a href="kelola_produk.php?edit=<?= $p['id_produk'] ?>" class="btn btn-kecil btn-biru">Edit</a>
          <a href="kelola_produk.php?hapus=<?= $p['id_produk'] ?>"
             class="btn btn-kecil btn-merah"
             onclick="return confirm('Yakin hapus produk ini?')">Hapus</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  </div>
  <div id="pesan-kosong" style="display:none;text-align:center;padding:2rem;color:var(--muda)">
    Tidak ada produk di kategori ini.
  </div>
</div>

<!-- JAVASCRIPT: Sembunyikan kolom otomatis sesuai kategori -->
<script>
// ID kategori Bahan Baku dari database
const idBahanBaku  = <?= json_encode($id_bahan_baku) ?>;

// ID kategori Pisang Wijen dari database
<?php
$kpw = mysqli_query($koneksi, "SELECT id_kategori FROM kategori WHERE nama_kategori LIKE '%Pisang Wijen%'");
$id_pisang_wijen = [];
while ($r = mysqli_fetch_assoc($kpw)) $id_pisang_wijen[] = (int)$r['id_kategori'];
?>
const idPisangWijen = <?= json_encode($id_pisang_wijen) ?>;

function cekKategoriDanSupplier(idKategori) {
    const id = parseInt(idKategori);

    // ── Kolom UKURAN ──────────────────────────
    const kolomUkuran  = document.getElementById('kolom-ukuran');
    const selectUkuran = document.getElementById('select-ukuran');

    if (idBahanBaku.includes(id)) {
        // Bahan Baku → sembunyikan Ukuran
        kolomUkuran.style.display = 'none';
        selectUkuran.removeAttribute('required');
        selectUkuran.value = '';
    } else {
        // Bukan Bahan Baku → tampilkan Ukuran
        kolomUkuran.style.display = '';
        selectUkuran.setAttribute('required', 'required');
    }

    // ── Kolom SUPPLIER ────────────────────────
    const kolomSupplier  = document.getElementById('kolom-supplier');
    const selectSupplier = kolomSupplier.querySelector('select');

    if (idPisangWijen.includes(id)) {
        // Pisang Wijen → sembunyikan Supplier
        kolomSupplier.style.display = 'none';
        selectSupplier.removeAttribute('required');
        selectSupplier.value = '';
    } else {
        // Bukan Pisang Wijen → tampilkan Supplier
        kolomSupplier.style.display = '';
        selectSupplier.setAttribute('required', 'required');
    }
}

// ── Fungsi TAB FILTER tabel produk ───────────
function filterTab(tab) {
    const rows    = document.querySelectorAll('#tbody-produk tr');
    const tabs    = ['semua','pisang','bahan'];
    const aktif   = {
        background: 'linear-gradient(135deg,var(--kuning),var(--oranye))',
        color: '#fff', border: '1.5px solid var(--kuning)', fontWeight: '600'
    };
    const nonAktif = {
        background: '#fff', color: 'var(--coklat)',
        border: '1.5px solid var(--border)', fontWeight: '500'
    };

    // Reset semua tombol
    tabs.forEach(t => {
        const btn = document.getElementById('tab-' + t);
        if (btn) Object.assign(btn.style, nonAktif);
    });
    // Aktifkan tombol yang dipilih
    const btnAktif = document.getElementById('tab-' + tab);
    if (btnAktif) Object.assign(btnAktif.style, aktif);

    // Tampilkan / sembunyikan baris
    let tampil = 0, no = 1;
    rows.forEach(row => {
        const cocok = (tab === 'semua') || (row.dataset.tab === tab);
        row.style.display = cocok ? '' : 'none';
        if (cocok) {
            const tdNo = row.querySelector('.nomor');
            if (tdNo) tdNo.textContent = no++;
            tampil++;
        }
    });

    // Update counter & pesan kosong
    document.getElementById('jumlah-produk').textContent = tampil + ' produk';
    document.getElementById('pesan-kosong').style.display = tampil === 0 ? '' : 'none';
}

// Jalankan otomatis saat halaman dibuka (penting untuk mode Edit)
window.addEventListener('DOMContentLoaded', function() {
    const kategoriAwal = document.getElementById('pilih-kategori').value;
    cekKategoriDanSupplier(kategoriAwal);
    filterTab('semua'); // mulai dengan tab Semua
});
</script>

<?php include 'footer_nav.php'; ?>
