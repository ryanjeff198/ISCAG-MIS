<?php

/**
 * Route Definitions
 *
 * Format: 'URI' => ['ControllerClass', 'method']
 *
 * The router will match the REQUEST_URI against these keys,
 * require the controller file, instantiate the class, and call the method.
 */

$routes = [
    // Public pages
    '/'                 => ['HomeController',  'index'],
    '/index'            => ['HomeController',  'index'],
    '/history-organization' => ['HomeController',  'historyOrganization'],
    '/departments'          => ['HomeController',  'departments'],
    '/apartment'            => ['HomeController',  'apartment'],
    '/daawah'               => ['HomeController',  'daawah'],
    '/damayan'              => ['HomeController',  'damayan'],
    '/events'               => ['HomeController',  'events'],
    '/announcements'        => ['HomeController',  'announcements'],
    '/contact'              => ['HomeController',  'contact'],

    // Authentication
    '/login'            => ['AuthController',  'login'],
    '/register'         => ['AuthController',  'register'],
    '/forgot-password'  => ['AuthController',  'forgotPassword'],
    '/verify-otp'       => ['AuthController',  'verifyOtp'],
    '/resend-otp'       => ['AuthController',  'resendOtp'],
    '/change-registration-email' => ['AuthController', 'changeRegistrationEmail'],
    '/reset-password'   => ['AuthController',  'resetPassword'],

    // Admin
    '/admin/dashboard'  => ['AdminController', 'dashboard'],
    '/admin/analytics'  => ['AdminController', 'analytics'],

    // User
    '/user/dashboard'   => ['UserController',  'dashboard'],
    '/user/profile'     => ['UserController',  'profile'],
    '/user/profile/update' => ['UserController', 'updateProfile'],
    '/user/profile/avatar' => ['UserController', 'uploadAvatar'],
    '/user/profile/avatar/serve' => ['UserController', 'serveAvatar'],
    '/user/notifications' => ['UserController', 'notifications'],
    '/user/status/check'  => ['UserController', 'checkStatus'],
    '/user/notifications/mark-read' => ['UserController', 'markNotificationRead'],
    '/logout'           => ['AuthController', 'logout'],

    // User Apartment Module
    '/user/apartment/apply'  => ['ApartmentController', 'apply'],
    '/user/apartment/status' => ['ApartmentController', 'status'],
    '/user/apartment/info'   => ['ApartmentController', 'info'],
    '/user/apartment/parking' => ['ApartmentController', 'parking'],
    '/user/apartment/parking/submit' => ['ApartmentController', 'submitParking'],
    '/user/apartment/save'   => ['ApartmentController', 'save'],
    '/user/apartment/submit' => ['ApartmentController', 'finalizeSubmission'],
    '/user/apartment/upload' => ['ApartmentController', 'handleUpload'],
    '/user/apartment/image'  => ['ApartmentController', 'serveImage'],
    '/user/apartment/remove-image' => ['ApartmentController', 'removeImage'],
    '/user/apartment/uploads/check' => ['ApartmentController', 'checkUploads'],
    '/user/apartment/lease'          => ['ApartmentController', 'lease'],
    '/user/apartment/lease/accept'   => ['ApartmentController', 'acceptLease'],
    '/user/apartment/lease/renew'    => ['ApartmentController', 'requestRenewal'],
    '/user/apartment/payment'        => ['ApartmentController', 'payment'],
    '/user/apartment/payment/submit' => ['ApartmentController', 'submitPayment'],
    '/user/apartment/soa'            => ['ApartmentController', 'soa'],

    // Service Modules
    '/user/services/burial-form'      => ['UserController', 'burialForm'],
    '/user/services/counseling/male'   => ['UserController', 'maleCounseling'],
    '/user/services/counseling/female' => ['UserController', 'femaleCounseling'],

    // Admin Modules (New)
    '/admin/apartment'               => ['AdminController', 'apartment'],
    '/admin/apartment/info'          => ['AdminController', 'apartmentInfo'],
    '/admin/apartment/profile'       => ['AdminController', 'apartmentProfile'],
    '/admin/apartment/notifications' => ['AdminController', 'apartmentNotifications'],
    '/admin/apartment/confirmation'  => ['AdminController', 'staffApartmentConfirmation'],
    '/admin/apartment/confirmation/approve' => ['AdminController', 'staffApproveApartmentApp'],
    '/admin/apartment/confirmation/reject'  => ['AdminController', 'staffRejectApartmentApp'],
    '/admin/apartment/soa'           => ['AdminController', 'apartmentSoa'],
    '/admin/apartment/parking'       => ['AdminController', 'staffParkingApproval'],
    '/admin/apartment/parking/approve' => ['AdminController', 'staffApproveParking'],
    '/admin/apartment/parking/reject'=> ['AdminController', 'staffRejectParking'],
    '/admin/apartment/tenants'       => ['AdminController', 'tenantInfo'],
    '/admin/apartment/renewals'      => ['AdminController', 'renewals'],
    '/admin/apartment/renewals/approve' => ['AdminController', 'approveRenewal'],
    '/admin/apartment/renewals/reject'  => ['AdminController', 'rejectRenewal'],
    '/admin/payment'                 => ['AdminController', 'payment'],

    // MIS Admin Hub Routes
    '/admin/mis_admin/apartment_records'   => ['AdminController', 'apartmentRecords'],
    '/admin/mis_admin/apartment_confirmation' => ['AdminController', 'apartmentConfirmation'],
    '/admin/mis_admin/parking_approval'    => ['AdminController', 'parkingApproval'],
    '/admin/mis_admin/parking/approve'     => ['AdminController', 'approveParking'],
    '/admin/mis_admin/parking/reject'      => ['AdminController', 'rejectParking'],
    '/admin/mis_admin/billing'             => ['AdminController', 'billing'],
    '/admin/mis_admin/statement_of_account' => ['AdminController', 'soa'],
    '/admin/mis_admin/reports'             => ['AdminController', 'reports'],
    '/admin/mis_admin/daawah_records'      => ['AdminController', 'daawahRecords'],
    '/admin/mis_admin/damayan_records'     => ['AdminController', 'damayanRecords'],
    '/admin/mis_admin/notifications'       => ['AdminController', 'notificationBroadcast'],
    '/admin/mis_admin/records'             => ['AdminController', 'userRecords'],
    '/admin/mis_admin/toggle_user_status'  => ['AdminController', 'toggleUserStatus'],
    '/admin/mis_admin/audit_logs'          => ['AdminController', 'auditLogs'],
    '/admin/mis_admin/notification'        => ['AdminController', 'notificationInbox'],
    '/admin/mis_admin/renewal_records'     => ['AdminController', 'renewalRecords'],
    '/admin/mis_admin/tenant_image'        => ['AdminController', 'serveTenantImage'],

    // Apartment Type Management API
    '/api/apartment-types'                 => ['ApartmentTypeController', 'listTypes'],
    '/api/apartment-types/detail'          => ['ApartmentTypeController', 'getType'],
    '/api/apartment-types/create'          => ['ApartmentTypeController', 'createType'],
    '/api/apartment-types/update'          => ['ApartmentTypeController', 'updateType'],
    '/api/apartment-types/delete'          => ['ApartmentTypeController', 'deleteType'],
    '/api/apartment-types/upload-image'    => ['ApartmentTypeController', 'uploadImage'],
    '/api/apartment-types/delete-image'    => ['ApartmentTypeController', 'deleteImage'],
    '/api/apartment-types/set-thumbnail'   => ['ApartmentTypeController', 'setThumbnail'],
    '/api/apartment-types/serve-image'     => ['ApartmentTypeController', 'serveImage'],
    '/api/apartment-units'                 => ['ApartmentTypeController', 'listUnits'],
    '/api/apartment-units/create'          => ['ApartmentTypeController', 'createUnit'],
    '/api/apartment-units/update'          => ['ApartmentTypeController', 'updateUnit'],
    '/api/apartment-units/delete'          => ['ApartmentTypeController', 'deleteUnit'],
];
