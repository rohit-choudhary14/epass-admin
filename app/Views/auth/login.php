<?php if (!isset($error)) $error = ''; ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>HC E-PASS | Admin Login</title>

<!-- MAIN CSS -->
<link rel="stylesheet" href="/HC-EPASS-MVC/public/assets/css/styles.css">

<!-- LOGIN PAGE CUSTOM CSS -->
<style>
body {
    margin: 0;
    padding: 0;
    background: #f3f4f6;
    font-family: "Inter","Segoe UI",sans-serif;
}

/* CENTER LOGIN BOX */
.login-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    padding-top: 40px;
    padding-bottom: 40px;
}

/* LOGIN CARD */
.login-box {
    background: #ffffffaa;
    backdrop-filter: blur(6px);
    padding: 35px 40px;
    width: 360px;
    border-radius: 14px;
    box-shadow: 0 6px 25px rgba(0,0,0,0.15);
    border: 1px solid #e5e7eb;
    animation: fadeIn 0.4s ease;
}

/* HEADING */
.login-box h2 {
    text-align: center;
    margin-bottom: 25px;
    font-size: 28px;
    font-weight: 700;
    color: #1f2937;
}

/* ERROR */
.err {
    background: #fee2e2;
    color: #b91c1c;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-size: 14px;
    border: 1px solid #fca5a5;
}

/* LABEL */
.login-box label {
    font-weight: 600;
    font-size: 14px;
    margin-top: 12px;
    display: block;
    color: #374151;
}

/* INPUT FIELDS */
.login-box input {
    width: 100%;
    padding: 11px 13px;
    border-radius: 8px;
    margin-top: 6px;
    border: 1px solid #d1d5db;
    background: #f9fafb;
    font-size: 15px;
}

/* LOGIN BUTTON */
.login-box button {
    margin-top: 22px;
    background: #2563eb;
    color: white;
    padding: 12px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    width: 100%;
    border: none;
    cursor: pointer;
    transition: .2s;
}
.login-box button:hover {
    background: #1d4ed8;
}

/* REGISTER LINK */
.login-box p {
    margin-top: 20px;
    text-align: center;
}
.login-box a {
    color: #2563eb;
    font-size: 14px;
}

/* SMALL ANIMATION */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

</style>
</head>

<body>

<!-- HIGH COURT HEADER -->
<?php include __DIR__ . '/../layouts/header.php'; ?>

<!-- LOGIN AREA -->
<div class="login-wrapper">
    <div class="login-box">
        <h2>Admin Login</h2>

        <?php if ($error): ?>
        <div class="err"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" action="/HC-EPASS-MVC/public/index.php?r=auth/loginPost">
            <label>Username</label>
            <input name="username" required>

            <label>Password</label>
            <input name="password" type="password" required>

            <button type="submit">Login</button>
        </form>

        <p><a href="/HC-EPASS-MVC/public/index.php?r=auth/registerForm">Register admin</a></p>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>

</body>
</html>
