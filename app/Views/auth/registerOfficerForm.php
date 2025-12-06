<?php 
if (!isset($error)) $error=''; 
if (!isset($success)) $success='';
include __DIR__ . '/../layouts/header.php';
?>

<style>
body {
    background:#f3f4f6;
    font-family:"Inter","Segoe UI",sans-serif;
}

/* CONTAINER BOX */
.officer-box {
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
.officer-box h2 {
    text-align: center;
    font-size: 30px;
    font-weight: 700;
    margin-bottom: 25px;
    color:#1f2937;
}

/* ERROR */
.err {
    background: #fee2e2;
    color: #b91c1c;
    padding: 10px 12px;
    border-radius: 6px;
    margin-bottom: 15px;
    border: 1px solid #fca5a5;
}

/* SUCCESS */
.success {
    background: #dcfce7;
    color: #166534;
    padding: 10px 12px;
    border-radius: 6px;
    margin-bottom: 15px;
    border: 1px solid #86efac;
}

/* LABEL */
.officer-box label {
    display:block;
    margin-top:15px;
    font-size:14px;
    font-weight:600;
    color:#374151;
}

/* INPUT / SELECT */
.officer-box input,
.officer-box select,
.officer-box textarea {
    width:100%;
    padding:11px 13px;
    font-size:15px;
    border-radius:8px;
    border:1px solid #d1d5db;
    margin-top:6px;
    background:#f9fafb;
}

/* BUTTON */
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
    cursor:pointer;
    transition:.2s;
}
.officer-box button:hover {
    background:#1d4ed8;
}

.officer-box p {
    text-align:center;
    margin-top:20px;
}

.officer-box a {
    color:#2563eb;
    font-weight:600;
    text-decoration:none;
}

/* ANIMATION */
@keyframes fadeIn {
    from { opacity:0; transform: translateY(20px); }
    to   { opacity:1; transform: translateY(0); }
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

    <!-- Officer type fixed = 10 -->
    <input type="hidden" name="type" value="10">

    <button type="submit">Create Officer Account</button>
</form>


    <p><a href="/HC-EPASS-MVC/public/index.php?r=auth/userList">← Back to Users</a></p>

</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
