<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Index as Index;
use App\Livewire as LiveWire;

// use App\Http\Controllers\BookingController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ScriptController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\RuBookingController;
use App\Http\Controllers\RuLiveNotificationWebhookController;
use App\Http\Controllers\RazorpayController;
Route::get('ru/set/live/notification/webhook', [RuLiveNotificationWebhookController::class, 'setLiveNotificationWebhook']);
Route::get('/get/ru/bookings/cron', function () {
    Artisan::call("job:getRuBookings");
    dd('Config cleared successfully.');
});

// Route::get('/', IndexController::class)->name('index');
Route::get('/', [IndexController::class, 'index'])->name('index');
Route::get('ru/price', [ScriptController::class, 'ruPrice']);
Route::get('ru/set/booking/webhook', [RuBookingController::class, 'setBookingHandlerAPi']);
Route::get('rua', [ScriptController::class, 'ruAvaliability']);
Route::get('pdf', [ScriptController::class, 'pdf']);
Route::get('booking-by-id', [ScriptController::class, 'getRuBookingById']);
Route::any('ru/webhook/get/bookings/{hash?}', [RuBookingController::class, 'getBookingFromRu']);
// Route::get('join-our-network/{id}', LiveWire\JoinOurNetworkController::class)->name('join.our.network');

// Route::get('/blogs', LiveWire\Blog::class)->name('blogs');
// Route::get('/blog_detail/{id}', LiveWire\Blog::class)->name('blog_detail');
Route::get('/config/clear', function () {
    Artisan::call("config:clear");
    Artisan::call("cache:clear");
    Artisan::call("route:clear");
    dd('Config cleared successfully.');
});



Route::get('/db/migrate', function () {
    Artisan::call("migrate");
    dd('Migration Completed successfully.');
});
Route::get('pdf/{id}', [ScriptController::class, 'pdf']);
Route::get('/properties/{slug}', [IndexController::class, 'properties'])->name('properties');
Route::post('/booking', [BookingController::class, 'index'])->name('booking');
Route::post('/filter-booking', [BookingController::class, 'filterbooking'])->name('filter-booking');
Route::get('filter_booking_data/{value}', [BookingController::class,'filterbooking'])->name('filter_booking_data');
Route::get('/join-our-network', [IndexController::class, 'joinnetwork'])->name('join-our-network');
Route::get('/about_us', [IndexController::class, 'aboutus'])->name('about_us');
Route::get('/contact_us', [IndexController::class, 'contactus'])->name('contact_us');
Route::get('/contact_us', [IndexController::class, 'contactus'])->name('contact_us');
Route::get('/policy', [IndexController::class, 'policy'])->name('policy');
Route::get('/booking_page', [IndexController::class, 'booking_page'])->name('booking_page');
Route::get('/cancellation_refund', [IndexController::class, 'cancellation_refund'])->name('cancellation_refund');
Route::get('/team', [IndexController::class, 'team'])->name('team');
Route::get('/faqs', [IndexController::class, 'faqs'])->name('faqs');

Route::get('/our_diffrence', [IndexController::class, 'ourdiffrence'])->name('our_diffrence');
// Route::get('filter_booking_data/{value}', [BookingController::class, 'filter_booking_data'])->name('filter_booking_data');

//----------------------------Blog ---------------------------------//
Route::get('/blogs', [BlogController::class, 'blogs'])->name('blogs');
Route::get('/blogs/{slug}', [BlogController::class, 'blogdetails'])->name('blog.detail');
Route::post('/blogs/search', [BlogController::class, 'searchBlog'])->name('blog.search');
Route::post('/blogs/loadmore', [BlogController::class, 'loadMore'])->name('blog.loadmore');
//-----------------------End of Blog -------------------------------//

//----------------------------Booking-----------------------------//
Route::get('/property-details/{slug}', [BookingController::class, 'property_details'])->name('property-details');
Route::any('/customer/property/book', [BookingController::class, 'customerPropertyBook'])->name('customer.property.book');
Route::post('/customer/property/book/form/post', [BookingController::class, 'customerPropertyBookFormPost'])->name('customer.property.book.form.post');
Route::post('customer/enquiry', [BookingController::class, 'customerEnquiry'])->name('customer.enquiry');
//----------------------------End of Booking-----------------------------//

//----------------------------Razorpay API-----------------------------//
Route::any('/razorpay/webhook/callback', [RazorpayController::class, 'handleWebhookCallBack'])->name('handle.razorpay.callback');
Route::get('/payment/received/thankyou/{id}', [RazorpayController::class, 'paymentThankyou'])->name('payment.received.thankyou');
//----------------------------End of Razorpay API-----------------------------//

Route::get('update-ru-bookings', [ScriptController::class, 'updateRuBookings']);

Route::get('payment/refund', [ScriptController::class, 'refundPayment']);

//-------------------------------Cron----------------------//

Route::get('/update/ru/price', function () {
    Artisan::call("urpp:job");
    dd('Price updated successfully.');
});

Route::get('/minstay', function () {
    Artisan::call("SyncMinStayCommandJob");
    dd('success.');
});

Route::get('/ru/availability', function () {
    Artisan::call("propertyAvailabilityJob");
    dd('success');
});


//---------------------------------pms---------------------//

Route::get('/{any}', function () {
    return view('welcome');
})->where("any",".*");



// ========================================= Routes =======================================================//




