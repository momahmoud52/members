<?php
session_start();
$conn = new mysqli("localhost", "root", "", "hr_login");
if ($conn->connect_error) die("DB Error");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $u = $conn->real_escape_string($_POST['username']);
    $p = $_POST['password']; // لا تشفير
    $r = $conn->query("SELECT * FROM admins WHERE username='$u' AND password='$p'");
    if ($r->num_rows === 1) {
        $_SESSION['logged_in'] = true;
        $_SESSION['admin_id'] = $r->fetch_assoc()['ids'];
        header("Location: index.php"); exit;
    } else {
        $error = "بيانات الدخول غير صحيحة!";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تسجيل الدخول</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * {margin: 0; padding: 0; box-sizing: border-box;}
    body {
      font-family: 'Cairo', sans-serif;
      background: #f0f0f0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    form {
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
    }
    input, button {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    button {
      background: #0077cc;
      color: white;
      font-weight: bold;
      border: none;
    }
    p { color: red; text-align: center; }
  </style>
</head>
<body>
  <form method="POST">
    <h2 style="margin-bottom:20px;">🔐 تسجيل الدخول</h2>
    <input type="text" name="username" placeholder="اسم المستخدم" required>
    <input type="password" name="password" placeholder="كلمة المرور" required>
    <button type="submit">دخول</button>
    <?php if (!empty($error)) echo "<p>$error</p>"; ?>
  </form>
</body>
</html>
