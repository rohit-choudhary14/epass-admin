<?php 
if (!isset($error)) $error=''; 
if (!isset($success)) $success='';
include __DIR__ . '/../layouts/header.php';
?>

<style>
body {
    background:#f3f4f6;
    font-family:"Inter","Segoe UI",sans-serif;
    margin: 0 !important;
    padding: 0 !important;
}

/* CENTER WRAPPER */
.register-wrapper {
    max-width: 650px;
    margin: 30px auto;
    background: #ffffffaa;
    backdrop-filter: blur(6px);
    padding: 35px 40px;
    border-radius: 14px;
    box-shadow: 0 6px 25px rgba(0,0,0,0.12);
    border: 1px solid #e5e7eb;
    animation: fadeIn .4s ease;
}

/* HEADING */
.register-wrapper h2 {
    font-size: 30px;
    font-weight: 700;
    text-align: center;
    color: #1f2937;
    margin-bottom: 25px;
}

/* ERROR MESSAGE */
.err {
    background: #fee2e2;
    color: #b91c1c;
    padding: 10px 12px;
    border-radius: 6px;
    margin-bottom: 15px;
    border: 1px solid #fca5a5;
}

/* SUCCESS MESSAGE */
.success {
    background: #dcfce7;
    color: #166534;
    padding: 10px 12px;
    border-radius: 6px;
    margin-bottom: 15px;
    border: 1px solid #86efac;
}

/* LABELS */
.register-wrapper label {
    font-weight: 600;
    font-size: 14px;
    color: #374151;
    margin-top: 15px;
    display: block;
}

/* INPUTS */
.register-wrapper input,
.register-wrapper textarea,
.register-wrapper select {
    width: 100%;
    padding: 11px 13px;
    border-radius: 8px;
    margin-top: 6px;
    border: 1px solid #d1d5db;
    background: #f9fafb;
    font-size: 15px;
}

/* BUTTON */
.register-wrapper button {
    margin-top: 25px;
    padding: 13px;
    width: 100%;
    border: none;
    background: #2563eb;
    color: white;
    font-size: 17px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: .2s;
}
.register-wrapper button:hover {
    background: #1d4ed8;
}

.register-wrapper p {
    text-align: center;
    margin-top: 18px;
}
.register-wrapper a {
    color: #2563eb;
    font-weight: 600;
}

/* ANIMATION */
@keyframes fadeIn {
    from { opacity:0; transform:translateY(20px); }
    to { opacity:1; transform:translateY(0); }
}
</style>

<div class="register-wrapper">

    <h2>Create Admin Account</h2>

    <?php if ($error): ?>
        <div class="err"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" action="/HC-EPASS-MVC/public/index.php?r=auth/registerPost">

        <label>Username</label>
        <input name="username" required>

        <label>Full name</label>
        <input name="name" required>

        <label>Email</label>
        <input name="email" type="email">

        <label>Contact</label>
        <input name="contact">

        <label>Address</label>
        <textarea name="address" rows="3"></textarea>

        <label>Gender</label>
        <select name="gender">
            <option value="">--</option>
            <option value="M">Male</option>
            <option value="F">Female</option>
        </select>

        <label>Password</label>
        <input name="password" type="password" required>

        <button type="submit">Create Admin</button>
    </form>

    <p><a href="/HC-EPASS-MVC/public/index.php?r=auth/loginForm">Back to login</a></p>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
