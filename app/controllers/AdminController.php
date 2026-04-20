<?php

require_once BASE_PATH . '/app/controllers/Controller.php';
require_once BASE_PATH . '/app/helpers/Auth.php';

class AdminController extends Controller
{
    public function dashboard(): void
    {
        Auth::protectRole(['Admin', 'Staff_Damayan', 'Staff_Male', 'Staff_Female', 'Staff_Tenant']);
        $this->view('admin/mis_admin/admin_dashboard', ['active_page' => 'admin_dashboard']);
    }

    public function apartment(): void
    {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $this->view('admin/Staff_Admin/Admin-Apartment_Department/apartment_dashboard');
    }

    public function apartmentInfo(): void
    {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $this->view('admin/Staff_Admin/Admin-Apartment_Department/apartments_info');
    }

    public function apartmentProfile(): void
    {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $this->view('admin/Staff_Admin/Admin-Apartment_Department/apartment_profile');
    }

    public function apartmentNotifications(): void
    {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $this->view('admin/Staff_Admin/Admin-Apartment_Department/apartment_notification');
    }

    public function apartmentSoa(): void
    {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $this->view('admin/Staff_Admin/Admin-Apartment_Department/statement_of_account');
    }

    public function payment(): void
    {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $this->view('admin/Staff_Admin/Admin-Apartment_Department/payment');
    }

    // MIS Admin Modules
    public function staffApartmentConfirmation(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        require_once BASE_PATH . '/app/models/ApartmentApp.php';
        $model = new ApartmentApp();
        $reports = $model->getAllApplications();
        $this->view('admin/Staff_Admin/Admin-Apartment_Department/apartment_confirmation', [
            'active_page' => 'apartment_confirmation',
            'reports' => $reports
        ]);
    }

    public function apartmentRecords(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $this->view('admin/mis_admin/apartment_records', ['active_page' => 'apartment_records']);
    }

    public function apartmentConfirmation(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        require_once BASE_PATH . '/app/models/ApartmentApp.php';
        $model = new ApartmentApp();
        $reports = $model->getAllApplications();
        $this->view('admin/mis_admin/admin_apartment_confirmation', [
            'active_page' => 'apartment_confirmation',
            'reports' => $reports
        ]);
    }

    public function approveApartmentApp(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $id = $_GET['id'] ?? null;
        if ($id) {
            require_once BASE_PATH . '/app/models/ApartmentApp.php';
            $model = new ApartmentApp();
            $model->updateApplicationStatus($id, 'Approved');
        }
        header('Location: ' . url('/admin/mis_admin/apartment_confirmation'));
    }

    public function rejectApartmentApp(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $id = $_GET['id'] ?? null;
        $reason = $_GET['reason'] ?? null;
        if ($id) {
            require_once BASE_PATH . '/app/models/ApartmentApp.php';
            $model = new ApartmentApp();
            $model->updateApplicationStatus($id, 'Rejected', $reason);
        }
        header('Location: ' . url('/admin/mis_admin/apartment_confirmation'));
    }

    public function parkingApproval(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        require_once BASE_PATH . '/app/models/ApartmentApp.php';
        $model = new ApartmentApp();
        $reports = $model->getAllParkingApplications();
        $this->view('admin/mis_admin/admin_parking_approval', [
            'active_page' => 'parking_approval',
            'reports' => $reports
        ]);
    }

    public function approveParking(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $id = $_GET['id'] ?? null;
        if ($id) {
            require_once BASE_PATH . '/app/models/ApartmentApp.php';
            $model = new ApartmentApp();
            $model->updateParkingStatus($id, 'Approved');
        }
        header('Location: ' . url('/admin/mis_admin/parking_approval'));
    }

    public function rejectParking(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $id = $_GET['id'] ?? null;
        $reason = $_GET['reason'] ?? null;
        if ($id) {
            require_once BASE_PATH . '/app/models/ApartmentApp.php';
            $model = new ApartmentApp();
            $model->updateParkingStatus($id, 'Rejected', $reason);
        }
        header('Location: ' . url('/admin/mis_admin/parking_approval'));
    }

    public function billing(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $this->view('admin/mis_admin/billing_and_payment', ['active_page' => 'billing']);
    }

    public function soa(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $this->view('admin/mis_admin/statement_of_account', ['active_page' => 'soa']);
    }

    public function reports(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $this->view('admin/mis_admin/admin_reports', ['active_page' => 'reports']);
    }

    public function daawahRecords(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        $this->view('admin/mis_admin/daawah_records', ['active_page' => 'daawah_records']);
    }

    public function damayanRecords(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        $this->view('admin/mis_admin/damayan_records', ['active_page' => 'damayan_records']);
    }

    public function notificationBroadcast(): void {
        Auth::protectRole(['Admin']);
        $this->view('admin/mis_admin/notification_broadcast', ['active_page' => 'notifications']);
    }

    public function userRecords(): void {
        Auth::protectRole(['Admin']);
        $this->view('admin/mis_admin/records', ['active_page' => 'records']);
    }

    public function auditLogs(): void {
        Auth::protectRole(['Admin']);
        $this->view('admin/mis_admin/audit_logs', ['active_page' => 'audit_logs']);
    }

    public function notificationInbox(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan', 'Staff_Male', 'Staff_Female', 'Staff_Tenant']);
        $this->view('admin/mis_admin/notification_inbox', ['active_page' => 'notification']);
    }

    public function serveTenantImage(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $userId = $_GET['uid'] ?? null;
        $type   = $_GET['type'] ?? '';

        if (!$userId) {
            http_response_code(400);
            echo 'Missing user ID';
            return;
        }

        $allowed = ['picture','valididfront','valididback','birthcert','nbi','proofofincome'];
        if (!in_array($type, $allowed)) {
            http_response_code(400);
            echo 'Invalid type';
            return;
        }

        require_once BASE_PATH . '/app/models/ApartmentApp.php';
        $model = new ApartmentApp();
        
        // Look up the tenant's addinfo record, then fetch the image
        $info = $model->getInfo($userId);
        if (empty($info)) {
            http_response_code(404);
            echo 'No application info found';
            return;
        }

        $infoId = $info['tenant_info'];
        $result = $model->getAddInfoImage($infoId, $type);

        if (!$result) {
            http_response_code(404);
            echo 'Image not found';
            return;
        }

        header("Content-Type: " . $result['mime']);
        header("Content-Length: " . strlen($result['data']));
        header("Cache-Control: private, max-age=3600");
        echo $result['data'];
    }
}
