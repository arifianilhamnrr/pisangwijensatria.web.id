<?php
// =============================================
// LOGIN.PHP - Halaman Login
// Pisang Wijen Satria - Sistem Persediaan
// =============================================

session_start();

// Kalau sudah login, langsung ke dashboard
if (isset($_SESSION['id_user'])) {
    header("Location: dashboard.php");
    exit();
}

require_once 'koneksi.php';

$error = "";
$sukses = "";

// Proses form login ketika tombol ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = "Username dan password tidak boleh kosong!";
    } else {
        // Cek user di database
        $username_aman = mysqli_real_escape_string($koneksi, $username);
        $password_aman = mysqli_real_escape_string($koneksi, $password);

        $query  = "SELECT * FROM user WHERE username = '$username_aman' AND password = '$password_aman' LIMIT 1";
        $result = mysqli_query($koneksi, $query);

        if ($result && mysqli_num_rows($result) === 1) {
            $data = mysqli_fetch_assoc($result);

            // Simpan data ke session
            $_SESSION['id_user']    = $data['id_user'];
            $_SESSION['nama_user']  = $data['nama_user'];
            $_SESSION['level_user'] = $data['level_user'];
            $_SESSION['username']   = $data['username'];

            // Arahkan ke dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Username atau password salah. Coba lagi!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Pisang Wijen Satria</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: 'DM Sans', sans-serif;
    min-height: 100vh;
    background: #FFF8EE;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
  }

  /* Dekorasi lingkaran latar */
  body::before {
    content: '';
    position: absolute;
    top: -120px; right: -120px;
    width: 400px; height: 400px;
    border-radius: 50%;
    background: rgba(249,168,37,0.10);
    pointer-events: none;
  }
  body::after {
    content: '';
    position: absolute;
    bottom: -80px; left: -80px;
    width: 300px; height: 300px;
    border-radius: 50%;
    background: rgba(230,81,0,0.07);
    pointer-events: none;
  }

  .card-wrapper {
    display: grid;
    grid-template-columns: 1fr 1fr;
    max-width: 860px;
    width: 100%;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(93,58,10,0.18);
    position: relative;
    z-index: 1;
  }

  /* Panel Kiri - Brand */
  .brand-panel {
    background: linear-gradient(160deg, #F9A825 0%, #E65100 55%, #8B4000 100%);
    padding: 3rem 2.5rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
  }
  .brand-panel::before {
    content: '';
    position: absolute; top: -60px; right: -60px;
    width: 200px; height: 200px; border-radius: 50%;
    background: rgba(255,255,255,0.08);
  }
  .brand-panel::after {
    content: '';
    position: absolute; bottom: -50px; left: -50px;
    width: 180px; height: 180px; border-radius: 50%;
    background: rgba(0,0,0,0.06);
  }
  .brand-top { position: relative; z-index: 1; }
  .brand-icon { font-size: 3.5rem; margin-bottom: 1.25rem; display: block; }
  .brand-title {
    font-family: 'Playfair Display', serif;
    font-size: 2rem; font-weight: 900;
    color: #fff; line-height: 1.2;
    margin-bottom: .6rem;
    text-shadow: 0 2px 8px rgba(0,0,0,0.15);
  }
  .brand-sub {
    font-size: .875rem; color: rgba(255,255,255,0.82);
    line-height: 1.65; font-weight: 300;
  }
  .brand-bottom { position: relative; z-index: 1; }
  .brand-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.25);
    padding: 7px 14px; border-radius: 30px;
    font-size: .78rem; color: #fff; font-weight: 500;
  }

  /* Panel Kanan - Form Login */
  .form-panel {
    background: #fff;
    padding: 2.75rem 2.5rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }
  .form-heading {
    font-family: 'Playfair Display', serif;
    font-size: 1.6rem; color: #5D3A0A;
    margin-bottom: .3rem;
  }
  .form-subheading {
    font-size: .875rem; color: #9E8060;
    margin-bottom: 2rem;
  }

  /* Pesan error / sukses */
  .alert {
    padding: 10px 14px; border-radius: 9px;
    font-size: .85rem; margin-bottom: 1.25rem;
    display: flex; align-items: center; gap: 8px;
  }
  .alert-error   { background: #FFEBEE; color: #B71C1C; border: 1px solid #FFCDD2; }
  .alert-success { background: #E8F5E9; color: #1B5E20; border: 1px solid #C8E6C9; }

  /* Input form */
  .form-group { margin-bottom: 1.1rem; }
  .form-label {
    display: block; font-size: .78rem; font-weight: 600;
    color: #8B5E1A; margin-bottom: .4rem;
    text-transform: uppercase; letter-spacing: .04em;
  }
  .form-input {
    width: 100%; padding: 11px 14px;
    border: 1.5px solid #E8D8B8; border-radius: 10px;
    font-family: 'DM Sans', sans-serif; font-size: .95rem;
    color: #3E2003; background: #FFFBF0;
    outline: none; transition: border-color .2s, box-shadow .2s;
  }
  .form-input:focus {
    border-color: #F9A825;
    box-shadow: 0 0 0 3px rgba(249,168,37,0.15);
    background: #fff;
  }
  .form-input::placeholder { color: #C4A882; }

  /* Toggle tampilkan password */
  .input-wrap { position: relative; }
  .toggle-pass {
    position: absolute; right: 12px; top: 50%;
    transform: translateY(-50%);
    background: none; border: none; cursor: pointer;
    color: #C4A882; font-size: .8rem; font-family: 'DM Sans', sans-serif;
    padding: 4px;
  }
  .toggle-pass:hover { color: #8B5E1A; }

  /* Tombol login */
  .btn-login {
    width: 100%; padding: 13px;
    background: linear-gradient(135deg, #F9A825, #E65100);
    color: #fff; border: none; border-radius: 11px;
    font-family: 'DM Sans', sans-serif; font-size: 1rem;
    font-weight: 600; cursor: pointer;
    transition: transform .2s, box-shadow .2s;
    box-shadow: 0 6px 20px rgba(230,81,0,0.28);
    margin-top: .75rem;
  }
  .btn-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(230,81,0,0.38);
  }
  .btn-login:active { transform: translateY(0); }

  /* Info akun demo */
  .demo-info {
    margin-top: 1.5rem;
    background: #FFF8EE; border: 1px solid #F0DDB8;
    border-radius: 10px; padding: .875rem 1rem;
  }
  .demo-title {
    font-size: .75rem; font-weight: 600; color: #8B5E1A;
    text-transform: uppercase; letter-spacing: .04em;
    margin-bottom: .5rem;
  }
  .demo-row {
    display: flex; justify-content: space-between;
    font-size: .8rem; color: #9E8060; padding: 2px 0;
  }
  .demo-row strong { color: #5D3A0A; }

  .form-footer {
    text-align: center; margin-top: 1.25rem;
    font-size: .78rem; color: #C4A882;
  }

  /* Responsif untuk layar kecil */
  @media (max-width: 600px) {
    .card-wrapper { grid-template-columns: 1fr; }
    .brand-panel { padding: 2rem 1.75rem; min-height: 160px; }
    .brand-title { font-size: 1.5rem; }
    .form-panel { padding: 2rem 1.75rem; }
  }
</style>
</head>
<body>

<div class="card-wrapper">

  <!-- Panel Kiri: Brand -->
  <div class="brand-panel">
    <div class="brand-top">
    <img src="logo.png"
    style="width:80px;height:80px;border-radius:16px;object-fit:cover;margin-bottom:1.25rem;display:block" alt="Logo">
      <h1 class="brand-title">Pisang Wijen<br>Satria</h1>
      <p class="brand-sub">Hidup memang harus dijalani, ketika kamu sudah memilih jalan tersebut, maka tempuhlah dan tuntaskanlah.</p>
    </div>
    <div class="brand-bottom">
      <div class="brand-badge">
        📍 Purwokerto, Jawa Tengah
      </div>
    </div>
  </div>

  <!-- Panel Kanan: Form Login -->
  <div class="form-panel">
    <h2 class="form-heading">Selamat Datang</h2>
    <p class="form-subheading">Masuk untuk mengelola persediaan</p>

    <?php if (!empty($error)): ?>
      <div class="alert alert-error">
        ❌ <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($sukses)): ?>
      <div class="alert alert-success">
        ✅ <?= htmlspecialchars($sukses) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="login.php">

      <div class="form-group">
        <label class="form-label" for="username">Username</label>
        <input
          class="form-input"
          type="text"
          id="username"
          name="username"
          placeholder="Masukkan username"
          value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
          autocomplete="username"
          required
        >
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <div class="input-wrap">
          <input
            class="form-input"
            type="password"
            id="password"
            name="password"
            placeholder="Masukkan password"
            autocomplete="current-password"
            required
            style="padding-right:60px"
          >
          <button type="button" class="toggle-pass" onclick="togglePassword()">
            <span id="toggle-text">Lihat</span>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-login">
        Masuk ke Sistem →
      </button>

    </form>

    <!-- Info akun yang tersedia -->
    <div class="demo-info">
      <div class="demo-title">🔑 Akun yang Tersedia</div>
      <div class="demo-row"><strong>Pemilik/Karyawan</strong> <span>user: owner / pass: suksesselalu1</span></div>

    </div>

    <p class="form-footer">© 2026 Pisang Wijen Satria · Purwokerto</p>
  </div>

</div>

<script>
function togglePassword() {
  var input = document.getElementById('password');
  var btn   = document.getElementById('toggle-text');
  if (input.type === 'password') {
    input.type = 'text';
    btn.textContent = 'Sembunyikan';
  } else {
    input.type = 'password';
    btn.textContent = 'Lihat';
  }
}
</script>

</body>
</html>