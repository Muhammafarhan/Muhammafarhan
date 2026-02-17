<?php
// Koneksi ke database online (ganti dengan detail kamu ya!)
$host = 'db.supabase.co';
$user = 'your-db-user';
$pass = 'your-db-pass';
$dbname = 'your-db-name';

// Cek koneksi
$conn = mysqli_init();
mysqli_real_connect($conn, $host, $user, $pass, $dbname);
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());

// Cek login & session
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$error = '';

// Proses LOGIN
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']); // Aman dari serangan SQL Injection
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = "Muhammad Farhan Saputra Pratama";
        $_SESSION['email'] = "farhan.saputra@contoh.com"; // Email kamu yang sudah diisi
        $_SESSION['password'] = "password_aman_kamu"; // Password kamu yang sudah diisi
        $_SESSION['saldo'] = $user['saldo'];
        header("Refresh:0");
        exit;
    } else {
        $error = "Email atau Password Salah!";
    }
}

// Proses LOGOUT
if (isset($_GET['logout'])) {
    session_destroy();
    header("Refresh:0");
    exit;
}

// Proses TOP UP SALDO
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['topup'])) {
    $newSaldo = $_POST['new_saldo'];
    $userId = $_SESSION['user_id'];
    
    $sql = "UPDATE users SET saldo='$newSaldo' WHERE id='$userId'";
    mysqli_query($conn, $sql);
    
    $_SESSION['saldo'] = $newSaldo;
    header("Refresh:0");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login & Dashboard</title>
    <style>
        * { box-sizing: border-box; }
        .container { max-width: 500px; margin: 30px auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; }
        .error { color: red; text-align: center; }
        .dashboard { text-align: left; }
        .menu { margin-bottom: 20px; padding: 10px; background: #f5f5f5; border-radius: 4px; }
        .menu a { margin-right: 15px; text-decoration: none; color: #0070f3; }
        .detail-item { margin: 10px 0; padding: 8px; border-bottom: 1px solid #eee; }
        .detail-label { font-weight: bold; display: inline-block; width: 120px; }
        button { padding: 10px 20px; background: #0070f3; color: white; border: none; border-radius: 4px; cursor: pointer; }
        input { width: 100%; padding: 8px; margin: 5px 0 15px; border: 1px solid #ccc; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($isLoggedIn): ?>
            <!-- HALAMAN DASHBOARD DENGAN MENU & DETAIL -->
            <div class="menu">
                <a href="?page=dashboard">Dashboard</a>
                <a href="?page=profile">Profil Saya</a>
                <a href="?page=settings">Pengaturan</a>
                <a href="?logout=1">Logout</a>
            </div>

            <?php 
            $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
            if ($page == 'dashboard'): ?>
                <div class="dashboard">
                    <h2>Selamat Datang, <?php echo $_SESSION['full_name']; ?>!</h2>
                    <div class="detail-item">
                        <span class="detail-label">Nama Lengkap:</span> <?php echo $_SESSION['full_name']; ?>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Email:</span> <?php echo $_SESSION['email']; ?>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Password:</span> <?php echo $_SESSION['password']; ?>
                    </div>
                    <div class="detail-item" style="color: green; font-size: 1.2em;">
                        <span class="detail-label">Saldo Anda:</span> <?php echo $_SESSION['saldo']; ?>
                    </div>
                    <!-- FITUR TOP UP SALDO -->
                    <hr>
                    <h4>Tambah Saldo Anda</h4>
                    <form method="POST">
                        <input type="text" name="new_saldo" placeholder="Contoh: US$ 999.999.500.000" required style="margin-bottom: 10px;">
                        <button type="submit" name="topup" style="width: 100%;">Update Saldo</button>
                    </form>
                </div>
            <?php elseif ($page == 'profile'): ?>
                <h3>Profil Lengkap</h3>
                <div class="detail-item">
                    <span class="detail-label">ID User:</span> <?php echo $_SESSION['user_id']; ?>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Nama Lengkap:</span> <?php echo $_SESSION['full_name']; ?>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Email:</span> <?php echo $_SESSION['email']; ?>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Password:</span> <?php echo $_SESSION['password']; ?>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Saldo:</span> <?php echo $_SESSION['saldo']; ?>
                </div>
            <?php elseif ($page == 'settings'): ?>
                <h3>Pengaturan Akun</h3>
                <p>Fitur pengaturan akan segera ditambahkan!</p>
            <?php endif; ?>

        <?php else: ?>
            <!-- FORM LOGIN -->
            <h2 style="text-align: center;">Login Akun</h2>
            <?php if ($error != '') echo "<p class='error'>$error</p>"; ?>
            <form method="POST">
                <label>Email:</label>
                <input type="email" name="email" required>

                <label>Password:</label>
                <input type="password" name="password" required>

                <button type="submit" name="login">Login</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
