<?php

use App\Jobs\TransactionalEmail;
use Illuminate\Support\Facades\Route;
use Illuminate\Mail\Mailables\Address;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/email', function() {
    TransactionalEmail::send(
        to: new Address('transactional@test.com'),
        data: collect(['name' => 'Dr. Who']),
        templateId: 2
    );

    return 'Message sent';
});
