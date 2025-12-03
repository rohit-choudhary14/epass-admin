<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>HC E-PASS Admin</title>
    <link rel="stylesheet" href="/HC-EPASS-MVC/public/assets/css/styles.css">
    
</head>
<body>
<header style="background:#2b6ca3;color:#fff;padding:12px;">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <strong>HC E-PASS</strong> &nbsp;
            <a href="/HC-EPASS-MVC/public/index.php?r=dashboard/index" style="color:#fff">Dashboard</a> |
            <a href="/HC-EPASS-MVC/public/index.php?r=pass/list" style="color:#fff">Passes</a>
        </div>
        <div>
            <?php if(isset($_SESSION['admin_user'])): ?>
                Logged: <?php echo htmlspecialchars($_SESSION['admin_user']['name']); ?> |
                <a href="/HC-EPASS-MVC/public/index.php?r=auth/logout" style="color:#fff">Logout</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<main style="padding:16px;">
