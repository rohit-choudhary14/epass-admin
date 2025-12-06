<?php include __DIR__ . '/../layouts/header.php'; ?>

<style>
    /* GLOBAL */
    body {
        margin: 0;
        background: #f1f3f5;
        font-family: Arial, sans-serif;
    }

    /* LAYOUT WRAPPER */
    .dashboard-container {
        display: flex;
        gap: 20px;
    }

    /* ========== SIDEBAR ========== */
    .sidebar {
        width: 240px;
        background: #1f2937;
        color: white;
        padding: 18px;
        border-radius: 8px;
        height: calc(100vh - 70px);
        position: sticky;
        top: 70px;
        transition: 0.3s;
    }

    .sidebar h3 {
        margin-bottom: 14px;
        font-size: 20px;
    }

    .sidebar a {
        display: block;
        padding: 10px;
        background: #374151;
        color: #fff;
        margin-bottom: 6px;
        border-radius: 6px;
        text-decoration: none;
    }

    .sidebar a:hover {
        background: #4b5563;
    }

    /* MOBILE SIDEBAR BUTTON */
    .mobile-menu-btn {
        display: none;
        padding: 10px 14px;
        background: #1f2937;
        color: white;
        border: none;
        border-radius: 6px;
        margin-bottom: 10px;
    }

    /* Show sidebar as popup on mobile */
    .sidebar.mobile-hidden {
        display: none;
        position: absolute;
        top: 60px;
        left: 10px;
        z-index: 999;
        width: 80%;
    }

    .sidebar.mobile-visible {
        display: block !important;
    }

    /* ========== MAIN CONTENT ========== */
    .main {
        flex: 1;
        padding-bottom: 40px;
    }

    .page-title {
        font-size: 23px;
        margin-bottom: 12px;
    }

    /* ========== CARDS ========== */
    .cards-grid {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }

    .stat-card {
        flex: 1;
        min-width: 180px;
        padding: 18px;
        border-radius: 10px;
        color: white;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.12);
    }

    .stat-card .count {
        font-size: 28px;
        font-weight: bold;
    }

    .stat-card.total {
        background: #2E86C1;
    }

    .stat-card.today {
        background: #1ABC9C;
    }

    .stat-card.week {
        background: #F39C12;
    }

    .stat-card.month {
        background: #8E44AD;
    }

    .stat-card.section {
        background: #6c757d;
    }

    .stat-card.section-today {
        background: #20c997;
    }

    /* ========== FILTER BOX ========== */
    .filters-box {
        background: white;
        margin: 20px 0;
        padding: 14px;
        border-radius: 8px;
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.09);
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .filters-box input,
    .filters-box select {
        padding: 8px 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
    }

    .btn-dark {
        padding: 8px 14px;
        background: #1f2937;
        border-radius: 6px;
        color: white;
        border: none;
    }

    /* ========== CHARTS ========== */
    .charts-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .chart-box {
        flex: 1;
        min-width: 260px;
        background: white;
        padding: 16px;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    /* ========== TABLE ========== */
    .table-box {
        margin-top: 20px;
        background: white;
        padding: 16px;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    .table-responsive {
        overflow-x: auto;
        width: 100%;
    }

    .table-modern {
        width: 100%;
        min-width: 800px;
        border-collapse: collapse;
    }

    .table-modern th {
        background: #f3f4f6;
        padding: 10px;
        text-align: left;
        border-bottom: 2px solid #ddd;
    }

    .table-modern td {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }

    .table-modern tr:hover {
        background: #f9fafb;
    }

    /* Pagination */
    .pagination {
        margin-top: 10px;
    }

    .pagination a {
        padding: 8px 14px;
        background: #2E86C1;
        color: white;
        margin-right: 6px;
        border-radius: 6px;
        text-decoration: none;
    }

    .pagination a:hover {
        background: #1B4F72;
    }

    /* ========== MOBILE RESPONSIVE ========== */
    @media(max-width: 900px) {
        .dashboard-container {
            flex-direction: column;
        }

        .sidebar {
            display: none;
        }

        .mobile-menu-btn {
            display: block;
        }
    }

    @media(max-width: 500px) {
        .stat-card .count {
            font-size: 22px;
        }

        .chart-box {
            min-height: 260px;
        }
    }
</style>

<!-- Mobile Menu Button -->
<button class="mobile-menu-btn" onclick="toggleSidebar()">☰ Menu</button>

<style>
    :root {
        --bg: #f1f3f5;
        --card: #fff;
        --muted: #6b7280;
        --accent: #2E86C1;
        --text: #111827;
    }

    [data-theme="dark"] {
        --bg: #0f1724;
        --card: #0b1220;
        --muted: #94a3b8;
        --accent: #60a5fa;
        --text: #e6eef8;
    }

    body {
        background: var(--bg);
        color: var(--text);
        font-family: Arial, sans-serif;
    }

    /* header row */
    .header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .header-left {
        display: flex;
        flex-direction: column;
    }

    .header-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    /* small icon button */
    .icon-btn {
        background: transparent;
        border: 1px solid rgba(0, 0, 0, 0.06);
        padding: 8px 10px;
        border-radius: 8px;
        cursor: pointer;
        color: var(--text);
    }

    .icon-btn:hover {
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
    }

    /* counter style (keeps previous card styles) */
    .stat-card .count {
        font-size: 28px;
        font-weight: 700;
    }

    /* leaderboard */
    .leaderboard {
        background: var(--card);
        padding: 12px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .leaderboard h4 {
        margin: 0 0 8px 0;
        font-size: 15px;
    }

    .leaderboard ol {
        padding-left: 18px;
        margin: 0;
        color: var(--muted);
    }

    /* small print friendly tweak for PDF export */
    @media print {

        .sidebar,
        .icon-btn,
        .filters-box,
        .header-actions {
            display: none !important;
        }

        body {
            background: #fff !important;
        }
    }
</style>

<div class="header-row">


    <div class="header-actions">
        <!-- Auto-refresh toggle -->
        <label style="display:flex;align-items:center;gap:8px">
            <input id="autoRefreshToggle" type="checkbox"> <span style="font-size:13px;color:var(--muted)">Auto refresh</span>
        </label>

        <!-- Dark mode -->
        <button id="themeToggle" class="icon-btn" title="Toggle dark mode">🌗 Theme</button>

        <!-- Export buttons -->
        <button id="exportCsvBtn" class="icon-btn" title="Export CSV">⬇ CSV</button>
        <button id="exportPdfBtn" class="icon-btn" title="Export PDF / Print">🖨 PDF</button>
    </div>
</div>

<!-- rest of existing dashboard markup follows unchanged -->

<div class="dashboard-container">

    <!-- Sidebar -->
    <div id="sidebar" class="sidebar mobile-hidden">
        <h3>Admin Panel</h3>
        <a href="?r=dashboard/index">Dashboard</a>
        <a href="?r=pass/list">All Passes</a>
        <a href="?r=auth/registerForm">Add Admin</a>
        <a href="?r=auth/logout">Logout</a>
    </div>

    <!-- MAIN -->
    <div class="main">

        <h2 class="page-title">
            Welcome, <?php echo htmlspecialchars($_SESSION['admin_user']['name']); ?>
        </h2>

        <!-- CARDS -->
        <div class="cards-grid">
            <div class="stat-card total">
                <div class="count"><?php echo $stats['total']; ?></div>Total Passes
            </div>
            <div class="stat-card today">
                <div class="count"><?php echo $stats['today']; ?></div>Today
            </div>
            <div class="stat-card week">
                <div class="count"><?php echo $stats['week']; ?></div>Last 7 Days
            </div>
            <div class="stat-card month">
                <div class="count"><?php echo $stats['month']; ?></div>This Month
            </div>
            <div class="stat-card section">
                <div class="count"><?php echo $stats['section_total']; ?></div>Section Total
            </div>
            <div class="stat-card section-today">
                <div class="count"><?php echo $stats['section_today']; ?></div>Section Today
            </div>
        </div>

        <!-- FILTERS -->
        <form method="get" class="filters-box">
            <input type="hidden" name="r" value="dashboard/index">
            <input type="date" name="from" value="<?php echo $filters['from'] ?? ''; ?>">
            <input type="date" name="to" value="<?php echo $filters['to'] ?? ''; ?>">
            <input type="text" name="adv" placeholder="Adv Enroll" value="<?php echo $filters['adv'] ?? ''; ?>">
            <input type="text" name="cino" placeholder="CINO" value="<?php echo $filters['cino'] ?? ''; ?>">
            <input type="text" name="pass_no" placeholder="Pass No" value="<?php echo $filters['pass_no'] ?? ''; ?>">
            <select name="passfor">
                <option value="">All</option>
                <option value="S">Senior/Adv</option>
                <option value="L">Litigant</option>
            </select>
            <button class="btn-dark">Apply</button>
        </form>

        <!-- CHARTS -->
        <div class="charts-grid">
            <div class="chart-box">
                <h4>Passes – Last 30 Days</h4>
                <canvas id="chart30"></canvas>
            </div>

            <div class="chart-box">
                <h4>Passes by Type</h4>
                <canvas id="typeChart"></canvas>
            </div>
        </div>

        <!-- TABLE -->
        <div class="table-box">
            <h4>Recent / Filtered Passes (<?php echo $totalRows; ?>)</h4>
            <div class="table-responsive">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pass No</th>
                            <th>CINO</th>
                            <th>Date</th>
                            <th>For</th>
                            <th>Adv</th>
                            <th>Court/Item</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rows)): ?>
                            <tr>
                                <td colspan="7">No records</td>
                            </tr>
                            <?php else: foreach ($rows as $r): ?>
                                <tr>
                                    <td><?php echo $r['id']; ?></td>
                                    <td><?php echo $r['pass_no']; ?></td>
                                    <td><?php echo $r['cino']; ?></td>
                                    <td><?php echo $r['entry_dt']; ?></td>
                                    <td><?php echo $r['passfor']; ?></td>
                                    <td><?php echo $r['adv_enroll']; ?></td>
                                    <td><?php echo $r['court_no'] . '/' . $r['item_no']; ?></td>
                                </tr>
                        <?php endforeach;
                        endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="<?php echo $this->buildPageUrl($page - 1); ?>">Prev</a>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="<?php echo $this->buildPageUrl($page + 1); ?>">Next</a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>
<script>
    (function() {
        /* ---------- Dark mode persisted ---------- */
        const root = document.documentElement;
        const themeToggle = document.getElementById('themeToggle');
        const saved = localStorage.getItem('hc_theme');
        if (saved === 'dark') document.documentElement.setAttribute('data-theme', 'dark');

        themeToggle.addEventListener('click', function() {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            if (isDark) {
                document.documentElement.removeAttribute('data-theme');
                localStorage.setItem('hc_theme', 'light');
            } else {
                document.documentElement.setAttribute('data-theme', 'dark');
                localStorage.setItem('hc_theme', 'dark');
            }
        });

        /* ---------- Animated counters ---------- */
        function animateCount(el, to) {
            const start = 0;
            const duration = 900;
            let startTime = null;

            function step(ts) {
                if (!startTime) startTime = ts;
                const progress = Math.min((ts - startTime) / duration, 1);
                const val = Math.floor(progress * (to - start) + start);
                el.textContent = val.toLocaleString();
                if (progress < 1) requestAnimationFrame(step);
            }
            requestAnimationFrame(step);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // find the counters by CSS (cards created earlier have .stat-card and first child .count)
            document.querySelectorAll('.stat-card .count').forEach(function(cntEl) {
                // parse value from server-rendered innerText (it is numeric)
                const raw = cntEl.textContent.replace(/,/g, '').trim();
                const numeric = parseInt(raw, 10) || 0;
                cntEl.textContent = '0';
                animateCount(cntEl, numeric);
            });
        });

        /* ---------- Auto refresh (page reload) ---------- */
        const autoToggle = document.getElementById('autoRefreshToggle');
        let autoInterval = null;
        // load persisted state
        if (localStorage.getItem('hc_auto_refresh') === '1') {
            autoToggle.checked = true;
            startAutoRefresh();
        }
        autoToggle.addEventListener('change', function() {
            localStorage.setItem('hc_auto_refresh', this.checked ? '1' : '0');
            if (this.checked) startAutoRefresh();
            else stopAutoRefresh();
        });

        function startAutoRefresh() {
            stopAutoRefresh();
            // reload every 45 seconds (safe)
            autoInterval = setInterval(function() {
                location.reload();
            }, 45000);
        }

        function stopAutoRefresh() {
            if (autoInterval) {
                clearInterval(autoInterval);
                autoInterval = null;
            }
        }

        /* ---------- Export handlers ---------- */
        document.getElementById('exportCsvBtn').addEventListener('click', function() {
            // reuse your existing export URL builder (form id=filterForm or current query)
            // If you used filter form with method GET and r=dashboard/export, open that URL
            const params = new URLSearchParams(window.location.search);
            params.set('r', 'dashboard/export');
            const url = 'index.php?' + params.toString();
            window.location = url;
        });

        document.getElementById('exportPdfBtn').addEventListener('click', function() {
            // print-friendly: open print dialog (user can save as PDF)
            window.print();
        });

    })();
