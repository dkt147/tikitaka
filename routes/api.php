<?php

use App\Http\Controllers\API_v7\BoxController;
use App\Http\Controllers\API_v7\CategoryController;
use App\Http\Controllers\API_v7\ItemController;
use App\Http\Controllers\API_v7\PaymentController;
use App\Http\Controllers\API_v7\PlanController;
use App\Http\Controllers\API_v7\PriceController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API_v7\QRCode;
use App\Http\Controllers\API_v7\SubscriptionController;
use App\Http\Controllers\BoxController as ControllersBoxController;
use App\Http\Controllers\Printful\PrintfulController;
use App\Http\Controllers\Printful\PrintfulPaymentController;
use App\Http\Controllers\Printful\QrController;
use App\Http\Controllers\Printful\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/admin/box/view/{id}',[App\Http\Controllers\BoxController::class,'view'])->name('item_view');

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/verifyforgot', [UserController::class, 'verifyForgot']);
Route::post('/reset', [UserController::class, 'resetPassword']);
Route::any('/otpsend', [UserController::class, 'sendOTP']);
Route::post('/user/delete', [UserController::class, 'delete']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/test', [UserController::class, 'test']);
    Route::get('/box/boxes', [BoxController::class, 'getBoxes']);
    Route::get('/user/box/items', [ItemController::class, 'getItems']);
    Route::post('/user/item/delete', [ItemController::class, 'delete']);
    Route::post('/user/box/delete', [BoxController::class, 'delete']);
    Route::post('/user/category/delete', [CategoryController::class, 'delete']);
    Route::get('/category/list', [CategoryController::class, 'index']);
    Route::any('/emailotp', [UserController::class, 'emailOTP']);
    Route::post('/verifyemail', [UserController::class, 'verifyEmail']);
    Route::post('/phonenumber', [UserController::class, 'userMobile']);
    Route::post('/box/store', [BoxController::class, 'store']);

    Route::post('/item/store', [ItemController::class, 'store']);
    Route::post('/box/qr', [BoxController::class, 'generateQR']);
    Route::get('box/price',[PriceController::class,'index']);

    Route::get('/plans', [PlanController::class, 'index']);

    Route::post('/sub/store', [SubscriptionController::class, 'store']);
    Route::post('/sub/cancel', [SubscriptionController::class, 'cancel']);
    Route::get('/sub/get', [SubscriptionController::class, 'getSubscription']);

    Route::post('/lov',[UserController::class,'lov']);
    Route::post('/member/add', [UserController::class, 'addMember']);

    Route::post('/payment/store', [PaymentController::class, 'store']);

    //Mark:- Printful API routes
    Route::group(['prefix'=>'printful'],function (){

        //Mark:- Store Related Routes
        Route::group(['prefix'=>'store'],function (){
            Route::get('/',[PrintfulController::class,'getStoreInformation']);
        });

        //Mark:- Product Related Routes
        Route::group(['prefix'=>'product'],function (){
            Route::get('variant',[PrintfulController::class,'getProductVariant']);
            Route::get('single/variant/{id}',[PrintfulController::class,'getSingleVariant']);
            Route::get('all/variant/{id}',[PrintfulController::class,'getAllVariants']);
        });

        //Mark:- Shipment Related Routes
        Route::group(['prefix'=>'shipment'],function (){
            Route::post('calculate',[PrintfulController::class,'calculateShipmentCost']);
            Route::get('information/{id}',[PrintfulController::class,'infoShipment']);
        });

        //Mark:- Order Related Routes
        Route::group(['prefix'=>'orders'],function (){
            Route::get('/',[PrintfulController::class,'getOrder']);
            Route::post('/',[PrintfulController::class,'createOrder']);
            Route::post('confirmation/{id}',[PrintfulController::class,'confirmOrder']);
            Route::get('cancel/{id}',[PrintfulController::class,'cancelOrder']);
            Route::get('track/{id}',[PrintfulController::class,'trackOrder2']);
            Route::get('user/{user_id}',[PrintfulController::class,'userTrackOrder']);
            Route::post('estimate-costs',[PrintfulController::class,'calculateCompleteShipmentCost']);
        });

        //Mark:- Payment Related Routes
        Route::group(['prefix'=>'payment'],function (){
            Route::post('intent',[PrintfulPaymentController::class,'createPaymentIntent']);
            Route::post('process',[PrintfulPaymentController::class,'processPayment']);
            Route::post('charge',[PrintfulPaymentController::class,'makeACharge']);
        });

        //Mark:- Payment Related Routes
        Route::group(['prefix'=>'app'],function (){
            Route::get('details',[PrintfulController::class,'appDetails']);
        });

        //Mark:- QR Related Routes
        Route::group(['prefix'=>'qr'],function (){
            Route::get('all',[QrController::class,'fetchAllQr']);
            Route::get('single/{id}',[QrController::class,'fetchSingleQr']);
            Route::get('user/{user_id}',[QrController::class,'fetchUserAllQr']);
            Route::get('scan/{qr_code_id}',[QrController::class,'scanQr']);
            Route::post('create-print-order',[QrController::class,'createPrintOrder']);
            Route::post('update-print-order/{id}',[QrController::class,'updatePrintOrderStatus']);
            Route::post('create-print-order-log',[QrController::class,'createPrintOrderLog']);
            Route::post('create-print-order-payment',[QrController::class,'createOrderPayment']);
            Route::post('create-print-order-payment-log',[QrController::class,'createOrderPaymentLog']);
        });

        //Mark:- Webhooks Related Routes
        Route::group(['prefix' => 'webhook'], function () {
            Route::post('/', [WebhookController::class, 'index']);
            Route::get('configuration', [WebhookController::class, 'getWebhookConfiguration']);
            Route::post('configuration', [WebhookController::class, 'webhookConfiguration']);
            Route::post('package-shipped', [WebhookController::class, 'packageShipped']);
            Route::post('order-failed', [WebhookController::class, 'orderFailed']);
            Route::post('order-canceled', [WebhookController::class, 'orderCanceled']);
            Route::post('order-refunded', [WebhookController::class, 'orderRefunded']);
            Route::post('product-updated', [WebhookController::class, 'productUpdated']);
            Route::post('product-deleted', [WebhookController::class, 'productDeleted']);
            Route::post('order-put-hold', [WebhookController::class, 'orderOnHold']);
            Route::post('order-remove-hold', [WebhookController::class, 'orderRemoveFromHold']);
            Route::post('package-returned', [WebhookController::class, 'packageReturned']);
            Route::post('order-created', [WebhookController::class, 'orderCreated']);
            Route::post('order-updated', [WebhookController::class, 'orderUpdated']);
        });

    });

});

