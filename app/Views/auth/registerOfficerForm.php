<?php 
if (!isset($error)) $error=''; 
if (!isset($success)) $success='';
include __DIR__ . '/../layouts/header.php';
?>

<style>
/* SAME CSS — untouched */
body {
    background:#f3f4f6;
    font-family:"Inter","Segoe UI",sans-serif;
}
.officer-box {
    max-width: 650px;
    margin: 30px auto;
    background: #ffffffaa;
    backdrop-filter: blur(6px);
    padding: 35px 40px;
    border-radius: 14px;
    box-shadow: 0 6px 25px rgba(0,0,0,0.12);
    border: 1px solid #e5e7eb;
}
.officer-box h2 {
    text-align: center;
    font-size: 30px;
    font-weight: 700;
    margin-bottom: 25px;
    color:#1f2937;
}
.err {
    background: #fee2e2;
    color: #b91c1c;
    padding: 10px 12px;
    border-radius: 6px;
    margin-bottom: 15px;
}
.success {
    background: #dcfce7;
    color: #166534;
    padding: 10px 12px;
    border-radius: 6px;
    margin-bottom: 15px;
}
.officer-box label {
    display:block;
    margin-top:15px;
    font-size:14px;
    font-weight:600;
    color:#374151;
}
.officer-box input,
.officer-box select {
    width:100%;
    padding:11px 13px;
    font-size:15px;
    border-radius:8px;
    border:1px solid #d1d5db;
    margin-top:6px;
    background:#f9fafb;
}
.officer-box button {
    margin-top:25px;
    width:100%;
    padding:13px;
    background:#2563eb;
    color:white;
    border:none;
    border-radius:8px;
    font-size:17px;
    font-weight:600;
}
</style>

<div class="officer-box">

    <h2>Register Officer</h2>

    <?php if($error): ?>
        <div class="err"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" action="/HC-EPASS-MVC/public/index.php?r=auth/registerOfficerPost">

        <label>Username *</label>
        <input name="username" required>

        <label>Full Name *</label>
        <input name="name" required>

        <label>Gender *</label>
        <select name="gender" required>
            <option value="">-- Select Gender --</option>
            <option value="M">Male</option>
            <option value="F">Female</option>
        </select>

        <label>Email *</label>
        <input name="email" type="email" required>

        <label>Contact Number *</label>
        <input name="contact" required>

        <label>Password *</label>
        <input name="password" type="password" required>

        <!-- NEW FIELD: Establishment -->
        <label>Establishment *</label>
        <select name="establishment" required>
            <option value="">-- Select Establishment --</option>
            <option value="P">Jodhpur (Principal Seat)</option>
            <option value="B">Jaipur (Bench)</option>
        </select>

        <input type="hidden" name="type" value="10">

        <button type="submit">Create Officer Account</button>

    </form>

    <p><a href="/HC-EPASS-MVC/public/index.php?r=auth/userList">← Back to Users</a></p>

</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
