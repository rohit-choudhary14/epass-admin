<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>HC E-PASS Admin</title>
    <!-- jQuery -->
    <script src="/HC-EPASS-MVC/public/assets/js/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="/HC-EPASS-MVC/public/assets/css/bootstrap.min.css">
    <script src="/HC-EPASS-MVC/public/assets/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="/HC-EPASS-MVC/public/assets/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="/HC-EPASS-MVC/public/assets/css/dataTables.bootstrap5.min.css">

    <script src="/HC-EPASS-MVC/public/assets/js/jquery.dataTables.min.js"></script>
    <script src="/HC-EPASS-MVC/public/assets/js/dataTables.bootstrap5.min.js"></script>

    <!-- DataTables Responsive -->
    <link rel="stylesheet" href="/HC-EPASS-MVC/public/assets/css/responsive.dataTables.min.css">
    <script src="/HC-EPASS-MVC/public/assets/js/dataTables.responsive.min.js"></script>

    <!-- Select2 -->
    <link href="/HC-EPASS-MVC/public/assets/css/select2.min.css" rel="stylesheet" />
    <script src="/HC-EPASS-MVC/public/assets/js/select2.min.js"></script>



    <link rel="stylesheet" href="/HC-EPASS-MVC/public/assets/css/header.css">

</head>

<body>
    <style>
        /* FIXED TRI-COLOR HEADER */
        .fixed-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 90px;
            /* adjust if needed */
            z-index: 10000;

        }

        .nav-black {
            position: fixed;
            top: 90px;
            /* SAME as header height */
            left: 0;
            width: 100%;
            background: #000;
            padding: 12px 20px;
            display: flex;
            gap: 18px;
            align-items: center;
            z-index: 9999;
        }

        .page-content {
            padding-top: 150px;
            /* header (90) + nav (~60) */
        }

        .dropdown-content {
            z-index: 10001;
        }

        .nav-black a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropbtn {
            background: none;
            color: white;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #1f2937;
            min-width: 180px;
            border-radius: 6px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            z-index: 999;
        }

        .dropdown-content a {
            color: #fff;
            padding: 10px 15px;
            display: block;
            text-decoration: none;
        }

        .dropdown-content a:hover {
            background-color: #374151;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        #global-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 99999;
        }

        #global-loader .spinner {
            width: 55px;
            height: 55px;
            border: 6px solid #e0e7ff;
            border-top: 6px solid #4f46e5;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    <!-- TRI-COLOR HEADER -->
    <header class="tri-header fixed-header">

        <img src="/HC-EPASS-MVC/public/assets/images/Emblem_of_India.svg" class="ashoka left">

        <div class="center-title">
            <h1>Rajasthan High Court</h1>
            <div class="sub">E Gate Pass Application</div>
        </div>

        <img src="/HC-EPASS-MVC/public/assets/images/logo12102023.png" class="ashoka right">
    </header>

    <nav class="nav-black">
        <?php if (isset($_SESSION['admin_user']) && isset($_SESSION['admin_user']['role_id'])): ?>

            <?php if ($_SESSION['admin_user']['role_id'] == 20): ?>
                <!-- ADMIN MENU -->
                <a href="/HC-EPASS-MVC/public/index.php?r=dashboard/index">Dashboard</a>
                <a href="/HC-EPASS-MVC/public/index.php?r=pass/list">Passes</a>
                <a href="/HC-EPASS-MVC/public/index.php?r=auth/userList">Users</a>
                <a href="/HC-EPASS-MVC/public/index.php?r=auth/logout">Logout</a>

            <?php elseif ($_SESSION['admin_user']['role_id'] == 10): ?>
                <!-- OFFICER MENU -->
                <a href="/HC-EPASS-MVC/public/index.php?r=officer/dashboard">Dashboard</a>

                <!-- DROPDOWN: MY PASSES -->
                <div class="dropdown">
                    <button class="dropbtn">Generated Passes ▾</button>
                    <div class="dropdown-content">
                        <a href="/HC-EPASS-MVC/public/index.php?r=pass/mySectionPasses">Section Visit Passes</a>
                        <a href="/HC-EPASS-MVC/public/index.php?r=pass/myPasses">Court Passes</a>

                    </div>
                </div>
                <a href="javascript:void(0)" id="openEstModal">Change Establishment</a>

                <a href="/HC-EPASS-MVC/public/index.php?r=auth/logout">Logout</a>
            <?php endif; ?>

        <?php else: ?>
            <a href="/HC-EPASS-MVC/public/index.php?r=auth/login">Login</a>
        <?php endif; ?>
    </nav>




    <main class="page-content">
        <!-- UNIVERSAL LOADER -->
        <div id="global-loader">
            <div class="spinner"></div>
        </div>