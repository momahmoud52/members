<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit;
}
$conn = new mysqli("localhost", "root", "", "hr_login");
if ($conn->connect_error) die("DB Error: " . $conn->connect_error);

// حالة لإظهار رسالة نجاح أو فشل
$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $f = fn($x) => $conn->real_escape_string($_POST[$x]);
    [$n,$id,$p,$a,$r,$e,$b] = [$f("full_name"),$f("national_id"),$f("phone"),$f("address"),$f("role"),$f("education_type"),$f("birth_date")];
    $img = '';
    
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $dir = 'uploads/';
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $name = uniqid() . '_' . basename($_FILES['image']['name']);
        $path = $dir . $name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $path)) {
            $img = $path;
        }
    }

    $sql = "INSERT INTO users (full_name, national_id, phone, address, role, education_type, birth_date, image_path)
            VALUES ('$n','$id','$p','$a','$r','$e','$b','$img')";
    if ($conn->query($sql)) {
        $message = "<p style='color: green;'>✅ تم حفظ العضو بنجاح.</p>";
    } else {
        $message = "<p style='color: red;'>❌ فشل الحفظ: " . $conn->error . "</p>";
    }
}

$users = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>الأعضاء</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet">
  <style>
    body {font-family: 'Cairo', sans-serif; background: #f9f9f9; padding: 20px;}
    .container {max-width: 1000px; margin: auto;}
    form, table {background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 8px rgba(0,0,0,0.1); margin-bottom: 30px;}
    input, button {width: 100%; padding: 10px; margin-bottom: 10px; border-radius: 6px; border: 1px solid #ccc;}
    button {background: #0077cc; color: white; font-weight: bold;}
    table {width: 100%; border-collapse: collapse;}
    th, td {padding: 10px; text-align: center; border: 1px solid #eee;}
    th {background: #005f91; color: white;}
    tr:nth-child(even) {background: #f3f3f3;}
    img {border-radius: 6px;}
    .logout {text-align: left; margin-bottom: 20px;}
    .message {margin-bottom: 15px;}
  </style>
</head>
<body>
<div class="container">
  <div class="logout"><a href="logout.php">🚪 تسجيل الخروج</a></div>
  <h2>➕ إضافة عضو جديد</h2>

  <div class="message"><?= $message ?></div>

  <form method="POST" enctype="multipart/form-data">
    <input name="full_name" placeholder="الاسم الكامل" required>
    <input name="national_id" placeholder="الرقم القومي" required>
    <input name="phone" placeholder="رقم الهاتف" required>
    <input name="address" placeholder="العنوان" required>
    <input name="role" placeholder="الصفة داخل الاتحاد" required>
    <input name="education_type" placeholder="نوع الدراسة" required>
    <input type="date" name="birth_date" required>
    <input type="file" name="image" required>
    <button type="submit">حفظ</button>
  </form>

  <h2>📋 جدول الأعضاء</h2>
  <table>
    <tr>
      <th>صورة</th><th>الاسم</th><th>القومي</th><th>الهاتف</th>
      <th>العنوان</th><th>الصفة</th><th>الدراسة</th><th>الميلاد</th><th>إجراءات</th>
    </tr>
    <?php while($u = $users->fetch_assoc()): ?>
    <tr>
      <td><img src="<?= $u['image_path'] ?>" width="50"></td>
      <td><?= $u['full_name'] ?></td>
      <td><?= $u['national_id'] ?></td>
      <td><?= $u['phone'] ?></td>
      <td><?= $u['address'] ?></td>
      <td><?= $u['role'] ?></td>
      <td><?= $u['education_type'] ?></td>
      <td><?= $u['birth_date'] ?></td>
      <td>
        <a href="edit.php?id=<?= $u['id'] ?>">✏️</a>
        <a href="delete.php?id=<?= $u['id'] ?>" onclick="return confirm('حذف؟')">🗑️</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
</body>
</html>
