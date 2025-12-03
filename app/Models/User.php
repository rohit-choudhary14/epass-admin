<?php
require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel
{
    // find user by username (case-insensitive)
    public function findByUsername($username)
    {
        $sql = "SELECT id, role_id, name, status, password, enroll_num, adv_code, contact_num, passtype, username, loginattempt, block, oldestpwd, oldpwd, estt, created
                FROM gatepass_users
                WHERE (status IS NOT NULL AND status <> '2') AND lower(username) = lower(:u)
                LIMIT 1";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':u' => $username]);
            $row = $stmt->fetch();
            return $row ? $row : null;
        } catch (PDOException $e) {
            error_log("User::findByUsername ".$e->getMessage());
            return null;
        }
    }

    // create admin user (role_id = 2, passtype = 3) — uses encryption for email/contact/password
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
            ':role_id' => 2,     // admin role as requested
            ':dob' => $data['dob'] ?? null,
            ':address' => $data['address'] ?? '',
            ':state' => $data['state'] ?? 0,
            ':district' => $data['district'] ?? 0,
            ':pincode' => $data['pincode'] ?? 0,
            ':ver_code' => rand(100000,999999),
            ':adv_code' => 0,
            ':passtype' => 3
        ];

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $res = $stmt->fetch();
            return $res ? (int)$res['id'] : null;
        } catch (PDOException $e) {
            error_log("User::createAdmin ".$e->getMessage());
            return null;
        }
    }
}
