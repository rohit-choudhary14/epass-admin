<?php if (!isset($error)) $error=''; if(!isset($success)) $success=''; ?>
<?php include __DIR__ . '/../layouts/header.php'; ?>
<h2>Register Admin</h2>
<?php if ($error) echo '<div class="err">'.htmlspecialchars($error).'</div>'; ?>
<?php if ($success) echo '<div style="color:green">'.htmlspecialchars($success).'</div>'; ?>
<form method="post" action="/HC-EPASS-MVC/public/index.php?r=auth/registerPost">
    <label>Username</label><input name="username" required>
    <label>Full name</label><input name="name" required>
    <label>Email</label><input name="email" type="email">
    <label>Contact</label><input name="contact">
    <label>Address</label><textarea name="address"></textarea>
    <label>Gender</label>
    <select name="gender"><option value="">--</option><option value="M">M</option><option value="F">F</option></select>
    <label>Password</label><input name="password" type="password" required>
    <button type="submit">Create Admin</button>
</form>
<p><a href="/HC-EPASS-MVC/public/index.php?r=auth/loginForm">Back to login</a></p>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
