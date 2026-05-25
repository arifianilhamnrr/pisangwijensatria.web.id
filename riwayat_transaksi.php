bash


<?php
session_start();
if (!isset($_SESSION['id_user'])) { header("Location: login.php"); exit(); }
require_once 'koneksi.php';

// ── FILTER ────────────────────────────────────
$filter_tgl  = isset($_GET['tgl'])  ? mysqli_real_escape_string($koneksi, $_GET['tgl'])  : '';
$filter_jenis= isset($_GET['jenis'])? mysqli_real_escape_string($koneksi, $_GET['jenis']): '';

// ── QUERY PRODUK MASUK ────────────────────────
$where_masuk = "WHERE 1=1";
if ($filter_tgl)   $where_masuk .= " AND pm.tgl_masuk = '$filter_tgl'";
if ($filter_jenis && $filter_jenis !== 'masuk') $where_masuk .= " AND 1=0";

$q_masuk = mysqli_query($koneksi,
    "SELECT
        pm.id_produk_masuk AS id_ref,
        'masuk'            AS jenis,
        pm.tgl_masuk       AS tanggal,
        p.nama_produk,
        pm.qty             AS jumlah,
        p.satuan,
        p.harga_produk     AS harga_satuan,
        (CAST(pm.qty AS UNSIGNED) * CAST(p.harga_produk AS UNSIGNED)) AS total,
        s.nama_toko        AS keterangan,
        pm.create_time     AS waktu
     FROM produk_masuk pm
     LEFT JOIN produk  p ON pm.id_produk   = p.id_produk
     LEFT JOIN transaksi_supp ts ON pm.id_tran_supp = ts.id_tran_supp
     LEFT JOIN supplier s ON ts.id_supplier = s.id_supplier
     $where_masuk
     ORDER BY pm.create_time DESC LIMIT 100");

// ── QUERY PRODUK KELUAR ───────────────────────
$where_keluar = "WHERE 1=1";
if ($filter_tgl)   $where_keluar .= " AND pk.tgl_keluar = '$filter_tgl'";
if ($filter_jenis && $filter_jenis !== 'keluar') $where_keluar .= " AND 1=0";

$q_keluar = mysqli_query($koneksi,
    "SELECT
        pk.id_produk_keluar AS id_ref,
        'keluar'            AS jenis,
        pk.tgl_keluar       AS tanggal,
        p.nama_produk,
        pk.qty_kel          AS jumlah,
        p.satuan,
        p.harga_produk      AS harga_satuan,
        (CAST(pk.qty_kel AS UNSIGNED) * CAST(p.harga_produk AS UNSIGNED)) AS total,
        pk.id_transaksi     AS keterangan,
        pk.time             AS waktu
     FROM produk_keluar pk
     LEFT JOIN produk_masuk pm ON pk.id_produk_masuk = pm.id_produk_masuk
     LEFT JOIN produk p ON pm.id_produk = p.id_produk
     LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
     $where_keluar
     AND (k.nama_kategori != 'Bahan Baku' OR k.nama_kategori IS NULL)
     ORDER BY pk.time DESC LIMIT 100");

// ── GABUNG & URUTKAN ──────────────────────────
$semua = [];

while ($r = mysqli_fetch_assoc($q_masuk))  $semua[] = $r;
while ($r = mysqli_fetch_assoc($q_keluar)) $semua[] = $r;

// Urutkan berdasarkan waktu terbaru
usort($semua, fn($a,$b) => strtotime($b['waktu']) - strtotime($a['waktu']));

// ── HITUNG RINGKASAN ──────────────────────────
$total_masuk  = 0; $jml_masuk  = 0;
$total_keluar = 0; $jml_keluar = 0;
foreach ($semua as $r) {
    if ($r['jenis'] === 'masuk')  { $total_masuk  += $r['total']; $jml_masuk++;  }
    if ($r['jenis'] === 'keluar') { $total_keluar += $r['total']; $jml_keluar++; }
}

include 'header_nav.php';
?>

<div class="page-head">
  <div>
    <h1 class="page-title">Riwayat Transaksi</h1>
    <p class="page-sub">Semua aktivitas produk masuk dan produk keluar</p>
  </div>
</div>

<!-- RINGKASAN -->
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.25rem">
  <div style="background:#E8F5E9;border:1px solid #C8E6C9;border-radius:12px;padding:1rem 1.25rem">
    <div style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#2E7D32;margin-bottom:.3rem">Total Produk Masuk</div>
    <div style="font-size:1.4rem;font-weight:700;color:#1B5E20">Rp <?= number_format($total_masuk,0,',','.') ?></div>
    <div style="font-size:.75rem;color:#388E3C;margin-top:2px"><?= $jml_masuk ?> transaksi masuk</div>
  </div>
  <div style="background:#FFEBEE;border:1px solid #FFCDD2;border-radius:12px;padding:1rem 1.25rem">
    <div style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#C62828;margin-bottom:.3rem">Total Produk Keluar</div>
    <div style="font-size:1.4rem;font-weight:700;color:#B71C1C">Rp <?= number_format($total_keluar,0,',','.') ?></div>
    <div style="font-size:.75rem;color:#E53935;margin-top:2px"><?= $jml_keluar ?> transaksi keluar</div>
  </div>
  <div style="background:var(--krem);border:1px solid var(--border);border-radius:12px;padding:1rem 1.25rem">
    <div style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:var(--muda);margin-bottom:.3rem">Selisih (Keluar − Masuk)</div>
    <?php $selisih = $total_keluar - $total_masuk; ?>
    <div style="font-size:1.4rem;font-weight:700;color:<?= $selisih >= 0 ? '#1B5E20' : '#B71C1C' ?>">
      Rp <?= number_format(abs($selisih),0,',','.') ?>
    </div>
    <div style="font-size:.75rem;color:var(--muda);margin-top:2px">
      <?= $selisih >= 0 ? '▲ Lebih banyak keluar' : '▼ Lebih banyak masuk' ?>
    </div>
  </div>
</div>

<!-- FILTER -->
<div class="card mb">
  <form method="GET" action="riwayat_transaksi.php"
        style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap">
    <div class="form-group" style="margin:0">
      <label class="form-label">Filter Tanggal</label>
      <input class="form-input" type="date" name="tgl"
             value="<?= htmlspecialchars($filter_tgl) ?>" style="width:180px">
    </div>
    <div class="form-group" style="margin:0">
      <label class="form-label">Jenis Transaksi</label>
      <select class="form-input" name="jenis" style="width:170px">
        <option value=""    <?= $filter_jenis==''      ?'selected':'' ?>>Semua (Masuk + Keluar)</option>
        <option value="masuk"  <?= $filter_jenis=='masuk' ?'selected':'' ?>>📥 Produk Masuk saja</option>
        <option value="keluar" <?= $filter_jenis=='keluar'?'selected':'' ?>>📤 Produk Keluar saja</option>
      </select>
    </div>
    <button type="submit" class="btn btn-utama">🔍 Filter</button>
    <?php if ($filter_tgl || $filter_jenis): ?>
    <a href="riwayat_transaksi.php" class="btn btn-abu">✕ Reset</a>
    <?php endif; ?>
  </form>
</div>

<!-- TABEL -->
<div class="card">
  <div class="card-head">
    <span class="card-title">📋 Semua Aktivitas Transaksi</span>
    <span class="badge-count"><?= count($semua) ?> data</span>
  </div>

  <?php if (empty($semua)): ?>
  <div style="text-align:center;padding:2.5rem;color:var(--muda)">
    📭 Belum ada data transaksi<?= $filter_tgl ? " pada tanggal $filter_tgl" : '' ?>.
    <br><br>
    <a href="produk_masuk.php" class="btn btn-utama" style="margin-right:8px">+ Catat Produk Masuk</a>
    <a href="produk_keluar.php" class="btn btn-abu">+ Catat Produk Keluar</a>
  </div>
  <?php else: ?>
  <div class="table-wrap">
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Jenis</th>
        <th>Tanggal</th>
        <th>Nama Produk</th>
        <th>Jumlah</th>
        <th>Harga Satuan</th>
        <th>Total</th>
        <th>Keterangan</th>
        <th>Waktu Input</th>
      </tr>
    </thead>
    <tbody>
      <?php $no = 1; foreach ($semua as $r): ?>
      <tr>
        <td><?= $no++ ?></td>
        <td>
          <?php if ($r['jenis'] === 'masuk'): ?>
            <span class="badge badge-h">📥 Masuk</span>
          <?php else: ?>
            <span class="badge badge-w">📤 Keluar</span>
          <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($r['tanggal'] ?? '-') ?></td>
        <td><strong><?= htmlspecialchars($r['nama_produk'] ?? '-') ?></strong></td>
        <td>
          <span style="font-weight:600;color:<?= $r['jenis']==='masuk'?'#2E7D32':'#C62828' ?>">
            <?= $r['jenis']==='masuk'?'+':'-' ?><?= htmlspecialchars($r['jumlah']) ?>
            <?= htmlspecialchars($r['satuan'] ?? '') ?>
          </span>
        </td>
        <td>Rp <?= number_format((int)$r['harga_satuan'],0,',','.') ?></td>
        <td>
          <strong style="color:<?= $r['jenis']==='masuk'?'#1B5E20':'#B71C1C' ?>">
            Rp <?= number_format((int)$r['total'],0,',','.') ?>
          </strong>
        </td>
        <td style="font-size:.8rem;color:var(--muda)">
          <?= htmlspecialchars($r['keterangan'] ?? '-') ?>
        </td>
        <td style="font-size:.78rem;color:var(--muda);white-space:nowrap">
          <?= $r['waktu'] ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <!-- BARIS TOTAL -->
    <tfoot>
      <tr style="background:var(--krem)">
        <td colspan="6" style="text-align:right;font-weight:600;font-size:.875rem;padding:.75rem .5rem;color:var(--coklat)">
          Total Masuk:
        </td>
        <td style="font-weight:700;color:#1B5E20;padding:.75rem .5rem">
          Rp <?= number_format($total_masuk,0,',','.') ?>
        </td>
        <td colspan="2"></td>
      </tr>
      <tr style="background:var(--krem)">
        <td colspan="6" style="text-align:right;font-weight:600;font-size:.875rem;padding:.25rem .5rem .75rem;color:var(--coklat)">
          Total Keluar:
        </td>
        <td style="font-weight:700;color:#B71C1C;padding:.25rem .5rem .75rem">
          Rp <?= number_format($total_keluar,0,',','.') ?>
        </td>
        <td colspan="2"></td>
      </tr>
    </tfoot>
  </table>
  </div>
  <?php endif; ?>
</div>

<?php include 'footer_nav.php'; ?>
PHPEOF
echo "done"