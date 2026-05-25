<?php
session_start();
if (!isset($_SESSION['id_user'])) { header("Location: login.php"); exit(); }
require_once 'koneksi.php';

$nama_user  = $_SESSION['nama_user'];
$level_user = $_SESSION['level_user'];

$q_produk = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM produk");
$ttl_produk = mysqli_fetch_assoc($q_produk)['total'];

$q_kat = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kategori");
$ttl_kat = mysqli_fetch_assoc($q_kat)['total'];

$q_supp = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM supplier");
$ttl_supp = mysqli_fetch_assoc($q_supp)['total'];

$q_trx   = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM produk_keluar");
$ttl_trx = mysqli_fetch_assoc($q_trx)['total'];


// Omzet dari produk keluar x harga produk
$q_omzet   = mysqli_query($koneksi,
    "SELECT SUM(CAST(pk.qty_kel AS UNSIGNED) * CAST(p.harga_produk AS UNSIGNED)) as omzet
     FROM produk_keluar pk
     LEFT JOIN produk_masuk pm ON pk.id_produk_masuk = pm.id_produk_masuk
     LEFT JOIN produk p ON pm.id_produk = p.id_produk");
$ttl_omzet = mysqli_fetch_assoc($q_omzet)['omzet'] ?? 0;

// Produk stok menipis
$q_menipis = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM produk WHERE stok_supp <= stok_min");
$ttl_menipis = mysqli_fetch_assoc($q_menipis)['total'];

$q_list = mysqli_query($koneksi,
    "SELECT p.*, k.nama_kategori FROM produk p
     LEFT JOIN kategori k ON p.id_kategori=k.id_kategori
     WHERE k.nama_kategori NOT LIKE '%Bahan Baku%'
     ORDER BY p.id_produk DESC LIMIT 5");

$q_trx_list = mysqli_query($koneksi,
    "SELECT pk.*, p.nama_produk, p.harga_produk, p.satuan,
            CAST(pk.qty_kel AS UNSIGNED) * CAST(p.harga_produk AS UNSIGNED) AS total
     FROM produk_keluar pk
     LEFT JOIN produk_masuk pm ON pk.id_produk_masuk = pm.id_produk_masuk
     LEFT JOIN produk p ON pm.id_produk = p.id_produk
     LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
     WHERE k.nama_kategori NOT LIKE '%Bahan Baku%'
     ORDER BY pk.time DESC LIMIT 5");

$hari_map  = ['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu',
              'Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu'];
$bulan_map = ['January'=>'Januari','February'=>'Februari','March'=>'Maret','April'=>'April',
              'May'=>'Mei','June'=>'Juni','July'=>'Juli','August'=>'Agustus',
              'September'=>'September','October'=>'Oktober','November'=>'November','December'=>'Desember'];
$tgl_tampil = ($hari_map[date('l')]??date('l')) . date(', d ') . ($bulan_map[date('F')]??date('F')) . date(' Y');

include 'header_nav.php';
?>

<div class="topbar" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.75rem;flex-wrap:wrap;gap:10px">
  <div>
    <h1 style="font-family:'Playfair Display',serif;font-size:1.65rem;color:var(--coklat)">Dashboard</h1>
    <p style="font-size:.85rem;color:var(--muda);margin-top:2px">Halo, <?= htmlspecialchars($nama_user) ?>! Selamat datang kembali.</p>
  </div>
  <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
    <div style="background:#fff;border:1px solid var(--border);padding:5px 13px;border-radius:20px;font-size:.78rem;color:var(--muda)">
      📅 <?= $tgl_tampil ?>
    </div>
    <div style="background:linear-gradient(135deg,var(--kuning),var(--oranye));color:#fff;padding:5px 14px;border-radius:20px;font-size:.78rem;font-weight:600">
      🍌 Pisang Wijen Satria
    </div>
  </div>
</div>

<!-- STATISTIK -->
<div class="stats-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem">
  <div style="background:linear-gradient(135deg,#F9A825,#E65100);border-radius:14px;padding:1.1rem 1.25rem;position:relative;overflow:hidden;border:1px solid transparent">
    <span style="position:absolute;right:12px;top:12px;font-size:2rem;opacity:.2">🍌</span>
    <div style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:rgba(255,255,255,.8);margin-bottom:.4rem">Total Produk</div>
    <div style="font-family:'Playfair Display',serif;font-size:1.75rem;font-weight:700;color:#fff"><?= $ttl_produk ?></div>
    <div style="font-size:.75rem;color:rgba(255,255,255,.75)"><?= $ttl_kat ?> kategori aktif</div>
  </div>
  <div style="background:#fff;border-radius:14px;padding:1.1rem 1.25rem;border:1px solid var(--border);position:relative;overflow:hidden">
    <span style="position:absolute;right:12px;top:12px;font-size:2rem;opacity:.12">💰</span>
    <div style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:var(--muda);margin-bottom:.4rem">Total Omzet</div>
    <div style="font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:700;color:var(--coklat)">Rp <?= number_format($ttl_omzet,0,',','.') ?></div>
    <div style="font-size:.75rem;color:var(--muda)">Dari <?= $ttl_trx ?> produk keluar</div>
  </div>
  <div style="background:#fff;border-radius:14px;padding:1.1rem 1.25rem;border:1px solid var(--border);position:relative;overflow:hidden">
    <span style="position:absolute;right:12px;top:12px;font-size:2rem;opacity:.12">🚚</span>
    <div style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:var(--muda);margin-bottom:.4rem">Supplier</div>
    <div style="font-family:'Playfair Display',serif;font-size:1.75rem;font-weight:700;color:var(--coklat)"><?= $ttl_supp ?></div>
    <div style="font-size:.75rem;color:var(--muda)">Supplier aktif</div>
  </div>
  <div style="background:#fff;border-radius:14px;padding:1.1rem 1.25rem;border:1px solid var(--border);position:relative;overflow:hidden">
    <span style="position:absolute;right:12px;top:12px;font-size:2rem;opacity:.12">⚠</span>
    <div style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:var(--muda);margin-bottom:.4rem">Stok Menipis</div>
    <div style="font-family:'Playfair Display',serif;font-size:1.75rem;font-weight:700;color:<?= $ttl_menipis>0?'#E65100':'var(--coklat)' ?>"><?= $ttl_menipis ?></div>
    <div style="font-size:.75rem;color:var(--muda)"><?= $ttl_menipis>0?'Perlu restock segera':'Semua stok aman' ?></div>
  </div>
</div>

<!-- SHORTCUT MENU -->
<div class="shortcut-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:.75rem;margin-bottom:1.5rem">
  <?php
  $menus = [
    ['href'=>'kelola_produk.php',    'ico'=>'📦', 'label'=>'Kelola Produk',   'sub'=>'Tambah & ubah barang'],
    ['href'=>'produk_masuk.php',     'ico'=>'↓',  'label'=>'Produk Masuk',    'sub'=>'Catat stok masuk'],
    ['href'=>'produk_keluar.php',    'ico'=>'↑',  'label'=>'Produk Keluar',   'sub'=>'Catat stok keluar'],
    ['href'=>'riwayat_transaksi.php','ico'=>'🧾', 'label'=>'Riwayat',         'sub'=>'Lihat semua transaksi'],
  ];
  foreach ($menus as $m): ?>
  <a href="<?= $m['href'] ?>" style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:1rem;text-decoration:none;transition:all .18s;display:block" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(93,58,10,.1)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
    <div style="font-size:1.5rem;margin-bottom:.4rem"><?= $m['ico'] ?></div>
    <div style="font-weight:600;font-size:.875rem;color:var(--coklat)"><?= $m['label'] ?></div>
    <div style="font-size:.75rem;color:var(--muda);margin-top:2px"><?= $m['sub'] ?></div>
  </a>
  <?php endforeach; ?>
</div>

<!-- TABEL PRODUK & TRANSAKSI -->
<div class="content-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem">
  <div class="card">
    <div class="card-head">
      <span class="card-title">Produk Terbaru</span>
      <a href="kelola_produk.php" style="font-size:.8rem;color:var(--oranye);text-decoration:none">Lihat Semua →</a>
    </div>
    <div class="table-wrap">
    <table class="dashboard-table">
      <thead><tr><th>Nama Produk</th><th>Kategori</th><th>Harga</th><th>Stok</th></tr></thead>
      <tbody>
        <?php while ($p = mysqli_fetch_assoc($q_list)): ?>
        <tr>
          <td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($p['nama_produk']) ?></td>
          <td><span class="badge badge-o"><?= htmlspecialchars($p['nama_kategori']??'-') ?></span></td>
          <td style="white-space:nowrap">Rp <?= number_format((int)$p['harga_produk'],0,',','.') ?></td>
          <td>
            <?php if ($p['stok_supp'] <= $p['stok_min']): ?>
              <span class="badge badge-w">⚠ <?= $p['stok_supp'] ?></span>
            <?php else: ?>
              <span class="badge badge-h"><?= $p['stok_supp'] ?></span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    </div>
  </div>

  <div class="card">
    <div class="card-head">
      <span class="card-title">Transaksi Terbaru</span>
      <a href="riwayat_transaksi.php" style="font-size:.8rem;color:var(--oranye);text-decoration:none">Lihat Semua →</a>
    </div>
    <div class="transaction-list">
    <?php while ($t = mysqli_fetch_assoc($q_trx_list)): ?>
    <div style="display:flex;align-items:center;gap:11px;padding:.65rem 0;border-bottom:1px solid #FAF4E8">
      <div style="width:34px;height:34px;border-radius:9px;background:var(--krem);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:.95rem;flex-shrink:0">🧾</div>
      <div style="flex:1;min-width:0">
        <div style="font-size:.875rem;font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($t['nama_produk']??'-') ?></div>
        <div style="font-size:.75rem;color:var(--muda);overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= $t['tgl_keluar'] ?> · <?= $t['qty_kel'] ?> <?= htmlspecialchars($t['satuan']??'') ?></div>
      </div>
      <div style="font-size:.875rem;font-weight:600;color:var(--coklat);white-space:nowrap;flex-shrink:0">
        Rp <?= number_format((int)$t['total'],0,',','.') ?>
      </div>
    </div>
    <?php endwhile; ?>
    </div>
  </div>
</div>

<?php include 'footer_nav.php'; ?>