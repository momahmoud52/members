<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "hr_login");
$id = (int) $_GET['id'] ?? 0;
$error = '';
$u = $conn->query("SELECT full_name FROM users WHERE id = $id")->fetch_assoc();

if (!$u) {
    die("⚠️ العضو غير موجود.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $answer = trim(strtolower($_POST['security_answer'] ?? ''));
    if ($answer !== 'messi') {
        $error = "❌ إجابة خاطئة، لن يتم الحذف.";
    } else {
        $conn->query("DELETE FROM users WHERE id = $id");
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تأكيد الحذف</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Cairo', sans-serif;
      background: #f9f9f9;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .confirm-box {
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
    }
    h2 {
      color: #c00;
      text-align: center;
      margin-bottom: 20px;
    }
    form input, button {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
    }
    button {
      background: #c00;
      color: white;
      font-weight: bold;
      border: none;
      cursor: pointer;
    }
    button:hover {
      background: #a00;
    }
    .error {
      color: red;
      text-align: center;
      margin-bottom: 10px;
    }
    .cancel {
      display: block;
      text-align: center;
      margin-top: 10px;
      color: #555;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="confirm-box">
    <h2>⚠️ تأكيد حذف العضو</h2>
    <p>هل تريد حذف العضو: <strong><?= htmlspecialchars($u['full_name']) ?></strong>؟</p>

    <?php if ($error): ?>
      <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <label>سؤال الأمان: من هو أفضل لاعب؟</label>
      <input type="text" name="security_answer" placeholder="أدخل الإجابة" required>
      <button type="submit">نعم، احذف</button>
    </form>

    <a class="cancel" href="index.php">⬅️ رجوع دون حذف</a>
  </div>
</body>
</html>
