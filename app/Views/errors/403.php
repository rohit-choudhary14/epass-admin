<!DOCTYPE html>
<html>
<head>
    <title>Access Denied</title>
    <style>
        body {
            font-family: "Inter", sans-serif;
            background: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        .box {
            max-width: 500px;
            margin: 80px auto;
            background: #fff;
            padding: 40px;
            text-align: center;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        h1 {
            font-size: 32px;
            color: #b91c1c;
            margin-bottom: 10px;
        }

        p {
            font-size: 16px;
            color: #4b5563;
        }

        a.btn {
            display: inline-block;
            margin-top: 25px;
            background: #2563eb;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
        }
        a.btn:hover {
            background: #1d4ed8;
        }
    </style>
</head>
<body>

<div class="box">
    <h1>Access Denied</h1>
    <p>You do not have permission to view this page.</p>

    <?php if($_SESSION['admin_user']['role_id'] == 20): ?>
        <a class="btn" href="/HC-EPASS-MVC/public/index.php?r=dashboard/index">Go to Admin Dashboard</a>
    <?php else: ?>
        <a class="btn" href="/HC-EPASS-MVC/public/index.php?r=officer/dashboard">Dashboard</a>
    <?php endif; ?>
</div>

</body>
</html>
