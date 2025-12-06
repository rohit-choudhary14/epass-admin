<?php
require_once __DIR__ . '/BaseModel.php';

class Court extends BaseModel
{
    public function getCaseTypes()
    {
        $sql = "SELECT case_type, type_name FROM case_type_t ORDER BY type_name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getCaseTypeName($id)
    {
        $sql = "SELECT type_name FROM case_type_t WHERE case_type = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['type_name'] : null;
    }


  


  public function findCourtCase($case_type, $case_no, $case_year, $cl_type, $cl_date)
{
    $cl_date_db = date("dmY", strtotime($cl_date));

    if ($cl_type == 'S') {
        $cltypeCondition = "B.causelisttype NOT IN ('D','W','L')";
    } else {
        $cltypeCondition = "B.causelisttype = :cl_type";
    }

    // Updated SQL: fetch full advocate details
    $sql = "
        SELECT 
            A.cino,

            A.pet_adv,
            A.pet_adv_cd,
            A.pet_mobile,
            A.pet_email,
          

            A.res_adv,
            A.res_adv_cd,
            A.res_mobile,
            A.res_email,
           
           

            B.sno AS item_no,
            B.croom AS court_no

        FROM civil_t A
        INNER JOIN causelistsrno B 
            ON (
                (A.case_no = B.case_no AND B.con_case_no IS NULL)
                OR
                (A.case_no = B.con_case_no AND B.con_case_no IS NOT NULL)
            )
        WHERE 
            A.regcase_type = :ct
            AND A.reg_no = :no
            AND A.reg_year = :yr
            AND $cltypeCondition
            AND B.causelistdate = :dt
            AND B.isfinalized = 'Y'
        LIMIT 1
    ";

    $stmt = $this->pdo->prepare($sql);

    $params = [
        ":ct" => $case_type,
        ":no" => $case_no,
        ":yr" => $case_year,
        ":dt" => $cl_date_db
    ];
    if ($cl_type != 'S') {
        $params[":cl_type"] = $cl_type;
    }

    $stmt->execute($params);
    $case = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$case) {
        return ["status" => "NOT_FOUND", "message" => "No entry in causelist"];
    }

    $cino = $case["cino"];
    $adv_list = [];

    // Mapping for readable labels
    $sideLabel = [
        1 => "Petitioner",
        2 => "Respondent"
    ];

    // PETITIONER ADVOCATE
    if (!empty(trim($case["pet_adv"]))) {

        $adv_list[] = [
            "name"        => trim($case["pet_adv"]),
            "side"        => 1,
            "side_label"  => $sideLabel[1],

            "adv_code"    => $case["pet_adv_cd"],
            "enroll_num"  => $case["pet_adv_cd"],     // Same column used for enrollment
            "mobile"      => $case["pet_mobile"],
            "email"       => $case["pet_email"],
            // "paddress"    => $case["pet_address"]
        ];
    }

    // RESPONDENT ADVOCATE
    if (!empty(trim($case["res_adv"]))) {

        $adv_list[] = [
            "name"        => trim($case["res_adv"]),
            "side"        => 2,
            "side_label"  => $sideLabel[2],

            "adv_code"    => $case["res_adv_cd"],
            "enroll_num"  => $case["res_adv_cd"],
            "mobile"      => $case["res_mobile"],
            "email"       => $case["res_email"],
            // "paddress"    => $case["res_address"]
        ];
    }

    // EXTRA ADVOCATES
    $sql2 = "SELECT adv_name, type FROM extra_adv_t WHERE cino=:cino AND trim(adv_name) <> ''";
    $stmt2 = $this->pdo->prepare($sql2);
    $stmt2->execute([":cino" => $cino]);

    foreach ($stmt2->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (!empty(trim($row["adv_name"]))) {

            // No details exist for extra advocates in civil_t
            $adv_list[] = [
                "name"       => trim($row["adv_name"]),
                "side"       => $row["type"],
                "side_label" => $sideLabel[$row["type"]],
                "mobile"     => null,
                "adv_code"   => null,
                "enroll_num" => null,
                "paddress"   => null
            ];
        }
    }

    // ADDRESS ADVOCATES
    $sql3 = "SELECT adv_name, type FROM civ_address_t WHERE cino=:cino AND display='Y' AND trim(adv_name) <> ''";
    $stmt3 = $this->pdo->prepare($sql3);
    $stmt3->execute([":cino" => $cino]);

    foreach ($stmt3->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (!empty(trim($row["adv_name"]))) {
            $adv_list[] = [
                "name"       => trim($row["adv_name"]),
                "side"       => $row["type"],
                "side_label" => $sideLabel[$row["type"]],
                "mobile"     => null,
                "adv_code"   => null,
                "enroll_num" => null,
                "paddress"   => null
            ];
        }
    }

    return [
        "status"    => "OK",
        "cino"      => $cino,
        "court_no"  => $case["court_no"],
        "item_no"   => $case["item_no"],
        "cl_date"   => $cl_date,
        "cl_type"   => $cl_type,
        "advocates" => $adv_list
    ];
}


    public function generatePass(
        $cino,
        $adv_type,
        $cldt,
        $cltype,
        $courtno,
        $itemno,
        $paddress,
        $partyno,
        $partymob,
        $partynm,
        $party_side
    ) {
        // Convert DD/MM/YYYY or YYYY-MM-DD to YYYY-MM-DD
        $cldt2 = date("Y-m-d", strtotime(str_replace("/", "-", $cldt)));

        // Generate Pass Number
        $passNo = $cltype . date("dmY", strtotime($cldt2)) . $courtno . $itemno . date("His");

        // Officer Details
        $user_id = $_SESSION['user']['id'] ?? 0;
        $user_ip = $_SERVER['REMOTE_ADDR'];

        // Officer always generating pass for advocate => passtype = 2, passfor = S
        $passtype = 2;
        $passfor = 'S';

        $sql = "
        INSERT INTO gatepass_details
        (
            cino, causelist_type, court_no, item_no, pass_no,
            user_id, user_ip, entry_dt, causelist_dt,
            paddress, adv_type, party_no, party_mob_no,
            party_type, passfor, party_name, passtype
        )
        VALUES
        (
            :cino, :cltype, :courtno, :itemno, :passno,
            :user_id, :user_ip, NOW(), :cldt,
            :paddress, :adv_type, :partyno, :partymob,
            :party_type, :passfor, :party_name, :passtype
        )
        RETURNING pass_no
    ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":cino"       => $cino,
            ":cltype"     => $cltype,
            ":courtno"    => $courtno,
            ":itemno"     => $itemno,
            ":passno"     => $passNo,
            ":user_id"    => $user_id,
            ":user_ip"    => $user_ip,
            ":cldt"       => $cldt2,
            ":paddress"   => $paddress,
            ":adv_type"   => $adv_type,
            ":partyno"    => $partyno,
            ":partymob"   => $partymob,
            ":party_type" => $party_side,
            ":passfor"    => $passfor,
            ":party_name" => $partynm,
            ":passtype"   => $passtype
        ]);

        return [
            "status" => "OK",
            "pass_no" => $passNo
        ];
    }
}
