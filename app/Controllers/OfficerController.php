<?php
require_once __DIR__ . '/BaseController.php';

class OfficerController extends BaseController
{
    public function dashboard()
    {
    

        $this->requireAuth();
        $this->requireRole([10]);
        if (empty($_SESSION['admin_user']['establishment'])) {
            $this->render("officer/forceEstablishmentSelect");
            return;
        }
        $this->render("officer/dashboard");
    }

    public function changeEstablishment()
    {
        $this->requireRole([10]);

        if (!isset($_SESSION['admin_user']) || $_SESSION['admin_user']['role_id'] != 10) {
            echo json_encode(["status" => "ERROR", "message" => "Unauthorized"]);
            return;
        }

        // If POST request → update establishment
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $est = $_POST["establishment"] ?? '';

            if ($est !== "P" && $est !== "B") {
                echo json_encode(["status" => "ERROR", "message" => "Invalid selection"]);
                return;
            }

            // Update user record
            $userId = $_SESSION["admin_user"]["id"];

            $sql = "UPDATE gatepass_users SET estt = :est WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ":est" => $est,
                ":id"  => $userId
            ]);

            // Update session as well
            $_SESSION["admin_user"]["establishment"] = $est;

            echo json_encode(["status" => "OK"]);
            return;
        }

        // GET request (not needed now)
        echo json_encode(["status" => "ERROR", "message" => "Invalid request"]);
    }

    public function saveEstablishment()
    {
        $this->requireAuth();
        $this->requireRole([10]);

        $est = $_POST['establishment'] ?? '';

        if (!in_array($est, ['P', 'B'])) {
            echo json_encode(["status" => "ERROR", "message" => "Invalid establishment"]);
            return;
        }

        $userId = $_SESSION['admin_user']['id'];

        $stmt = $this->pdo->prepare("
        UPDATE gatepass_users SET estt = :est WHERE id = :id
    ");
        $stmt->execute([
            ":est" => $est,
            ":id"  => $userId
        ]);

        // update session
        $_SESSION['admin_user']['establishment'] = $est;

        echo json_encode(["status" => "OK"]);
    }
}
