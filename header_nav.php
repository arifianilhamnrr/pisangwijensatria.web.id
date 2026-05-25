<?php
// header_nav.php - Navigasi dan layout atas, dipakai semua halaman
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['id_user'])) { header("Location: login.php"); exit(); }

$halaman_aktif = basename($_SERVER['PHP_SELF']);
$level         = $_SESSION['level_user'];
$nama          = $_SESSION['nama_user'];
$inisial       = strtoupper(substr($nama, 0, 2));
$level_map     = [1=>'Admin', 2=>'Pemilik', 3=>'Kasir'];
$label_level   = $level_map[$level] ?? 'User';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pisang Wijen Satria</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
:root {
  --kuning:#F9A825; --oranye:#E65100;
  --coklat:#5D3A0A; --coklat-tua:#3E2003;
  --muda:#9E8060; --krem:#FFF8EE; --border:#EFE0C4;
}
html { scroll-behavior:smooth; }
body { font-family:'DM Sans',sans-serif; background:#F7F0E6; color:var(--coklat-tua); min-height:100vh; }

/* ===== SIDEBAR ===== */
.sidebar {
  position:fixed; top:0; left:0; bottom:0; width:235px;
  background:linear-gradient(180deg,#3E2003 0%,#5D3A0A 100%);
  display:flex; flex-direction:column; z-index:200;
  box-shadow: 4px 0 20px rgba(62,32,3,.2);
  overflow:hidden;
}
/* Area scroll menu navigasi saja — logo & tombol keluar tetap terlihat */
.sidebar-scroll {
  flex:1; overflow-y:auto; padding:0 1.2rem;
  /* sembunyikan scrollbar tapi tetap bisa scroll */
  scrollbar-width:none;
}
.sidebar-scroll::-webkit-scrollbar { display:none; }
.sidebar-top { padding:1.4rem 1.2rem 0; }
.sidebar-bottom { padding:0 1.2rem 1.2rem; flex-shrink:0; }
.logo { display:flex; align-items:center; gap:9px; padding-bottom:1.2rem; margin-bottom:1.2rem; border-bottom:1px solid rgba(255,255,255,.1); }
.logo-icon { font-size:1.6rem; }
.logo-text { font-family:'Playfair Display',serif; font-size:1rem; color:#fff; line-height:1.2; }
.logo-text small { display:block; font-family:'DM Sans',sans-serif; font-size:.68rem; color:var(--kuning); font-weight:400; }

.nav-sec { font-size:.63rem; text-transform:uppercase; letter-spacing:.1em; color:rgba(255,255,255,.3); margin:.9rem 0 .35rem; }

.nav-a {
  display:flex; align-items:center; gap:9px; padding:9px 10px; border-radius:9px;
  font-size:.875rem; color:rgba(255,255,255,.7); text-decoration:none;
  transition:all .18s; margin-bottom:2px; cursor:pointer; border:none; background:none;
  width:100%; font-family:'DM Sans',sans-serif;
}
.nav-a:hover { background:rgba(255,255,255,.09); color:#fff; }
.nav-a.aktif { background:linear-gradient(135deg,var(--kuning),var(--oranye)); color:#fff; font-weight:600; }
.nav-ico { font-size:1rem; width:18px; text-align:center; flex-shrink:0; }

.sidebar-bottom { margin-top:auto; }
.user-box {
  background:rgba(255,255,255,.08); border-radius:10px; padding:10px 11px;
  display:flex; align-items:center; gap:9px; margin-bottom:6px;
}
.avatar {
  width:34px; height:34px; border-radius:50%;
  background:linear-gradient(135deg,var(--kuning),var(--oranye));
  display:flex; align-items:center; justify-content:center;
  font-size:.8rem; font-weight:700; color:#fff; flex-shrink:0;
}
.user-nm { font-size:.85rem; color:#fff; font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.user-lv { font-size:.68rem; color:var(--kuning); }

/* ===== KONTEN ===== */
.main { margin-left:235px; padding:2rem 2.25rem; min-height:100vh; }

.page-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; }
.page-title { font-family:'Playfair Display',serif; font-size:1.6rem; color:var(--coklat); }
.page-sub { font-size:.85rem; color:var(--muda); margin-top:2px; }

/* ===== ALERT ===== */
.alert { padding:10px 14px; border-radius:9px; font-size:.875rem; margin-bottom:1.25rem; display:flex; align-items:center; gap:8px; }
.alert-sukses  { background:#E8F5E9; color:#1B5E20; border:1px solid #C8E6C9; }
.alert-error   { background:#FFEBEE; color:#B71C1C; border:1px solid #FFCDD2; }
.alert-sukses::before { content:'✅ '; }
.alert-error::before  { content:'❌ '; }

/* ===== CARD ===== */
.card { background:#fff; border-radius:14px; padding:1.35rem; border:1px solid var(--border); overflow:hidden; }
.card.mb { margin-bottom:1.25rem; }
.card-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.1rem; flex-wrap:wrap; gap:8px; }
.card-title { font-family:'Playfair Display',serif; font-size:1.05rem; color:var(--coklat); }
.badge-count { background:#F7F0E6; border:1px solid var(--border); padding:3px 10px; border-radius:20px; font-size:.78rem; color:var(--muda); }

/* ===== FORM ===== */
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
.form-group { display:flex; flex-direction:column; }
.form-label { font-size:.78rem; font-weight:600; color:#8B5E1A; text-transform:uppercase; letter-spacing:.04em; margin-bottom:.4rem; }
.form-input {
  padding:10px 13px; border:1.5px solid var(--border); border-radius:9px;
  font-family:'DM Sans',sans-serif; font-size:.9rem; color:var(--coklat-tua);
  background:var(--krem); outline:none; transition:border-color .2s, box-shadow .2s;
  width:100%;
}
.form-input:focus { border-color:var(--kuning); box-shadow:0 0 0 3px rgba(249,168,37,.15); background:#fff; }
.form-input::placeholder { color:#C4A882; }
select.form-input { appearance:none; background-image:url("data:image/svg+xml,%3Csvg width='12' height='7' viewBox='0 0 12 7' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%238B5E1A' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 12px center; padding-right:32px; }
.req { color:var(--oranye); }
.hint { font-size:.75rem; color:var(--muda); margin-top:3px; }

/* ===== TOMBOL ===== */
.btn {
  display:inline-flex; align-items:center; gap:5px; padding:9px 18px;
  border-radius:9px; font-family:'DM Sans',sans-serif; font-size:.875rem;
  font-weight:500; cursor:pointer; border:none; text-decoration:none;
  transition:all .18s; white-space:nowrap;
}
.btn-utama { background:linear-gradient(135deg,var(--kuning),var(--oranye)); color:#fff; box-shadow:0 4px 14px rgba(230,81,0,.25); }
.btn-utama:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(230,81,0,.35); }
.btn-abu { background:#F0E8DC; color:var(--coklat); border:1px solid var(--border); }
.btn-abu:hover { background:#E8DDD0; }
.btn-kecil { padding:5px 11px; font-size:.78rem; border-radius:6px; }
.btn-biru { background:#E6F1FB; color:#185FA5; border:1px solid #B5D4F4; }
.btn-biru:hover { background:#d0e5f8; }
.btn-merah { background:#FFEBEE; color:#B71C1C; border:1px solid #FFCDD2; }
.btn-merah:hover { background:#FFCDD2; }

/* ===== TABEL ===== */
.table-wrap { overflow-x:auto; }
table { width:100%; border-collapse:collapse; min-width:500px; }
th { font-size:.72rem; text-transform:uppercase; letter-spacing:.04em; color:var(--muda); font-weight:600; padding:.55rem .5rem; text-align:left; border-bottom:1.5px solid var(--border); white-space:nowrap; }
td { padding:.6rem .5rem; font-size:.875rem; border-bottom:1px solid #FAF4E8; vertical-align:middle; }
tr:last-child td { border-bottom:none; }
tr:hover td { background:#FFFAF3; }
code { background:#F0E8DC; padding:2px 7px; border-radius:4px; font-size:.8rem; word-break:break-all; }

/* Tabel khusus dashboard - tidak perlu min-width */
.dashboard-table { min-width:auto; width:100%; table-layout:fixed; }
.dashboard-table td { overflow:hidden; text-overflow:ellipsis; }
.dashboard-table th:nth-child(1) { width:35%; }
.dashboard-table th:nth-child(2) { width:25%; }
.dashboard-table th:nth-child(3) { width:25%; }
.dashboard-table th:nth-child(4) { width:15%; }

/* Transaction list */
.transaction-list { overflow:hidden; }

/* Tabel produk - kelola produk */
.produk-table { min-width:auto; width:100%; }
.produk-table td, .produk-table th { white-space:nowrap; }
.produk-table .btn { display:inline-block; margin:0 2px; }

/* Tabel lainnya */
.keluar-table, .kategori-table, .riwayat-table, .supplier-table, .user-table { min-width:auto; width:100%; }
.keluar-table td, .keluar-table th,
.kategori-table td, .kategori-table th,
.riwayat-table td, .riwayat-table th,
.supplier-table td, .supplier-table th,
.user-table td, .user-table th { white-space:nowrap; }
.keluar-table .btn, .kategori-table .btn, .riwayat-table .btn, .supplier-table .btn, .user-table .btn { display:inline-block; margin:0 2px; }

/* ===== BADGE ===== */
.badge { display:inline-block; padding:2px 9px; border-radius:20px; font-size:.72rem; font-weight:600; }
.badge-h   { background:#E8F5E9; color:#2E7D32; }
.badge-w   { background:#FFF3E0; color:#E65100; }
.badge-o   { background:#FFF8E1; color:#8B5E1A; border:1px solid #F0DDB8; }
.badge-abu { background:#F3F4F6; color:#555; border:1px solid #E0E0E0; }
.badge-b   { background:#E6F1FB; color:#185FA5; }

/* ===== HAMBURGER MENU ===== */
.hamburger {
  display:none; position:fixed; top:1rem; left:1rem; z-index:300;
  background:linear-gradient(135deg,var(--kuning),var(--oranye));
  border:none; border-radius:8px; padding:8px 12px;
  cursor:pointer; box-shadow:0 4px 12px rgba(230,81,0,.3);
}
.hamburger span {
  display:block; width:20px; height:2px; background:#fff;
  margin:4px 0; transition:all .3s;
}

/* ===== RESPONSIVE ===== */
@media (max-width:850px) {
  .sidebar { width:200px; }
  .main { margin-left:200px; padding:1.25rem; }
  .form-grid { grid-template-columns:1fr; }
  .page-head { flex-direction:column; align-items:flex-start; }
}

@media (max-width:768px) {
  .hamburger { display:block; }
  .sidebar {
    transform:translateX(-100%); transition:transform .3s;
    width:260px; box-shadow:none;
  }
  .sidebar.open {
    transform:translateX(0); box-shadow:4px 0 20px rgba(62,32,3,.4);
  }
  .main { margin-left:0; padding:1rem; padding-top:4rem; }

  /* Overlay gelap saat sidebar terbuka */
  body::before {
    content:''; position:fixed; top:0; left:0; right:0; bottom:0;
    background:rgba(0,0,0,0); pointer-events:none; z-index:150;
    transition:background .3s;
  }
  body.sidebar-open::before {
    background:rgba(0,0,0,.5); pointer-events:auto;
  }

  /* Grid responsif */
  .form-grid { grid-template-columns:1fr; gap:.75rem; }

  /* Topbar dashboard */
  .topbar { flex-direction:column; align-items:flex-start !important; }

  /* Card statistik jadi 2 kolom */
  .stats-grid { grid-template-columns:repeat(2,1fr) !important; }

  /* Shortcut menu jadi 2 kolom */
  .shortcut-grid { grid-template-columns:repeat(2,1fr) !important; }

  /* Tabel produk & transaksi jadi 1 kolom */
  .content-grid { grid-template-columns:1fr !important; }
  .content-grid .card { overflow:hidden; }

  /* Ringkasan riwayat jadi 1 kolom */
  .summary-grid { grid-template-columns:1fr !important; }

  /* Tabel scroll horizontal */
  .table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
  table { min-width:600px; }

  /* Dashboard table - tidak perlu scroll, gunakan ellipsis */
  .dashboard-table { min-width:auto !important; }
  .dashboard-table th { font-size:.68rem; padding:.4rem .3rem; }
  .dashboard-table td { font-size:.8rem; padding:.5rem .3rem; }
  .dashboard-table th:nth-child(1) { width:30%; }
  .dashboard-table th:nth-child(2) { width:30%; }
  .dashboard-table th:nth-child(3) { width:25%; }
  .dashboard-table th:nth-child(4) { width:15%; }

  /* Transaction list lebih compact */
  .transaction-list > div { padding:.5rem 0 !important; gap:8px !important; }
  .transaction-list > div > div:first-child { width:30px !important; height:30px !important; font-size:.85rem !important; }
  .transaction-list > div > div:nth-child(2) > div:first-child { font-size:.8rem !important; }
  .transaction-list > div > div:nth-child(2) > div:last-child { font-size:.7rem !important; }
  .transaction-list > div > div:last-child { font-size:.8rem !important; }

  /* Tabel produk lebih compact */
  .produk-table { min-width:700px; }
  .produk-table th { font-size:.68rem; padding:.4rem .3rem; }
  .produk-table td { font-size:.75rem; padding:.5rem .3rem; }
  .produk-table .btn-kecil { padding:4px 8px; font-size:.7rem; margin:1px; }
  .produk-table .badge { font-size:.68rem; padding:2px 6px; }

  /* Tabel lainnya lebih compact */
  .keluar-table, .kategori-table, .riwayat-table, .supplier-table, .user-table { min-width:700px; }
  .keluar-table th, .kategori-table th, .riwayat-table th, .supplier-table th, .user-table th { font-size:.68rem; padding:.4rem .3rem; }
  .keluar-table td, .kategori-table td, .riwayat-table td, .supplier-table td, .user-table td { font-size:.75rem; padding:.5rem .3rem; }
  .keluar-table .btn-kecil, .kategori-table .btn-kecil, .riwayat-table .btn-kecil, .supplier-table .btn-kecil, .user-table .btn-kecil { padding:4px 8px; font-size:.7rem; margin:1px; }
  .keluar-table .badge, .kategori-table .badge, .riwayat-table .badge, .supplier-table .badge, .user-table .badge { font-size:.68rem; padding:2px 6px; }

  /* Card dengan max-width jadi full width */
  .card[style*="max-width"] { max-width:100% !important; }

  /* Tab filter buttons wrap */
  div[style*="display:flex;gap:8px"] button { flex:1; min-width:auto; }

  /* Statistik card lebih compact */
  .stats-grid > div { padding:.9rem 1rem !important; }
  .stats-grid > div > div[style*="font-size:1.75rem"],
  .stats-grid > div > div[style*="font-size:1.5rem"] { font-size:1.3rem !important; }

  /* Shortcut menu lebih compact */
  .shortcut-grid a { padding:.85rem !important; }

  /* Button group wrap */
  .card-head { flex-direction:column; align-items:flex-start !important; gap:8px; }

  /* Form filter wrap */
  form[style*="display:flex"] { flex-direction:column; align-items:stretch !important; }
  form[style*="display:flex"] .form-input { width:100% !important; }
  form[style*="display:flex"] .btn { width:100%; justify-content:center; }
  form[style*="display:flex"] .form-group { width:100%; }

  /* Action buttons di tabel */
  td .btn { margin-bottom:4px; }
}

@media (max-width:480px) {
  .page-title { font-size:1.3rem; }
  .card { padding:1rem; }
  .btn { width:100%; justify-content:center; }

  /* Statistik jadi 1 kolom di layar sangat kecil */
  .stats-grid { grid-template-columns:1fr !important; }
  .shortcut-grid { grid-template-columns:1fr !important; }

  /* Statistik card lebih compact lagi */
  .stats-grid > div { padding:.75rem .85rem !important; }
  .stats-grid > div > div[style*="font-size:1.75rem"],
  .stats-grid > div > div[style*="font-size:1.5rem"],
  .stats-grid > div > div[style*="font-size:1.4rem"] { font-size:1.2rem !important; }
  .stats-grid > div > div[style*="font-size:.72rem"] { font-size:.68rem !important; }

  /* Summary grid lebih compact */
  .summary-grid > div { padding:.85rem 1rem !important; }
  .summary-grid > div > div[style*="font-size:1.4rem"] { font-size:1.2rem !important; }

  /* Dashboard table lebih compact */
  .dashboard-table th { font-size:.65rem !important; padding:.35rem .25rem !important; }
  .dashboard-table td { font-size:.75rem !important; padding:.45rem .25rem !important; }
  .dashboard-table .badge { font-size:.65rem !important; padding:2px 6px !important; }
  .dashboard-table th:nth-child(1) { width:35%; }
  .dashboard-table th:nth-child(2) { width:0; display:none; }
  .dashboard-table td:nth-child(2) { display:none; }
  .dashboard-table th:nth-child(3) { width:40%; }
  .dashboard-table th:nth-child(4) { width:25%; }

  /* Transaction list lebih compact */
  .transaction-list > div { padding:.45rem 0 !important; gap:6px !important; }
  .transaction-list > div > div:first-child { width:28px !important; height:28px !important; font-size:.8rem !important; }
  .transaction-list > div > div:nth-child(2) > div:first-child { font-size:.75rem !important; }
  .transaction-list > div > div:nth-child(2) > div:last-child { font-size:.68rem !important; }
  .transaction-list > div > div:last-child { font-size:.75rem !important; }

  /* Tabel produk lebih compact lagi */
  .produk-table { min-width:650px; }
  .produk-table th { font-size:.65rem !important; padding:.35rem .25rem !important; }
  .produk-table td { font-size:.7rem !important; padding:.45rem .25rem !important; }
  .produk-table .btn-kecil { padding:3px 6px !important; font-size:.65rem !important; margin:1px !important; }
  .produk-table .badge { font-size:.65rem !important; padding:1px 5px !important; }

  /* Tabel lainnya lebih compact lagi */
  .keluar-table, .kategori-table, .riwayat-table, .supplier-table, .user-table { min-width:650px; }
  .keluar-table th, .kategori-table th, .riwayat-table th, .supplier-table th, .user-table th { font-size:.65rem !important; padding:.35rem .25rem !important; }
  .keluar-table td, .kategori-table td, .riwayat-table td, .supplier-table td, .user-table td { font-size:.7rem !important; padding:.45rem .25rem !important; }
  .keluar-table .btn-kecil, .kategori-table .btn-kecil, .riwayat-table .btn-kecil, .supplier-table .btn-kecil, .user-table .btn-kecil { padding:3px 6px !important; font-size:.65rem !important; margin:1px !important; }
  .keluar-table .badge, .kategori-table .badge, .riwayat-table .badge, .supplier-table .badge, .user-table .badge { font-size:.65rem !important; padding:1px 5px !important; }

  /* Font lebih kecil di tabel */
  th { font-size:.68rem; padding:.4rem .3rem; }
  td { font-size:.8rem; padding:.5rem .3rem; }

  /* Badge lebih kecil */
  .badge { font-size:.68rem; padding:2px 7px; }

  /* Hamburger lebih kecil */
  .hamburger { padding:6px 10px; top:.75rem; left:.75rem; }
  .hamburger span { width:18px; }

  /* Logo sidebar lebih kecil */
  .logo img { width:35px !important; height:35px !important; }
  .logo-text { font-size:.9rem; }

  /* Card title lebih kecil */
  .card-title { font-size:.95rem; }

  /* Form input lebih compact */
  .form-input { padding:8px 11px; font-size:.85rem; }
  .form-label { font-size:.72rem; }

  /* Button lebih compact */
  .btn { padding:8px 14px; font-size:.8rem; }
  .btn-kecil { padding:4px 9px; font-size:.72rem; }
}
</style>
</head>
<body>

<!-- Hamburger Menu Button -->
<button class="hamburger" onclick="toggleSidebar()" aria-label="Toggle Menu">
  <span></span>
  <span></span>
  <span></span>
</button>

<aside class="sidebar" id="sidebar">

  <!-- LOGO — selalu terlihat di atas -->
  <div class="sidebar-top">
    <div class="logo">
    <img src="logo.png"
    style="width:40px;height:40px;border-radius:10px;object-fit:cover;flex-shrink:0" alt="Logo Pisang Wijen Satria">
      <div class="logo-text">Pisang Wijen Satria<small>Sistem Persediaan</small></div>
    </div>
  </div>

  <!-- MENU — bisa scroll kalau layar kecil -->
  <div class="sidebar-scroll">
    <div class="nav-sec">Utama</div>
    <a class="nav-a <?= $halaman_aktif=='dashboard.php'?'aktif':'' ?>" href="dashboard.php">
      <span class="nav-ico">⊞</span> Dashboard
    </a>
    <a class="nav-a <?= $halaman_aktif=='kelola_produk.php'?'aktif':'' ?>" href="kelola_produk.php">
      <span class="nav-ico">📦</span> Data Produk
    </a>
    <?php if ($level != 3): ?>
    <a class="nav-a <?= $halaman_aktif=='kelola_kategori.php'?'aktif':'' ?>" href="kelola_kategori.php">
      <span class="nav-ico">📂</span> Kategori
    </a>
    <?php endif; ?>

    <div class="nav-sec">Transaksi</div>
    <a class="nav-a <?= $halaman_aktif=='produk_masuk.php'?'aktif':'' ?>" href="produk_masuk.php">
      <span class="nav-ico">↓</span> Produk Masuk
    </a>
    <a class="nav-a <?= $halaman_aktif=='produk_keluar.php'?'aktif':'' ?>" href="produk_keluar.php">
      <span class="nav-ico">↑</span> Produk Keluar
    </a>
    <a class="nav-a <?= $halaman_aktif=='riwayat_transaksi.php'?'aktif':'' ?>" href="riwayat_transaksi.php">
      <span class="nav-ico">🧾</span> Riwayat Transaksi
    </a>

    <?php if ($level != 3): ?>
    <div class="nav-sec">Supplier</div>
    <a class="nav-a <?= $halaman_aktif=='kelola_supplier.php'?'aktif':'' ?>" href="kelola_supplier.php">
      <span class="nav-ico">🚚</span> Data Supplier
    </a>
    <?php endif; ?>

    <?php if ($level == 2): ?>
    <div class="nav-sec">Pengaturan</div>
    <a class="nav-a <?= $halaman_aktif=='kelola_user.php'?'aktif':'' ?>" href="kelola_user.php">
      <span class="nav-ico">👥</span> Kelola Akun
    </a>
    <?php endif; ?>
  </div>

  <!-- PROFIL & TOMBOL KELUAR — selalu terlihat di bawah -->
  <div class="sidebar-bottom">
    <div style="height:1px;background:rgba(255,255,255,.1);margin-bottom:10px"></div>
    <div class="user-box">
      <div class="avatar"><?= $inisial ?></div>
      <div style="overflow:hidden">
        <div class="user-nm"><?= htmlspecialchars($nama) ?></div>
        <div class="user-lv"><?= $label_level ?></div>
      </div>
    </div>
    <form method="POST" action="logout.php">
      <button type="submit" class="nav-a" style="color:rgba(255,255,255,.75);margin-top:2px">
        <span class="nav-ico">⏏</span> Keluar
      </button>
    </form>
  </div>

</aside>

<main class="main">