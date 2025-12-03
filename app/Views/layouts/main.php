<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>HC E-Pass Admin</title>
    <link rel="stylesheet" href="/hc-epass-mvc/public/assets/css/styles.css">
</head>
<body>
    <header>
        <h1>HC E-Pass Admin</h1>
        <nav>
            <a href="index.php">Dashboard</a> |
            <a href="index.php?r=pass/list">Passes</a> |
            <a href="index.php?r=auth/logout">Logout</a>
        </nav>
    </header>
    <main>
        <?php if (isset($content)) echo $content; ?>
    </main>
</body>
</html>
