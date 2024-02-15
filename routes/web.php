<?php

use Illuminate\Support\Facades\Route;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use TCG\Voyager\Facades\Voyager;
use App\Http\Controllers\PlansController;


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

Route::get('/test', function () {

    $sizeInInches = "3x3";
    $sizeInInchesArray = explode("x",$sizeInInches);

    $widthInPixels = $sizeInInchesArray[0] * 300;
    $heightInPixels = $sizeInInchesArray[1] * 300;

    $sizeArray = [$widthInPixels,$heightInPixels];

//    return QrCode::size($sizeArray[0],$sizeArray[1])->generate('Hello World!')->save('public/qrcode.jpg');

    $image = QrCode::format('svg')->size($sizeArray[0],$sizeArray[1])->generate('Hello World!');
    $output_file = '/img-' . time() . '.svg';
    Storage::disk('local')->put($output_file, $image);

    return $image;

});

Route::get('/', function () {
    return view('voyager::login');
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
