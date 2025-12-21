<?php
require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel
{
    // find user by username (case-insensitive)
    public function findByUsername($username)
    {
        $sql = "SELECT id, role_id, name, status, password, enroll_num, adv_code, contact_num, passtype, username, loginattempt, block, oldestpwd, oldpwd, estt, created
                FROM gatepass_users
                WHERE (status IS NOT NULL AND status <> '20') AND lower(username) = lower(:u)
                LIMIT 1";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':u' => $username]);
            $row = $stmt->fetch();
            return $row ? $row : null;
        } catch (PDOException $e) {
            error_log("User::findByUsername " . $e->getMessage());
            return null;
        }
    }

    // create admin user (role_id = 20, passtype = 3) — uses encryption for email/contact/password
    public function createAdmin(array $data)
    {
        $sql = "INSERT INTO gatepass_users
            (username, name, email, password, gender, contact_num, status, ip, role_id, created, dob, address, state, district, pincode, ver_code, adv_code, passtype)
            VALUES (:username, :name, :email, :password, :gender, :contact_num, :status, :ip, :role_id, NOW(), :dob, :address, :state, :district, :pincode, :ver_code, :adv_code, :passtype)
            RETURNING id";

        $params = [
            ':username' => $data['username'],
            ':name' => $data['name'],
            ':email' => $this->encryptData($data['email']),
            ':password' => $this->encryptData($data['password']),
            ':gender' => $data['gender'] ?? '',
            ':contact_num' => $this->encryptData($data['contact']),
            ':status' => 1,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            ':role_id' => 20,     // admin role as requested
            ':dob' => $data['dob'] ?? null,
            ':address' => $data['address'] ?? '',
            ':state' => $data['state'] ?? 0,
            ':district' => $data['district'] ?? 0,
            ':pincode' => $data['pincode'] ?? 0,
            ':ver_code' => rand(100000, 999999),
            ':adv_code' => 0,
            ':passtype' => 3
        ];

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $res = $stmt->fetch();
            return $res ? (int)$res['id'] : null;
        } catch (PDOException $e) {
            error_log("User::createAdmin " . $e->getMessage());
            return null;
        }
    }

    public function getAllUsers()
    {
        $sql = "SELECT id, username, name, email, contact_num, role_id, status,estt 
            FROM gatepass_users 
            WHERE role_id = 10 
            ORDER BY id DESC";

        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $output = [];

        foreach ($rows as $r) {
            $output[] = [
                'id'       => $r['id'],
                'username' => $r['username'],
                'name'     => $r['name'],
                'email'    => $this->decryptData($r['email']),
                'contact'  => $this->decryptData($r['contact_num']),
                'type'     => $r['role_id'],
                'status'   => $r['status'],
                'estt'     => $r['estt']
            ];
        }
        return $output;
    }

    public function createOfficer($username, $name, $gender, $email, $contact, $password, $type, $estt)
    {
        $sql = "INSERT INTO gatepass_users 
            (username, name, gender, email, password, contact_num, address, status, ip, created, role_id,estt)
            VALUES
            (:username, :name, :gender, :email, :password, :contact, '', 1, :ip, NOW(), :role_id, :estt)
            RETURNING id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':username' => $username,
            ':name'     => $name,
            ':gender'   => $gender,
            ':email'    => $this->encryptData($email),
            ':password' => $this->encryptData($password),
            ':contact'  => $this->encryptData($contact),
            ':ip'       => $_SERVER['REMOTE_ADDR'] ?? '',
            ':role_id'  => 10,
            ':estt'     => $estt
        ]);
    }
    public function checkPartyExists($name, $mobile)
    {
        $sql = "SELECT id, contact_num FROM gatepass_users 
            WHERE LOWER(name) = LOWER(:name)
            AND passtype = 3 AND role_id=2";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":name" => trim($name)]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) return false;

        foreach ($rows as $row) {

            $decMob = $this->decryptData($row["contact_num"]);

            if ($decMob == trim($mobile)) {

                return true;
            }
        }
        return false;
    }
    public function findPartyByMobile($mobileEnc)
    {
        if (!$mobileEnc) {
            return false;
        }
       
        $encMobile = trim(
            $this->encryptData($mobileEnc));
 
        $sql = "SELECT name, address
            FROM gatepass_users
            WHERE passtype = 3
              AND role_id = 2
              AND contact_num = :mobile
            LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":mobile" => $encMobile
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return false;
        }
        return $row;
    }

    public function createPartyUser(
        $name,
        $mobile,
        $email,
        $passwordHash,
        $roleId,
        $passtype,
        $est,
        $address
    ) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $created = date('Y-m-d H:i:s');

        $sql = "
        INSERT INTO gatepass_users
        (
            name,
            gender,
            contact_num,
            email,
            password,
            role_id,
            passtype,
            ip,
            created,
            estt,
            status,
            username,
            address
        )
        VALUES
        (
            :name,
            :gender,
            :mobile,
            :email,
            :password,
            :role_id,
            :passtype,
            :ip,
            :created,
            :est,
            1,
            :username,
            :address
        )
    ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':name'     => $name,
            ':gender'   => 'N',
            ':mobile'   => $this->encryptData($mobile),
            ':email'    => $this->encryptData($email),
            ':password' => $passwordHash,
            ':role_id'  => $roleId,
            ':passtype' => $passtype,
            ':ip'       => $ip,
            ':created'  => $created,
            ':est'      => $est,
            ':username' => $mobile,     // ✅ username = mobile
            ':address'  => $address
        ]);
    }
}
