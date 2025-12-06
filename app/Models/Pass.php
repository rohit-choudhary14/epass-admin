<?php
require_once __DIR__ . '/BaseModel.php';

class Pass extends BaseModel
{
    
    
    public function list($from = null, $to = null, $limit = null)
{
    $sql = "SELECT id, pass_no, passfor, passtype,
                   to_char(entry_dt,'DD/MM/YYYY') AS entry_dt
            FROM gatepass_details
            WHERE 1=1";
    $params = [];

    if ($from) {
        $sql .= " AND entry_dt::date >= :from";
        $params[':from'] = $from;
    }
    if ($to) {
        $sql .= " AND entry_dt::date <= :to";
        $params[':to'] = $to;
    }

    $sql .= " ORDER BY entry_dt DESC";

    if ($limit !== null) {
        $sql .= " LIMIT :lim";
    }

    try {
        $stmt = $this->pdo->prepare($sql);

        if ($limit !== null) {
            $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
        }

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->execute();
        return $stmt->fetchAll();

    } catch (PDOException $e) {
        error_log("Pass::list error: " . $e->getMessage());
        return [];
    }
}

    
    /**
     * Return paginated list of passes with filters and sorting.
     *
     * @param array $opts keys: from, to, q (search), adv (adv_enroll), sort (col), dir (ASC|DESC), page, perPage
     * @return array ['rows'=>[], 'total'=>int, 'page'=>int, 'perPage'=>int]
     */
    public function listPaginated(array $opts = [])
{
    $from = $opts['from'] ?? null;
    $to   = $opts['to']   ?? null;
    $q    = trim($opts['q'] ?? '');
    $adv  = trim($opts['adv'] ?? '');
    $sort = $opts['sort'] ?? 'entry_dt';
    $dir  = strtoupper($opts['dir'] ?? 'DESC');
    $page = max(1, (int)($opts['page'] ?? 1));
    $perPage = (int)($opts['perPage'] ?? 50);
    $offset = ($page - 1) * $perPage;

    $allowedSort = ['entry_dt','pass_no','id','passfor','passtype','adv_enroll','court_no'];
    if (!in_array($sort, $allowedSort)) $sort = 'entry_dt';

    // BUILD BASE SQL ONLY ONCE
    $where = " WHERE 1=1 ";
    $params = [];

    if ($from) { $where .= " AND entry_dt::date >= :from"; $params[':from'] = $from; }
    if ($to)   { $where .= " AND entry_dt::date <= :to";   $params[':to'] = $to; }
    if ($adv)  { $where .= " AND lower(adv_enroll) LIKE lower(:adv)"; $params[':adv'] = "%$adv%"; }
    if ($q) {
        $where .= " AND (lower(pass_no) LIKE lower(:q) OR lower(cino) LIKE lower(:q))";
        $params[':q'] = "%$q%";
    }

    // COUNT
    $countSql = "SELECT COUNT(*) FROM gatepass_details $where";
    $stmt = $this->pdo->prepare($countSql);
    foreach ($params as $k => $v) $stmt->bindValue($k, $v);
    $stmt->execute();
    $total = (int)$stmt->fetchColumn();

    // DATA
    $sql = "SELECT id, pass_no, passfor, passtype, adv_enroll, court_no, item_no, cino,
                   to_char(entry_dt,'DD/MM/YYYY HH24:MI') AS entry_dt
            FROM gatepass_details
            $where
            ORDER BY $sort $dir
            LIMIT :lim OFFSET :off";

    $stmt = $this->pdo->prepare($sql);
    foreach ($params as $k => $v) $stmt->bindValue($k, $v);
    $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return [
        'rows'    => $stmt->fetchAll(),
        'total'   => $total,
        'page'    => $page,
        'perPage' => $perPage
    ];
}


