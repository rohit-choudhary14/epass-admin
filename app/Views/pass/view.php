<?php include __DIR__ . '/../layouts/header.php'; ?>
<style>
    :root {
        --bg: #eef1f7;
        --card: #ffffff;
        --text: #1b3a57;
        --muted: #6c757d;
        --accent: #0d6efd;
    }

    body {
        background: var(--bg);
        font-family: "Inter", "Segoe UI", sans-serif;
        color: var(--text);
        /* padding: 20px; */
        transition: background .25s, color .25s;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Dark mode */
    body.dark {
        --bg: #0f1720;
        --card: #081125;
        --text: #e6eef8;
        --muted: #9aa6b2;
        --accent: #3ea0ff;
    }

    .pass-wrapper {
        max-width: 980px;
        margin: 24px auto;
        background: var(--card);
        padding: 28px;
        border-radius: 14px;
        position: relative;
        box-shadow: 0 6px 26px rgba(17, 24, 39, 0.12);
    }

    /* Seal watermark */
    .pass-wrapper::before {
        content: "";
        position: absolute;
        inset: 0;
        background: url('/HC-EPASS-MVC/public/assets/images/rajasthan_seal.png') center/260px no-repeat;
        opacity: 0.06;
        z-index: 0;
    }

    .content {
        position: relative;
        z-index: 2;
    }

    .header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .header-left h2 {
        margin: 0;
        font-weight: 800;
        font-size: 26px;
    }

    .meta-badges .badge {
        font-weight: 700;
    }

    .details-grid {
        margin-top: 18px;
    }

    .detail-label {
        color: var(--muted);
        font-weight: 700;
    }

    .detail-value {
        font-weight: 600;
        color: var(--text);
    }

    .photo {
        width: 110px;
        height: 110px;
        border-radius: 10px;
        object-fit: cover;
        border: 2px solid rgba(0, 0, 0, 0.06);
        background: #f6f8fb;
    }

    /* timeline */
    .timeline {
        border-left: 3px solid rgba(13, 110, 253, 0.12);
        padding-left: 18px;
        margin-top: 10px;
    }

    .timeline-item {
        margin-bottom: 14px;
        position: relative;
    }

    .timeline-item::before {
        content: "";
        position: absolute;
        left: -10px;
        top: 6px;
        width: 12px;
        height: 12px;
        background: var(--accent);
        border-radius: 50%;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.08);
    }

    /* signature */
    .sig-pad {
        border: 1px dashed rgba(0, 0, 0, 0.12);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.02);
        touch-action: none;
    }

    /* actions area */
    .actions {
        margin-top: 18px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: center;
    }

    /* print hide controls */
    @media print {
        .no-print {
            display: none !important;
        }

        .pass-wrapper {
            box-shadow: none;
            border-radius: 0;
        }
    }

    /* responsive tweaks */
    @media (max-width:720px) {
        .header-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .photo {
            width: 90px;
            height: 90px;
        }
    }
</style>

