<?php if (!isset($error)) $error = ''; ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Login</title>
<link rel="stylesheet" href="/HC-EPASS-MVC/public/assets/css/styles.css">
</head>
<body>
<div class="login-box">
    <h2>Admin Login</h2>
    <?php if ($error): ?><div class="err"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post" action="/HC-EPASS-MVC/public/index.php?r=auth/loginPost">
        <label>Username</label>
        <input name="username" required>
        <label>Password</label>
        <input name="password" type="password" required>
        <button type="submit">Login</button>
    </form>
    <p><a href="/HC-EPASS-MVC/public/index.php?r=auth/registerForm">Register admin</a></p>
</div>
</body>
</html>
