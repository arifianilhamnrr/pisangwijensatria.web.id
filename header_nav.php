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
.card { background:#fff; border-radius:14px; padding:1.35rem; border:1px solid var(--border); }
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
code { background:#F0E8DC; padding:2px 7px; border-radius:4px; font-size:.8rem; }

/* ===== BADGE ===== */
.badge { display:inline-block; padding:2px 9px; border-radius:20px; font-size:.72rem; font-weight:600; }
.badge-h   { background:#E8F5E9; color:#2E7D32; }
.badge-w   { background:#FFF3E0; color:#E65100; }
.badge-o   { background:#FFF8E1; color:#8B5E1A; border:1px solid #F0DDB8; }
.badge-abu { background:#F3F4F6; color:#555; border:1px solid #E0E0E0; }
.badge-b   { background:#E6F1FB; color:#185FA5; }

@media (max-width:850px) {
  .sidebar { width:200px; }
  .main { margin-left:200px; padding:1.25rem; }
  .form-grid { grid-template-columns:1fr; }
}
@media (max-width:600px) {
  .sidebar { display:none; }
  .main { margin-left:0; }
}
</style>
</head>
<body>

<aside class="sidebar">

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