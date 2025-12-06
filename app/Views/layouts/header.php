<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>HC E-PASS Admin</title>

    <!-- CSS -->
    <link rel="stylesheet" href="/HC-EPASS-MVC/public/assets/css/header.css">
</head>
<body>

<!-- TRI-COLOR HEADER -->
<header class="tri-header">

    <img src="/HC-EPASS-MVC/public/assets/images/Emblem_of_India.svg" class="ashoka left">

    <div class="center-title">
        <h1>Rajasthan High Court</h1>
        <div class="sub">सत्यमेव जयते</div>
    </div>

    <img src="/HC-EPASS-MVC/public/assets/images/Emblem_of_India.svg" class="ashoka right">
</header>

<!-- BLACK NAV BAR -->
<!-- <nav class="nav-black">
   
    <div class="menu-links">
        <a href="/HC-EPASS-MVC/public/index.php?r=dashboard/index">Dashboard</a>
        <a href="/HC-EPASS-MVC/public/index.php?r=pass/list">Passes</a>

        <?php if(isset($_SESSION['admin_user'])): ?>
            <span>Logged: <?= htmlspecialchars($_SESSION['admin_user']['name']) ?></span>
            <a href="/HC-EPASS-MVC/public/index.php?r=auth/logout">Logout</a>
        <?php endif; ?>
    </div>
</nav> -->
<!-- BLACK NAV BAR -->
<nav class="nav-black">
   
    <?php if($_SESSION['admin_user']['role_id'] == 2): ?>
    <!-- ADMIN MENU -->
    <a href="/HC-EPASS-MVC/public/index.php?r=dashboard/index">Dashboard</a>
    <a href="/HC-EPASS-MVC/public/index.php?r=pass/list">Passes</a>
    <a href="/HC-EPASS-MVC/public/index.php?r=auth/userList">Users</a>
     <a href="/HC-EPASS-MVC/public/index.php?r=auth/logout">Logout</a>

<?php elseif($_SESSION['admin_user']['role_id'] == 10): ?>
    <!-- OFFICER MENU -->
    <a href="/HC-EPASS-MVC/public/index.php?r=officer/dashboard">Dashboard</a>
     <a href="/HC-EPASS-MVC/public/index.php?r=auth/logout">Logout</a>
    <!-- <a href="/HC-EPASS-MVC/public/index.php?r=pass/generate&type=advocate">Generate Pass</a> -->

<?php endif; ?>
</nav>


<main class="page-content">