</script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // MOBILE SIDEBAR
    function toggleSidebar() {
        let sb = document.getElementById('sidebar');
        if (sb.classList.contains('mobile-hidden')) {
            sb.classList.remove('mobile-hidden');
            sb.classList.add('mobile-visible');
        } else {
            sb.classList.add('mobile-hidden');
            sb.classList.remove('mobile-visible');
        }
    }

    // CHARTS
    let days = <?php echo json_encode(array_column($chart30, 'day')); ?>;
    let totals = <?php echo json_encode(array_column($chart30, 'total')); ?>;

    new Chart(document.getElementById('chart30'), {
        type: 'line',
        data: {
            labels: days,
            datasets: [{
                data: totals,
                borderColor: '#2E86C1',
                backgroundColor: 'rgba(46,134,193,0.15)',
                fill: true
            }]
        }
    });

    let typeLabels = <?php echo json_encode(array_column($byType, 'passtype')); ?>;
    let typeValues = <?php echo json_encode(array_column($byType, 'total')); ?>;

    new Chart(document.getElementById('typeChart'), {
        type: 'bar',
        data: {
            labels: typeLabels,
            datasets: [{
                data: typeValues,
                backgroundColor: ['#1ABC9C', '#F39C12', '#8E44AD', '#2E86C1']
            }]
        }
    });
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>