<div class="pass-wrapper">
    <div class="content">

        <!-- Header -->
        <div class="header-row">
            <div class="header-left">
                <h2>Pass #<?php echo htmlspecialchars($p['pass_no'] ?? '—'); ?></h2>
                <div class="meta-badges mt-2">
                    <!-- Status badge -->
                    <?php
                    // determine status
                    $today = new DateTimeImmutable('now', new DateTimeZone('Asia/Kolkata'));
                    $status = $p['status'] ?? null;
                    // if valid_till provided, compute status
                    if (!$status && !empty($p['valid_till'])) {
                        try {
                            $vt = new DateTimeImmutable($p['valid_till']);
                            $status = ($vt >= $today) ? 'Active' : 'Expired';
                        } catch (Exception $e) {
                            $status = 'Unknown';
                        }
                    }
                    if (!$status && !empty($p['entry_dt'])) {
                        // fallback: active if entry date >= today-1
                        try {
                            $ed = new DateTimeImmutable($p['entry_dt']);
                            $interval = $today->diff($ed)->days;
                            $status = ($ed <= $today) ? 'Active' : 'Active';
                        } catch (Exception $e) {
                            $status = 'Unknown';
                        }
                    }
                    $status = $status ?? 'Unknown';
                    $badgeClass = ($status === 'Active') ? 'bg-success text-white' : (($status === 'Expired') ? 'bg-danger text-white' : 'bg-secondary text-white');
                    ?>
                    <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($status); ?></span>

                    <!-- Pass date -->
                    <span class="badge bg-light text-dark"><?php echo htmlspecialchars($p['entry_dt_str'] ?? '—'); ?></span>

                    <!-- Dark mode toggle (no-print) -->
                    <button id="darkToggle" class="btn btn-sm btn-outline-secondary ms-2 no-print">🌙 Dark</button>
                </div>
            </div>

            <!-- Right: Photo + QR -->
            <div class="header-right d-flex align-items-center gap-3">
                <!-- Photo -->
                <?php
                // $photo = $p['photo_url'] ?? '/HC-EPASS-MVC/public/assets/images/placeholder.png';
                $photo = $p['photo_url'] ?? "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAADICAMAAACahl6sAAAAIVBMVEUAAP8AAAD/AAD///8A/wD//wAA//8A/wD/AAD//wAAAP8A/wB6wVdcAAABzUlEQVR4nO3aTU7DMBiF4eAYmP//mw7CDBXUilX8WZHhpT2BEuqif2BqyS2AATfcPAfgC3a7PznXfiYmJiYmJiYmJiYmJiYmJiYmJiYmJiYmJiYmJiYmJiYmJiY2KW3AOy5p7RnNsZo/G8PRZz0JzoV8j2xn+Fa3p64vCFAHNDaHfAz7bgL8EaPYNx3+0EvgFatgL3H2oIuAtbbDa0k08IY3gWWMRtHPmFljEbQz5hZYxG0M+YWZiadjF6O5ws8Gw6Yfg8Gx+DeeBz4LB8HnWgPkHsGdbAfkedB7BHWgPVDfK/HcV4LpmF2Kzxj5BM24vgjjF8A/wQgONuCeMSwB8BGA4G4J4xPAHME4DsCeMfwHwA4Dcb4h4xHAHwQYPgbgnjEcAfBBg+BvD1Y/4GIFp8TjNgbruvUTk4lnEpZZm4l5PrVDLUV75tLgvSq4B7L6+IGivYhyIfl8Go1OfhvD6hxU4/QxOH7nkVN8B6n/texg2pAbmPT4/HPdHp8x2biYmJiYmJiYmJiYmJiYmJiYmJiYmJiYmJiYmJiYmJiYmJiYn5F7xC/qLvRVKbAAAAAElFTkSuQmCC";

                ?>
                <img alt="Photo" class="photo" src="<?php echo htmlspecialchars($photo); ?>">

                <!-- QR (generate from pass public URL or pass id) -->
                <?php
                $passUrl = $p['public_url'] ?? ("https://example.com/pass/view?id=" . urlencode($p['id'] ?? ''));
                $qrUrl = "https://chart.googleapis.com/chart?cht=qr&chs=220x220&chl=" . rawurlencode($passUrl);
                ?>
                <img alt="QR" style="width:86px;height:86px;border-radius:8px;" src="<?php echo $qrUrl; ?>">
            </div>
        </div>

        <!-- Main details -->
        <div class="row details-grid mt-3">
            <div class="col-md-8">
                <div class="card border-0" style="background:transparent">
                    <div class="card-body p-0">
                        <div class="row mb-2">
                            <div class="col-4 detail-label">CINO</div>
                            <div class="col-8 detail-value"><?php echo htmlspecialchars($p['cino'] ?? '—'); ?></div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-4 detail-label">Advocate (Enroll)</div>
                            <div class="col-8 detail-value"><?php echo htmlspecialchars($p['adv_enroll'] ?? '—'); ?></div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-4 detail-label">Pass For</div>
                            <div class="col-8 detail-value"><?php echo htmlspecialchars($p['passfor'] ?? '—'); ?></div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-4 detail-label">Court / Item</div>
                            <div class="col-8 detail-value"><?php echo htmlspecialchars(($p['court_no'] ?? '—') . ' / ' . ($p['item_no'] ?? '—')); ?></div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-4 detail-label">Address</div>
                            <div class="col-8 detail-value"><?php echo nl2br(htmlspecialchars($p['paddress'] ?? '—')); ?></div>
                        </div>

                        <?php if (!empty($p['remarks'])): ?>
                            <div class="row mb-2">
                                <div class="col-4 detail-label">Remarks</div>
                                <div class="col-8 detail-value"><?php echo nl2br(htmlspecialchars($p['remarks'])); ?></div>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

            <!-- Right column: timeline + status details -->
            <div class="col-md-4">
                <div class="card p-2" style="border-radius:10px;">
                    <div class="card-body p-2">
                        <h6 class="mb-2">Pass Details</h6>
                        <dl class="row">
                            <dt class="col-5 detail-label">ID</dt>
                            <dd class="col-7 detail-value"><?php echo htmlspecialchars($p['id'] ?? '—'); ?></dd>

                            <dt class="col-5 detail-label">Valid Till</dt>
                            <dd class="col-7 detail-value"><?php echo htmlspecialchars($p['valid_till'] ?? '—'); ?></dd>

                            <dt class="col-5 detail-label">Issued By</dt>
                            <dd class="col-7 detail-value"><?php echo htmlspecialchars($p['issued_by'] ?? 'Admin'); ?></dd>
                        </dl>

                        <hr>

                        <h6 class="mb-2">Timeline</h6>
                        <div class="timeline" id="timeline">
                            <?php
                            // example timeline structure: $p['timeline'] = [
                            //   ['label'=>'Created','by'=>'System','dt'=>'2025-05-01 10:12','note'=>'...'],
                            // ];
                            $timeline = $p['timeline'] ?? null;
                            if (!$timeline) {
                                // provide safe defaults
                                $timeline = [
                                    ['label' => 'Pass Requested', 'by' => 'User', 'dt' => ($p['entry_dt_str'] ?? date('Y-m-d')), 'note' => 'Request received'],
                                    ['label' => 'Approved', 'by' => 'Admin', 'dt' => ($p['entry_dt_str'] ?? date('Y-m-d')), 'note' => 'Approved by gate control'],
                                ];
                            }
                            foreach ($timeline as $t) {
                                $tlabel = htmlspecialchars($t['label'] ?? 'Event');
                                $tby = htmlspecialchars($t['by'] ?? '');
                                $tdt = htmlspecialchars($t['dt'] ?? '');
                                $tnote = htmlspecialchars($t['note'] ?? '');
                                echo "<div class=\"timeline-item\"><div><strong>{$tlabel}</strong> <small class=\"text-muted\">by {$tby}</small></div><div class=\"text-muted small\">{$tdt}</div><div class=\"small mt-1\">{$tnote}</div></div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Signature area -->
        <div class="mt-4">
            <h6>Signature (Sign below)</h6>
            <div class="row">
                <div class="col-md-8">
                    <canvas id="sigCanvas" class="sig-pad" width="700" height="160"></canvas>
                    <div class="mt-2 d-flex gap-2 no-print">
                        <button id="sigClear" class="btn btn-outline-secondary btn-sm">Clear</button>
                        <button id="sigDownload" class="btn btn-primary btn-sm">Download PNG</button>
                        <button id="sigFillSample" class="btn btn-outline-info btn-sm">Sample</button>
                    </div>
                    <div class="form-text text-muted mt-1">Signature will be printed on the pass. Use mouse or touch.</div>
                </div>

                <div class="col-md-4">
                    <div class="card p-2" style="border-radius:10px;">
                        <div class="card-body p-2">
                            <h6>Signer</h6>
                            <div class="small text-muted">Name</div>
                            <div class="mb-2"><?php echo htmlspecialchars($p['signed_by'] ?? '—'); ?></div>
                            <div class="small text-muted">Designation</div>
                            <div><?php echo htmlspecialchars($p['signed_designation'] ?? '—'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="actions no-print">
            <button onclick="window.print()" class="btn btn-dark">🖨 Print</button>
            <a href="/HC-EPASS-MVC/public/index.php?r=pass/pdf&id=<?php echo rawurlencode($p['id'] ?? ''); ?>"
                class="btn btn-danger">⬇ PDF</a>
            <a href="/HC-EPASS-MVC/public/index.php?r=pass/list" class="btn btn-outline-primary">← Back to List</a>
        </div>

    </div>
</div>

<!-- JS: signature pad + dark toggle -->
<script>
    // Dark mode toggle (persist in localStorage)
    (function() {
        const btn = document.getElementById('darkToggle');
        const root = document.body;
        const saved = localStorage.getItem('hc_epass_dark') === '1';
        if (saved) root.classList.add('dark');
        btn.addEventListener('click', () => {
            root.classList.toggle('dark');
            localStorage.setItem('hc_epass_dark', root.classList.contains('dark') ? '1' : '0');
            btn.textContent = root.classList.contains('dark') ? '☀ Light' : '🌙 Dark';
        });
        // set initial label
        btn.textContent = root.classList.contains('dark') ? '☀ Light' : '🌙 Dark';
    })();

    // Signature pad (simple)
    (function() {
        const canvas = document.getElementById('sigCanvas');
        const ctx = canvas.getContext('2d');
        let drawing = false;
        let last = {
            x: 0,
            y: 0
        };

        function resizeCanvas() {
            // maintain crispness for printing: use CSS size but keep internal resolution
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            const w = canvas.width;
            const h = canvas.height;
            canvas.width = w * ratio;
            canvas.height = h * ratio;
            canvas.style.width = w + "px";
            canvas.style.height = h + "px";
            ctx.scale(ratio, ratio);
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.lineWidth = 2.5;
            ctx.strokeStyle = getComputedStyle(document.body).color || '#111';
        }
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);

        function p(e) {
            const rect = canvas.getBoundingClientRect();
            return {
                x: (e.touches ? e.touches[0].clientX : e.clientX) - rect.left,
                y: (e.touches ? e.touches[0].clientY : e.clientY) - rect.top
            };
        }

        canvas.addEventListener('pointerdown', function(evt) {
            drawing = true;
            last = p(evt);
        });
        canvas.addEventListener('pointermove', function(evt) {
            if (!drawing) return;
            const pt = p(evt);
            ctx.beginPath();
            ctx.moveTo(last.x, last.y);
            ctx.lineTo(pt.x, pt.y);
            ctx.stroke();
            last = pt;
        });
        ['pointerup', 'pointercancel', 'pointerleave'].forEach(ev => {
            canvas.addEventListener(ev, () => drawing = false);
        });

        // buttons
        document.getElementById('sigClear').addEventListener('click', function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        });

        document.getElementById('sigDownload').addEventListener('click', function() {
            // convert to PNG and download
            const dataUrl = canvas.toDataURL('image/png');
            const a = document.createElement('a');
            a.href = dataUrl;
            a.download = 'signature_pass_<?php echo htmlspecialchars($p['pass_no'] ?? ''); ?>.png';
            document.body.appendChild(a);
            a.click();
            a.remove();
        });

        document.getElementById('sigFillSample').addEventListener('click', function() {
            // simple sample stroke
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.beginPath();
            ctx.moveTo(20, 90);
            ctx.quadraticCurveTo(120, 10, 260, 80);
            ctx.moveTo(260, 80);
            ctx.quadraticCurveTo(340, 120, 410, 60);
            ctx.stroke();
        });

    })();
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>