    public function find($id)
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT *, to_char(entry_dt,'DD/MM/YYYY HH24:MI') AS entry_dt_str
                 FROM gatepass_details WHERE id = :id LIMIT 1"
            );
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Pass::find " . $e->getMessage());
            return null;
        }
    }

    /**
     * Simple revoke method (sets revoked flag if your table has it)
     * Adjust column name as per your schema (here I assume 'revoked' boolean or char).
     */
    public function revoke($id, $revokedByUserId)
    {
        try {
            $sql = "UPDATE gatepass_details SET revoked = true WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Pass::revoke " . $e->getMessage());
            return false;
        }
    }
    // Count passes
    /**
     * Count passes optionally within date range and by passfor
     */
    public function countPasses($from = null, $to = null, $passfor = null)
    {
        $sql = "SELECT COUNT(*) FROM gatepass_details WHERE 1=1";
        $params = [];
        if ($from) { $sql .= " AND entry_dt::date >= :from"; $params[':from'] = $from; }
        if ($to)   { $sql .= " AND entry_dt::date <= :to";   $params[':to'] = $to; }
        if ($passfor) { $sql .= " AND passfor = :passfor"; $params[':passfor'] = $passfor; }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Section passes count (if table exists)
     */
    public function countSectionPasses($from = null, $to = null)
    {
        try {
            $sql = "SELECT COUNT(*) FROM gatepass_details_section WHERE 1=1";
            $params = [];
            if ($from) { $sql .= " AND pass_dt::date >= :from"; $params[':from'] = $from; }
            if ($to)   { $sql .= " AND pass_dt::date <= :to";   $params[':to'] = $to; }
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            // table might not exist
            return 0;
        }
    }

    /**
     * Returns rows for filtered listing (paginated)
     * opts: from,to,passfor,adv,cino,pass_no,page,perPage
     */
    public function filteredList(array $opts = [])
    {
        $from = !empty($opts['from']) ? $opts['from'] : null;
        $to   = !empty($opts['to']) ? $opts['to'] : null;
        $passfor = !empty($opts['passfor']) ? $opts['passfor'] : null;
        $adv  = isset($opts['adv']) ? trim($opts['adv']) : '';
        $cino = isset($opts['cino']) ? trim($opts['cino']) : '';
        $pass_no = isset($opts['pass_no']) ? trim($opts['pass_no']) : '';
        $page = isset($opts['page']) ? max(1,(int)$opts['page']) : 1;
        $perPage = isset($opts['perPage']) ? (int)$opts['perPage'] : 25;
        $offset = ($page-1)*$perPage;

        $base = "FROM gatepass_details WHERE 1=1";
        $params = [];
        if ($from) { $base .= " AND entry_dt::date >= :from"; $params[':from'] = $from; }
        if ($to)   { $base .= " AND entry_dt::date <= :to";   $params[':to'] = $to; }
        if ($passfor) { $base .= " AND passfor = :passfor"; $params[':passfor']=$passfor; }
        if ($adv !== '') { $base .= " AND lower(adv_enroll) LIKE lower(:adv)"; $params[':adv']="%{$adv}%"; }
        if ($cino !== '') { $base .= " AND lower(cino) LIKE lower(:cino)"; $params[':cino']="%{$cino}%"; }
        if ($pass_no !== '') { $base .= " AND lower(pass_no) LIKE lower(:pass_no)"; $params[':pass_no']="%{$pass_no}%"; }

        // count
        $cntStmt = $this->pdo->prepare("SELECT COUNT(*) " . $base);
        $cntStmt->execute($params);
        $total = (int)$cntStmt->fetchColumn();

        // select rows
        $sql = "SELECT id, pass_no, cino, passfor, passtype, adv_enroll, court_no, item_no,
                       to_char(entry_dt,'DD/MM/YYYY HH24:MI') AS entry_dt, paddress
                " . $base . " ORDER BY entry_dt DESC LIMIT :lim OFFSET :off";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k=>$v) $stmt->bindValue($k,$v);
        $stmt->bindValue(':lim', (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        return ['rows'=>$rows,'total'=>$total,'page'=>$page,'perPage'=>$perPage];
    }

    /**
     * Chart: passes grouped by day for last N days (fills missing days with 0)
     * returns array of ['day'=>'YYYY-MM-DD','total'=>int]
     */
    public function passesLastNDays($n = 30, $from = null, $to = null, $passfor = null, $adv = '')
    {
        // compute date range (if filters provided honour them, else last n days)
        if (!$to) $toDate = new DateTime;
        else $toDate = new DateTime($to);

        if (!$from) {
            $fromDate = clone $toDate;
            $fromDate->modify('-'.($n-1).' days');
        } else {
            $fromDate = new DateTime($from);
        }

        $sql = "SELECT entry_dt::date AS day, COUNT(*) AS total
                FROM gatepass_details
                WHERE entry_dt::date >= :from AND entry_dt::date <= :to";
        $params = [':from'=>$fromDate->format('Y-m-d'), ':to'=>$toDate->format('Y-m-d')];

        if ($passfor) { $sql .= " AND passfor = :passfor"; $params[':passfor']=$passfor; }
        if ($adv !== '') { $sql .= " AND lower(adv_enroll) LIKE lower(:adv)"; $params[':adv'] = "%{$adv}%"; }

        $sql .= " GROUP BY entry_dt::date ORDER BY day ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        // build full day list with zeros filled
        $map = [];
        foreach ($rows as $r) $map[$r['day']] = (int)$r['total'];
        $out = [];
        $cur = clone $fromDate;
        while ($cur <= $toDate) {
            $d = $cur->format('Y-m-d');
            $out[] = ['day'=>$d, 'total'=> isset($map[$d]) ? $map[$d] : 0];
            $cur->modify('+1 day');
        }
        return $out;
    }

    /**
     * Passes grouped by passtype (or passfor)
     */
    public function passesByType($from=null,$to=null,$adv='')
    {
        $sql = "SELECT passtype, COUNT(*) AS total FROM gatepass_details WHERE 1=1";
        $params = [];
        if ($from) { $sql .= " AND entry_dt::date >= :from"; $params[':from']=$from; }
        if ($to)   { $sql .= " AND entry_dt::date <= :to";   $params[':to']=$to; }
        if ($adv !== '') { $sql .= " AND lower(adv_enroll) LIKE lower(:adv)"; $params[':adv']="%{$adv}%"; }
        $sql .= " GROUP BY passtype ORDER BY passtype";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Top N advocates by count (returns adv_enroll + count)
     */
    public function topAdvocates($n=10, $from=null, $to=null)
    {
        $sql = "SELECT adv_enroll, COUNT(*) AS total FROM gatepass_details WHERE adv_enroll IS NOT NULL AND adv_enroll <> ''";
        $params = [];
        if ($from) { $sql .= " AND entry_dt::date >= :from"; $params[':from']=$from; }
        if ($to)   { $sql .= " AND entry_dt::date <= :to";   $params[':to']=$to; }
        $sql .= " GROUP BY adv_enroll ORDER BY total DESC LIMIT :lim";
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k=>$v) $stmt->bindValue($k,$v);
        $stmt->bindValue(':lim',(int)$n, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Returns rows for export (wrapper) - uses filteredList under the hood
     */
    public function getFilteredRows(array $opts = [])
    {
        return $this->filteredList($opts);
    }


// Latest passes table
public function latestPasses($limit = 10)
{
    $sql = "SELECT id, pass_no, passfor,
                   to_char(entry_dt,'DD/MM/YYYY') AS entry_dt
            FROM gatepass_details
            ORDER BY id DESC LIMIT :lim";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Passes for last 30 days chart
public function passesLast30Days()
{
    $sql = "SELECT entry_dt::date AS day, COUNT(*) AS total
            FROM gatepass_details
            WHERE entry_dt >= NOW() - INTERVAL '30 days'
            GROUP BY entry_dt::date
            ORDER BY day ASC";
    $stmt = $this->pdo->query($sql);
    return $stmt->fetchAll();
}

public function saveAdvocatePass($d) {

    $sql = "INSERT INTO passes 
            (cnr, case_type, case_no, year, adv_name, adv_enroll, mobile, purpose, passfor, created_by, entry_dt)
            VALUES 
            (:cnr, :case_type, :case_no, :year, :adv_name, :adv_enroll, :mobile, :purpose, :passfor, :created_by, NOW())";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        ":cnr"        => $d['cnr'],
        ":case_type"  => $d['case_type'],
        ":case_no"    => $d['case_no'],
        ":year"       => $d['year'],
        ":adv_name"   => $d['adv_name'],
        ":adv_enroll" => $d['adv_enroll'],
        ":mobile"     => $d['mobile'],
        ":purpose"    => $d['purpose'],
        ":passfor"    => $d['passfor'],
        ":created_by" => $d['created_by']
    ]);

    return $this->db->lastInsertId();
}
public function saveSrAdvocatePass($d) {

    $sql = "INSERT INTO passes 
            (cnr, case_type, case_no, year, adv_name, adv_enroll, mobile, purpose, passfor, created_by, entry_dt)
            VALUES 
            (:cnr, :case_type, :case_no, :year, :adv_name, :adv_enroll, :mobile, :purpose, :passfor, :created_by, NOW())";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        ":cnr"        => $d['cnr'],
        ":case_type"  => $d['case_type'],
        ":case_no"    => $d['case_no'],
        ":year"       => $d['year'],
        ":adv_name"   => $d['adv_name'],
        ":adv_enroll" => $d['adv_enroll'],
        ":mobile"     => $d['mobile'],
        ":purpose"    => $d['purpose'],
        ":passfor"    => $d['passfor'],
        ":created_by" => $d['created_by']
    ]);

    return $this->db->lastInsertId();
}

public function saveLitigantPass($d) {

    $sql = "INSERT INTO passes 
        (lit_name, mobile, purpose, cnr, case_type, case_no, year, court_no, item_no, passfor, created_by, entry_dt)
        VALUES 
        (:lit_name, :mobile, :purpose, :cnr, :case_type, :case_no, :year, :court_no, :item_no, :passfor, :created_by, NOW())";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        ":lit_name"   => $d['lit_name'],
        ":mobile"     => $d['mobile'],
        ":purpose"    => $d['purpose'],
        ":cnr"        => $d['cnr'],
        ":case_type"  => $d['case_type'],
        ":case_no"    => $d['case_no'],
        ":year"       => $d['year'],
        ":court_no"   => $d['court_no'],
        ":item_no"    => $d['item_no'],
        ":passfor"    => $d['passfor'],
        ":created_by" => $d['created_by']
    ]);

    return $this->db->lastInsertId();
}
public function saveCourtPass($d) {

    $sql = "INSERT INTO passes 
        (cnr, case_type, case_no, year, person_name, mobile, court_no, item_no, hearing_date, purpose, passfor, created_by, entry_dt)
        VALUES 
        (:cnr, :case_type, :case_no, :year, :person_name, :mobile, :court_no, :item_no, :hearing_date, :purpose, :passfor, :created_by, NOW())";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        ":cnr"          => $d['cnr'],
        ":case_type"    => $d['case_type'],
        ":case_no"      => $d['case_no'],
        ":year"         => $d['year'],
        ":person_name"  => $d['person_name'],
        ":mobile"       => $d['mobile'],
        ":court_no"     => $d['court_no'],
        ":item_no"      => $d['item_no'],
        ":hearing_date" => $d['hearing_date'],
        ":purpose"      => $d['purpose'],
        ":passfor"      => $d['passfor'],
        ":created_by"   => $d['created_by']
    ]);

    return $this->db->lastInsertId();
}
public function saveSectionPass($d) {

    $sql = "INSERT INTO passes 
        (person_name, mobile, section, visit_date, purpose, passfor, created_by, entry_dt)
        VALUES 
        (:person_name, :mobile, :section, :visit_date, :purpose, :passfor, :created_by, NOW())";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        ":person_name" => $d['person_name'],
        ":mobile"      => $d['mobile'],
        ":section"     => $d['section'],
        ":visit_date"  => $d['visit_date'],
        ":purpose"     => $d['purpose'],
        ":passfor"     => $d['passfor'],
        ":created_by"  => $d['created_by']
    ]);

    return $this->db->lastInsertId();
}
public function saveVendorPass($d) {

    $sql = "INSERT INTO passes 
        (vendor_name, mobile, company, purpose, valid_upto, id_proof, passfor, created_by, entry_dt)
        VALUES 
        (:vendor_name, :mobile, :company, :purpose, :valid_upto, :id_proof, :passfor, :created_by, NOW())";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        ":vendor_name" => $d['vendor_name'],
        ":mobile"      => $d['mobile'],
        ":company"     => $d['company'],
        ":purpose"     => $d['purpose'],
        ":valid_upto"  => $d['valid_upto'],
        ":id_proof"    => $d['id_proof'],
        ":passfor"     => $d['passfor'],
        ":created_by"  => $d['created_by']
    ]);

    return $this->db->lastInsertId();
}
public function getPassById($id) {
    $stmt = $this->db->prepare("SELECT * FROM passes WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
public function fetchCourtCase($type, $no, $year, $clType, $clDate)
{
    $sql = "
        SELECT 
            A.cino, 
            B.sno, 
            B.croom,
            A.pet_name,
            A.res_name
        FROM civil_t A
        JOIN causelistsrno B 
          ON ((A.case_no = B.case_no AND B.con_case_no IS NULL)
          OR  (A.case_no = B.con_case_no AND B.con_case_no IS NOT NULL))
        WHERE A.regcase_type = :type
          AND A.reg_no = :no
          AND A.reg_year = :year
          AND B.causelisttype = :ctype
          AND B.causelistdate = :cdate
          AND B.isfinalized = 'Y'
        LIMIT 1;
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        ':type' => $type,
        ':no'   => $no,
        ':year' => $year,
        ':ctype'=> $clType,
        ':cdate'=> $clDate
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}




}
