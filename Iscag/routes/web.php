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

    // Authentication
    '/login'            => ['AuthController',  'login'],
    '/register'         => ['AuthController',  'register'],
    '/forgot-password'  => ['AuthController',  'forgotPassword'],
    '/verify-otp'       => ['AuthController',  'verifyOtp'],
    '/reset-password'   => ['AuthController',  'resetPassword'],

    // Admin
    '/admin/dashboard'  => ['AdminController', 'dashboard'],

    // User
    '/user/dashboard'   => ['UserController',  'dashboard'],
    '/user/profile'     => ['UserController',  'profile'],
    '/user/profile/update' => ['UserController', 'updateProfile'],
    '/user/profile/avatar' => ['UserController', 'uploadAvatar'],
    '/user/profile/avatar/serve' => ['UserController', 'serveAvatar'],
    '/user/notifications' => ['UserController', 'notifications'],
    '/logout'           => ['AuthController', 'logout'],

    // User Apartment Module
    '/user/apartment/apply'  => ['ApartmentController', 'apply'],
    '/user/apartment/status' => ['ApartmentController', 'status'],
    '/user/apartment/info'   => ['ApartmentController', 'info'],
    '/user/apartment/parking' => ['ApartmentController', 'parking'],
    '/user/apartment/save'   => ['ApartmentController', 'save'],
    '/user/apartment/upload' => ['ApartmentController', 'handleUpload'],
    '/user/apartment/image'  => ['ApartmentController', 'serveImage'],

    // Service Modules
    '/user/services/burial-form'      => ['UserController', 'burialForm'],
    '/user/services/counseling/male'   => ['UserController', 'maleCounseling'],
    '/user/services/counseling/female' => ['UserController', 'femaleCounseling'],

    // Admin Modules (New)
    '/admin/apartment'  => ['AdminController', 'apartment'],
    '/admin/payment'    => ['AdminController', 'payment'],

    // MIS Admin Hub Routes
    '/admin/mis_admin/apartment_records'   => ['AdminController', 'apartmentRecords'],
    '/admin/mis_admin/tenant_confirmation' => ['AdminController', 'tenantConfirmation'],
    '/admin/mis_admin/parking_approval'    => ['AdminController', 'parkingApproval'],
    '/admin/mis_admin/billing'             => ['AdminController', 'billing'],
    '/admin/mis_admin/statement_of_account' => ['AdminController', 'soa'],
    '/admin/mis_admin/reports'             => ['AdminController', 'reports'],
    '/admin/mis_admin/daawah_records'      => ['AdminController', 'daawahRecords'],
    '/admin/mis_admin/damayan_records'     => ['AdminController', 'damayanRecords'],
    '/admin/mis_admin/notifications'       => ['AdminController', 'notificationBroadcast'],
    '/admin/mis_admin/records'             => ['AdminController', 'userRecords'],
    '/admin/mis_admin/audit_logs'          => ['AdminController', 'auditLogs'],
    '/admin/mis_admin/notification'        => ['AdminController', 'notificationInbox'],
];
