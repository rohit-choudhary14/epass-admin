<?php
require_once __DIR__ . '/BaseController.php';

class OfficerController extends BaseController
{
    public function dashboard()
    {
        // Officer must have role_id = 10
        $this->requireRole([10]);

        $this->render("officer/dashboard");
    }
}
