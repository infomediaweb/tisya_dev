<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Index as Index;
use App\Livewire as LiveWire;

// use App\Http\Controllers\BookingController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\AboutusController;
use App\Http\Controllers\ContactusController;
use App\Http\Controllers\CancellationRefundPolicyController;
use App\Http\Controllers\BecomeahostController;
use App\Http\Controllers\TermsandConditionsController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ScriptController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\RuBookingController;
use App\Http\Controllers\RuLiveNotificationWebhookController;
use App\Http\Controllers\RazorpayController;
use App\Http\Controllers\Faqcontroller;
use App\Http\Controllers\LandingpageEnquieryController;
use App\Http\Controllers\PmsApi;


Route::get('ru/set/live/notification/webhook', [RuLiveNotificationWebhookController::class, 'setLiveNotificationWebhook']);
Route::get('/get/ru/bookings/cron', function () {
    Artisan::call("job:getRuBookings");
    dd('Config cleared successfully.');
});

// Route::get('/', IndexController::class)->name('index');
Route::get('/', [IndexController::class, 'index'])->name('index');
Route::get('/form', [IndexController::class, 'landing'])->name('landing');
Route::get('/properties', [IndexController::class, 'properties'])->name('properties');


 //Route::get('/property-detail/{slug?}', [IndexController::class, 'propertyDetail'])->name('property-detail');
Route::get('/property-detail/{home_type}/{slug}', [IndexController::class, 'propertyDetail'])->name('property-detail');

Route::get('specialoffers', [IndexController::class, 'specialoffers'])->name('specialoffers');
// Route::get('/property-detail', [IndexController::class, 'propertyDetail'])->name('property-detail');
Route::get('/get/ajax/property/price', [IndexController::class, 'PropertyPriceFilter']);
Route::get('/aboutus', [AboutusController::class, 'index'])->name('aboutus');

Route::get('location-property/{slug_location}', [IndexController::class, 'locationAllProperty'])->name('location-property');
Route::get('collection-property/{slug}', [IndexController::class, 'collectionProperty'])->name('collection-property');
Route::get('tag-property-list/{tag_name}', [IndexController::class, 'TagPropertyList'])->name('tag-property-list');
Route::get('state-property-list/{state}', [IndexController::class, 'stateAllProperty'])->name('state-property-list');
Route::get('/collections-property-list', [IndexController::class, 'collectionsPropertyList'])->name('collections-property-list');

Route::get('/becomeahost', [BecomeahostController::class, 'index'])->name('becomeahost');
Route::post('/becomeahost/submit', [BecomeahostController::class, 'becomeahost'])->name('host');

Route::post('/landingenquire/submit', [LandingpageEnquieryController::class, 'landingenquireSave'])->name('landingenquire');


Route::post('/enquire', [BecomeahostController::class, 'enquireSave'])->name('enquire');

Route::get('/cancellationandrefund', [CancellationRefundPolicyController::class, 'index'])->name('cancellationandrefund');

Route::get('/privacypolicy', [PrivacyPolicyController::class, 'index'])->name('privacypolicy');

Route::get('/termsandconditions', [TermsandConditionsController::class, 'index'])->name('termsandconditions');

Route::get('/contactus', [ContactusController::class, 'index'])->name('contactus');
Route::post('/contactus/submit', [ContactusController::class, 'contact'])->name('contactus.submit');

Route::get('/blog', [BlogController::class, 'blogs'])->name('blog');
Route::get('/blogdetails/{slug}', [BlogController::class, 'blogdetails'])->name('blogdetails');

Route::get('/faq', [Faqcontroller::class, 'faq'])->name('faq');


Route::get('location-session-save', [IndexController::class, 'locationSessionSave'])->name('location-session-save');
Route::get('property-list', [IndexController::class, 'PropertyList'])->name('property-list');

Route::post('property-detail/booking/apply/coupon/code', [IndexController::class, 'applyBookingCouponCode'])->name('booking.apply.coupon.code');
Route::any('/book/property', [IndexController::class, 'bookProperty'])->name('website.customer.property.book');
Route::post('property-booking', [BookingController::class, 'savePropertyBookingWebsite'])->name('property-booking');
Route::get('/razorpay-payment-success', [BookingController::class, 'handlePaymentBookingWebsite'])->name('property-booking-update');
Route::get('thankyou', [BookingController::class, 'thankYouPage'])->name('payment.thankYou');
Route::get('payment-failure', [BookingController::class, 'PaymentFaild'])->name('payment-failure');

Route::get('ru/price', [ScriptController::class, 'ruPrice']);
Route::get('ru/availability', [ScriptController::class, 'ruAvaliability']);
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

Route::get('/update/ru/price', function () {
    Artisan::call("urpp:job");
    dd('Config cleared successfully.');
});

Route::get('/db/migrate', function () {
    Artisan::call("migrate");
    dd('Migration Completed successfully.');
});
Route::get('pdf/{id}', [ScriptController::class, 'pdf']);

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

Route::get('/ajax-filter-property', [IndexController::class, 'ajaxFilterProperties'])->name('ajax-filter-propperty');

Route::any('envelope/booking/OTA/reservation', [PmsApi\DashboardController::class, 'hyperGuestResponseNew']);

Route::any('envelope/booking/OTA/reservation2', [PmsApi\HyperGuestWebhook::class, 'hyperGuestResponseNew']);


Route::get('/pms/{any?}', function () {
    return view('welcome'); // Replace with your actual view
})->where('any', '.*'); // This will accept anything after "/pms"


Route::fallback(function () {
    return redirect('/'); // Redirect to the index page if no route matches
});



// ========================================= Routes =======================================================//




