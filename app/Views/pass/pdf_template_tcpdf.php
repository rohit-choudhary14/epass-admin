<?php
$case = $p['cino'];
$passno = $p['pass_no'];
$peti = $p['party_name'] ?: "-";
$resp = $p['party_type'] ?: "-";
$valid = date("d/m/Y", strtotime($p['causelist_dt']));
$gen = date("d/m/Y", strtotime($p['entry_dt']));
$adv = $p['paddress'] ?: "-";
?>

<style>
body { font-family: helvetica; font-size: 11px; }

.table {
    width: 100%;
    border-collapse: collapse;
}

.table td, .table th {
    border: 1px solid #000;
    padding: 5px;
}

.heading-blue {
    background: #dce6f7;
    font-weight: bold;
    padding: 5px;
    border: 1px solid #000;
}
</style>

<!-- OUTER BORDER -->
<table width="100%" style="border:1px solid #000; padding:10px;">
<tr><td>

    <!-- HEADER CENTER TEXT ONLY -->
    <table width="100%">
        <tr>
            <td width="15%"></td>

            <td width="70%" align="center">
                <div style="font-size:20px; font-weight:bold;">
                    RAJASTHAN HIGH COURT BENCH JAIPUR
                </div>
                <div style="font-size:14px; font-weight:bold;">
                    ePass Details
                </div>
                (Pass valid for: <b><?= $valid ?></b> only)
            </td>

            <td width="15%"></td>
        </tr>
    </table>

    <br>

    <!-- PASS FOR ADVOCATE TITLE -->
    <div class="heading-blue">Pass for Advocate</div>

    <table class="table">
        <tr>
            <th width="25%">Case Details</th>
            <td width="25%"><?= $case ?></td>
            <th width="25%">Pass Number</th>
            <td width="25%"><?= $passno ?></td>
        </tr>

        <tr>
            <th>Petitioner</th>
            <td><?= $peti ?></td>
            <th>Respondent</th>
            <td><?= $resp ?></td>
        </tr>
    </table>

    <!-- EPASS DETAILS HEADER -->
    <div class="heading-blue">ePass Details</div>

    <!-- EPASS DETAILS BLOCK -->
    <table class="table">
        <tr>
            <td width="75%">
                This entry pass is issued for <b><?= $adv ?></b>
                and valid for case hearing on <b><?= $valid ?></b>.
            </td>

            <td width="25%" align="center">
                <b>Pass Generation Date</b><br>
                <?= $gen ?>
            </td>
        </tr>
    </table>

</td></tr>
</table>
