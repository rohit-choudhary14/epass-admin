<?php include __DIR__ . '/../layouts/header.php'; ?>

<style>
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        padding: 0;
        font-family: "Inter", Arial, sans-serif;
        background: #f3f4f6;
    }

    .officer-container {
        font-family: "Inter", sans-serif;
        max-width: 1250px;
        margin: 26px auto;
        padding: 20px;
    }

    .page-title {
        font-size: 32px;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 25px;
        text-align: left;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 28px;
    }

    .dashboard-card {
        background: white;
        border-radius: 14px;
        padding: 30px 20px;
        border: 1px solid #e5e7eb;
        text-align: center;
        cursor: pointer;
        transition: .25s ease-in-out;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
    }

    .dashboard-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 8px 26px rgba(0, 0, 0, 0.12);
    }

    .dashboard-card h3 {
        font-size: 22px;
        font-weight: 700;
        color: #2563eb;
        margin-bottom: 10px;
    }

    .dashboard-card p {
        font-size: 15px;
        color: #475569;
    }

    .search-box {
        margin-top: 40px;
        background: white;
        border-radius: 14px;
        padding: 26px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
    }

    .search-box h3 {
        margin-bottom: 15px;
        font-size: 22px;
        font-weight: 700;
        color: #1e293b;
    }

    .search-box input {
        width: 100%;
        padding: 14px 16px;
        font-size: 16px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        background: #f9fafb;
        transition: .2s ease;
    }

    .search-box input:focus {
        border-color: #2563eb;
        outline: none;
        background: white;
    }

    .search-btn {
        margin-top: 16px;
        background: #2563eb;
        color: white;
        padding: 14px;
        border: none;
        width: 100%;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: .25s ease;
    }

    .search-btn:hover {
        background: #1d4ed8;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.25);
    }

    /* PROFILE BOX */
    .profile-box {
        margin-top: 40px;
        background: white;
        padding: 20px 24px;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
    }

    .profile-box p {
        font-size: 16px;
        margin: 6px 0;
        color: #334155;
    }

    .profile-box b {
        color: #111827;
    }

    /* MOBILE RESPONSIVE */
    @media (max-width: 768px) {
        .page-title {
            font-size: 26px;
        }

        .dashboard-card {
            padding: 22px 18px;
        }

        .search-box,
        .profile-box {
            padding: 20px;
        }
    }
</style>



<div class="officer-container">

    <?php
    $map = [
        "P" => "Jodhpur",
        "B" => "Jaipur"
    ];

    $est = $_SESSION['admin_user']['establishment'] ?? '';
    $est = trim($est);

    $estName = $map[$est] ?? "N/A";
    ?>

    <h2 class="page-title">Officer Dashboard (<?= $estName ?>)</h2>

    <!-- GRID BUTTONS -->
    <div class="dashboard-grid">

        <div class="dashboard-card" onclick="window.location.href='/HC-EPASS-MVC/public/index.php?r=pass/generate&type=advocate';">
            <h3>Advocate Pass</h3>
            <p>Generate pass for advocates</p>
        </div>

        <div class="dashboard-card" onclick="window.location.href='/HC-EPASS-MVC/public/index.php?r=pass/generate&type=litigant';">
            <h3>Litigant Pass</h3>
            <p>Generate pass for litigants</p>
        </div>
        <div class="dashboard-card" onclick="window.location.href='/HC-EPASS-MVC/public/index.php?r=pass/generate&type=partyinperson';">
            <h3>Party In Person</h3>
            <p>Generate pass for Party in person</p>
        </div>
         <div class="dashboard-card" onclick="window.location.href='/HC-EPASS-MVC/public/index.php?r=pass/generate&type=partyinperson';">
            <h3>Vendor Pass</h3>
            <p>Generate pass for Vendor</p>
        </div>
    </div>

    <!-- SEARCH BLOCK -->
    <div class="search-box">
        <h3>Search Generated Passes</h3>
        <form method="get" action="/HC-EPASS-MVC/public/index.php">
            <input type="hidden" name="r" value="pass/searchOfficer">
            <input type="text" name="q" placeholder="Search by Pass No / Name / CINO..." required>
            <button class="search-btn" type="submit">Search</button>
        </form>
    </div>

    <!-- PROFILE INFO -->
    <div class="profile-box">
        <p><b>Officer:</b> <?= htmlspecialchars($_SESSION['admin_user']['name'] ?? 'N/A') ?></p>
        <p><b>Role:</b> <?= htmlspecialchars($_SESSION['admin_user']['role'] ?? 'N/A') ?></p>
        <p><b>Department:</b> <?= htmlspecialchars($_SESSION['admin_user']['department'] ?? 'N/A') ?></p>
    </div>

</div>



<?php include __DIR__ . '/../layouts/footer.php'; ?>