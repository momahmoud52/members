<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "hr_login");
$id = (int) $_GET['id'];
$u = $conn->query("SELECT * FROM users WHERE id = $id")->fetch_assoc();

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $answer = trim(strtolower($_POST['security_answer'] ?? ''));
    if ($answer !== 'messi') {
        $error = "❌ إجابة السؤال غير صحيحة. الرجاء المحاولة مرة أخرى.";
    } else {
        $f = fn($x) => $conn->real_escape_string($_POST[$x]);
        [$n,$idn,$p,$a,$r,$e,$b] = [$f("full_name"),$f("national_id"),$f("phone"),$f("address"),$f("role"),$f("education_type"),$f("birth_date")];

        $img = $u['image_path'];
        if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $dir = 'uploads/';
            $name = uniqid() . '_' . basename($_FILES['image']['name']);
            $path = $dir . $name;
            if (!is_dir($dir)) mkdir($dir);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $path)) $img = $path;
        }

        $conn->query("UPDATE users SET
            full_name='$n', national_id='$idn', phone='$p', address='$a',
            role='$r', education_type='$e', birth_date='$b', image_path='$img'
            WHERE id = $id");

        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تعديل بيانات العضو</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Cairo', sans-serif;
      background: #f5f7fa;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding: 40px;
    }
    .form-container {
      background: #fff;
      border-radius: 8px;
      padding: 30px;
      width: 100%;
      max-width: 600px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
      margin-bottom: 20px;
      text-align: center;
      color: #333;
    }
    input, button {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 16px;
    }
    button {
      background: #007bff;
      color: white;
      font-weight: bold;
      border: none;
      cursor: pointer;
    }
    button:hover {
      background: #0056b3;
    }
    .error {
      color: red;
      margin-bottom: 15px;
      text-align: center;
    }
  </style>
</head>
<body>
<div class="form-container">
  <h2>✏️ تعديل بيانات العضو</h2>
  
  <?php if ($error): ?>
    <div class="error"><?= $error ?></div>
  <?php endif; ?>
  
  <form method="POST" enctype="multipart/form-data">
    <input type="text" name="full_name" value="<?= $u['full_name'] ?>" required placeholder="الاسم الكامل">
    <input type="text" name="national_id" value="<?= $u['national_id'] ?>" required placeholder="الرقم القومي">
    <input type="text" name="phone" value="<?= $u['phone'] ?>" required placeholder="رقم الهاتف">
    <input type="text" name="address" value="<?= $u['address'] ?>" required placeholder="العنوان">
    <input type="text" name="role" value="<?= $u['role'] ?>" required placeholder="الصفة داخل الاتحاد">
    <input type="text" name="education_type" value="<?= $u['education_type'] ?>" required placeholder="نوع الدراسة">
    <input type="date" name="birth_date" value="<?= $u['birth_date'] ?>" required>

    <label>تحميل صورة جديدة (اختياري):</label>
    <input type="file" name="image">

    <label>سؤال الأمان: من هو أفضل لاعب؟</label>
    <input type="text" name="security_answer" placeholder="أدخل الإجابة" required>

    <button type="submit">تحديث البيانات</button>
  </form>
</div>
</body>
</html>
