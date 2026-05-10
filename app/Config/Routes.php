<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default - redirect ke login
$routes->get('/', function () {
    return redirect()->to('/auth/login');
});

// ---- Auth (publik, tanpa filter) ----
$routes->group('auth', function ($routes) {
    $routes->get('login',   '\App\Modules\Auth\Controllers\AuthController::login');
    $routes->post('login',  '\App\Modules\Auth\Controllers\AuthController::loginPost');
    $routes->get('logout',  '\App\Modules\Auth\Controllers\AuthController::logout');
    $routes->get('captcha', '\App\Modules\Auth\Controllers\AuthController::captchaImage');
});

// ---- Protected Routes (membutuhkan login) ----
$routes->group('', ['filter' => 'auth'], function ($routes) {

    // Dashboard (admin only)
    $routes->group('dashboard', ['filter' => 'admin'], function ($routes) {
        $routes->get('/',          '\App\Modules\Dashboard\Controllers\DashboardController::index');
        $routes->get('analytics',  '\App\Modules\Dashboard\Controllers\DashboardController::analytics');
    });

    // Tickets
    $routes->group('tickets', function ($routes) {
        $routes->get('/',                '\App\Modules\Tickets\Controllers\TicketsController::index');
        $routes->get('detail/(:num)',    '\App\Modules\Tickets\Controllers\TicketsController::detail/$1');
        $routes->post('assign/(:num)',   '\App\Modules\Tickets\Controllers\TicketsController::assign/$1');
        $routes->post('resolve/(:num)',  '\App\Modules\Tickets\Controllers\TicketsController::resolve/$1');
        $routes->get('poll',             '\App\Modules\Tickets\Controllers\TicketsController::poll');
        $routes->get('templates',        '\App\Modules\Tickets\Controllers\TicketsController::templates');
        $routes->post('reply/(:num)',    '\App\Modules\Tickets\Controllers\TicketsController::reply/$1');
    });

    // Customers
    $routes->group('customers', function ($routes) {
        $routes->get('/',             '\App\Modules\Customers\Controllers\CustomersController::index');
        $routes->get('detail/(:num)', '\App\Modules\Customers\Controllers\CustomersController::detail/$1');
    });

    // Knowledge Base
    $routes->group('knowledge', function ($routes) {
        $routes->get('/',              '\App\Modules\Knowledge\Controllers\KnowledgeController::index');
        $routes->get('create',         '\App\Modules\Knowledge\Controllers\KnowledgeController::create');
        $routes->post('store',         '\App\Modules\Knowledge\Controllers\KnowledgeController::store');
        $routes->get('edit/(:num)',    '\App\Modules\Knowledge\Controllers\KnowledgeController::edit/$1');
        $routes->post('update/(:num)', '\App\Modules\Knowledge\Controllers\KnowledgeController::update/$1');
        $routes->post('delete/(:num)', '\App\Modules\Knowledge\Controllers\KnowledgeController::delete/$1');
        $routes->post('sync/(:num)',   '\App\Modules\Knowledge\Controllers\KnowledgeController::sync/$1');
    });

    // Staff (admin only)
    $routes->group('staff', ['filter' => 'admin'], function ($routes) {
        $routes->get('/',                       '\App\Modules\Staff\Controllers\StaffController::index');
        $routes->get('create',                  '\App\Modules\Staff\Controllers\StaffController::create');
        $routes->post('store',                  '\App\Modules\Staff\Controllers\StaffController::store');
        $routes->get('detail/(:num)',           '\App\Modules\Staff\Controllers\StaffController::detail/$1');
        $routes->get('edit/(:num)',             '\App\Modules\Staff\Controllers\StaffController::edit/$1');
        $routes->post('update/(:num)',          '\App\Modules\Staff\Controllers\StaffController::update/$1');
        $routes->post('toggle/(:num)',          '\App\Modules\Staff\Controllers\StaffController::toggle/$1');
        $routes->post('reset-password/(:num)', '\App\Modules\Staff\Controllers\StaffController::resetPassword/$1');
        // Divisions AJAX
        $routes->post('division-store',          '\App\Modules\Staff\Controllers\StaffController::divisionStore');
        $routes->post('division-update/(:num)',  '\App\Modules\Staff\Controllers\StaffController::divisionUpdate/$1');
        $routes->post('division-delete/(:num)',  '\App\Modules\Staff\Controllers\StaffController::divisionDelete/$1');
        // Services AJAX
        $routes->post('service-store',           '\App\Modules\Staff\Controllers\StaffController::serviceStore');
        $routes->post('service-update/(:num)',   '\App\Modules\Staff\Controllers\StaffController::serviceUpdate/$1');
        $routes->post('service-delete/(:num)',   '\App\Modules\Staff\Controllers\StaffController::serviceDelete/$1');
    });

    // Conversations
    $routes->group('conversations', function ($routes) {
        $routes->get('/',                '\App\Modules\Conversations\Controllers\ConversationsController::index');
        $routes->get('chat/(:num)',      '\App\Modules\Conversations\Controllers\ConversationsController::chat/$1');
        $routes->post('reply/(:num)',    '\App\Modules\Conversations\Controllers\ConversationsController::reply/$1');
        $routes->post('takeover/(:num)', '\App\Modules\Conversations\Controllers\ConversationsController::takeover/$1');
        $routes->post('release/(:num)',  '\App\Modules\Conversations\Controllers\ConversationsController::release/$1');
        $routes->get('poll/(:num)',      '\App\Modules\Conversations\Controllers\ConversationsController::poll/$1');
        $routes->get('templates',        '\App\Modules\Conversations\Controllers\ConversationsController::templates');
    });

    // Settings (admin only)
    $routes->group('settings', ['filter' => 'admin'], function ($routes) {
        $routes->get('/',               '\App\Modules\Settings\Controllers\SettingsController::index');
        $routes->post('save',           '\App\Modules\Settings\Controllers\SettingsController::save');
        $routes->post('test-connection', '\App\Modules\Settings\Controllers\SettingsController::testConnection');
    });
});

// ---- API Endpoints (Bearer Token auth) ----
$routes->group('api', ['filter' => 'apiauth'], function ($routes) {
    $routes->post('log',                  '\App\Modules\Api\Controllers\ApiController::log');
    $routes->post('ticket',               '\App\Modules\Api\Controllers\ApiController::ticket');
    $routes->get('setting/(:segment)',    '\App\Modules\Api\Controllers\ApiController::setting/$1');
    $routes->post('customer',             '\App\Modules\Api\Controllers\ApiController::customer');
});
