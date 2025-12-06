<?php include __DIR__ . '/../layouts/header.php'; ?>

<style>
    body {
        padding: 0px !important;
        margin: 0px !important;
        font-family: Arial, sans-serif;
    }

    .officer-container {
        max-width: 1250px;
        margin: 25px auto;
        padding: 25px;
    }

    /* TITLE */
    .officer-container h2 {
        font-size: 30px;
        font-weight: 700;
        margin-bottom: 25px;
        color: #111827;
    }

    /* GRID OF CARDS */
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 25px;
    }

    /* BOX STYLE */
    .dashboard-card {
        background: white;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: .2s ease;
    }

    .dashboard-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 5px 18px rgba(0, 0, 0, 0.12);
    }

    .dashboard-card h3 {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 12px;
        color: #2563eb;
    }

    .dashboard-card p {
        font-size: 15px;
        color: #374151;
    }

    /* SEARCH BAR */
    .search-box {
        margin-top: 40px;
        background: white;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .search-box h3 {
        margin-bottom: 15px;
        font-size: 22px;
    }

    .search-box input {
        width: 100%;
        padding: 11px 13px;
        font-size: 16px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        background: #f9fafb;
    }

    .search-btn {
        margin-top: 15px;
        background: #2563eb;
        color: white;
        padding: 12px;
        border: none;
        width: 100%;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
    }

    .search-btn:hover {
        background: #1d4ed8;
    }

    /* PROFILE BOX */
    .profile-box {
        margin-top: 35px;
        background: #ffffffcc;
        padding: 20px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
    }

    .profile-box b {
        color: #111827;
    }
</style>

<div class="officer-container">

    <!-- <h2>Officer Dashboard</h2> -->

    <!-- GRID BUTTONS -->
    <div class="dashboard-grid">

        <div class="dashboard-card" onclick="window.location.href='/HC-EPASS-MVC/public/index.php?r=pass/generate&type=advocate';">
            <h3>Advocate Pass</h3>
            <p>Generate pass for Advocate</p>
        </div>

        <!-- <div class="dashboard-card" onclick="window.location.href='/HC-EPASS-MVC/public/index.php?r=pass/generate&type=sr_advocate';">
            <h3>Senior Advocate</h3>
            <p>Generate pass for Senior Advocate</p>
        </div> -->

        <div class="dashboard-card" onclick="window.location.href='/HC-EPASS-MVC/public/index.php?r=pass/generate&type=litigant';">
            <h3>Litigant Pass</h3>
            <p>Generate pass for litigants</p>
        </div>

        <!-- <div class="dashboard-card" onclick="window.location.href='/HC-EPASS-MVC/public/index.php?r=pass/generate&type=court';">
            <h3>Court Pass</h3>
            <p>For courtrooms, items & dates</p>
        </div>

        <div class="dashboard-card" onclick="window.location.href='/HC-EPASS-MVC/public/index.php?r=pass/generate&type=section';">
            <h3>Section Pass</h3>
            <p>Generate pass for office sections</p>
        </div>

        <div class="dashboard-card" onclick="window.location.href='/HC-EPASS-MVC/public/index.php?r=pass/generate&type=vendor';">
            <h3>Vendor Pass</h3>
            <p>Pass for vendors & workers</p>
        </div> -->

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
        <p><b>Officer:</b> <?= htmlspecialchars($_SESSION['admin_user']['name']) ?></p>
        <p><b>Role:</b> <?= htmlspecialchars($_SESSION['admin_user']['role']) ?></p>
        <p><b>Department:</b> <?= htmlspecialchars($_SESSION['admin_user']['department']) ?></p>
    </div>

</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>