<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PmsApi;
use App\Http\Controllers\RuBookingController;
use App\Http\Controllers\RuLiveNotificationWebhookController;
use App\Http\Controllers\PmsApi\HyperGuestController;
use App\Http\Controllers\PmsApi\HyperGuest\HyperGuestAvailibilty;
use App\Http\Controllers\PmsApi\HyperGuest\HyperGuestRate;

//use App\Http\Controllers\PmsApi\Location;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::any('get/hyperguest/webhook', [PmsApi\DashboardController::class, 'hyperGuestResponse']);
Route::any('envelope/booking/OTA/reservation', [PmsApi\DashboardController::class, 'hyperGuestResponseNew']);



Route::post('linechart', [PmsApi\DashboardController::class, 'dashboardLineChart']);
Route::post('dashboard', [PmsApi\DashboardController::class, 'dashboardAnalytics']);
Route::post('weeklyreport', [PmsApi\DashboardController::class, 'dashboardWeeklyReport']);
Route::post('channelrevenue', [PmsApi\DashboardController::class, 'dashboardChannelRevenue']);
Route::get('dashboard/property', [PmsApi\DashboardController::class, 'property']);
Route::get('dashboard/location', [PmsApi\DashboardController::class, 'location']);
Route::get('dashboard/channel', [PmsApi\DashboardController::class, 'channel']);
Route::post('net-revenue', [PmsApi\DashboardController::class, 'netRevenueDetails']);
Route::post('average-price-per-night', [PmsApi\DashboardController::class, 'averagePricePerNightDetails']);
Route::post('bookings-created', [PmsApi\DashboardController::class, 'bookingsCreatedDetails']);


Route::post('upload', [PmsApi\UploadController::class, 'upload']);
Route::any('ru/webhook/get/bookings/{hash?}', [RuBookingController::class, 'getBookingFromRu']);
Route::any('ru/webhook/get/live/notification/{hash?}', [RuLiveNotificationWebhookController::class, 'getLiveNotificationWebhook']);
Route::middleware('auth:sanctum')->group(function() {
    ////////////////////////////////////
    
    
    
    Route::post("/pms-logout",[PmsApi\PmsAuthController::class,'logout']);
    Route::post("/pms-changepassword",[PmsApi\PmsAuthController::class,'changepassword']);
    // Service based contrllers and routes
    Route::get('states', [PmsApi\StatesController::class, 'getStates']);
    Route::resource('location', PmsApi\LocationController::class);
    Route::post('location/{id}', [PmsApi\LocationController::class, 'update']);
    Route::post('location-update-status/{id}', [PmsApi\LocationController::class, 'updateStatus']);
    Route::post('location-delete-multiple', [PmsApi\LocationController::class, 'deleteMultipleRecord']);
    Route::get('location-by-state/{state_id}', [PmsApi\LocationController::class, 'getLocations']);
    Route::get('location/get/all', [PmsApi\LocationController::class, 'allLocation']);
    Route::get('location-image-delete/{id}', [PmsApi\LocationController::class, 'deleteImageLocation']);
    Route::post('show-on-home-location/{id}', [PmsApi\LocationController::class, 'updateShowOnLocation']);
    
    
    Route::resource('collection', PmsApi\CollectionController::class);
    Route::post('collection/{id}', [PmsApi\CollectionController::class, 'update']);
    Route::post('collection-update-status/{id}', [PmsApi\CollectionController::class, 'updateStatus']);
    Route::post('collection-delete-multiple', [PmsApi\CollectionController::class, 'deleteMultipleRecord']);
    Route::get('get-collection', [PmsApi\CollectionController::class, 'getCollection']);
    Route::get('collection/get/all', [PmsApi\CollectionController::class, 'allLocation']);
    Route::get('collection-image-delete/{id}', [PmsApi\CollectionController::class, 'deleteImageLocation']);
    Route::post('show-on-home-collection/{id}', [PmsApi\CollectionController::class, 'updateShowOnCollection']);
    
    Route::resource('terms-and-condition', PmsApi\TermsandConditionController::class);
    Route::post('terms-and-condition', [PmsApi\TermsandConditionController::class, 'store']);
    Route::post('terms-and-condition/{id}', [PmsApi\TermsandConditionController::class, 'update']);
    
    Route::resource('cancellation-policy', PmsApi\CancellationPolicyController::class);
    Route::post('cancellation-policy', [PmsApi\CancellationPolicyController::class, 'store']);
    Route::post('cancellation-policy/{id}', [PmsApi\CancellationPolicyController::class, 'update']);
    
    Route::resource('privacy-policy', PmsApi\PrivacyPolicyController::class);
    Route::post('privacy-policy', [PmsApi\PrivacyPolicyController::class, 'store']);
    Route::post('privacy-policy/{id}', [PmsApi\PrivacyPolicyController::class, 'update']);
    
   //----------------------Company api -----------------------------------//
    Route::resource('company', PmsApi\CompanyController::class);
    Route::post('company/{id}', [PmsApi\CompanyController::class, 'update']);
    Route::post('company-update-status/{id}', [PmsApi\CompanyController::class, 'updateStatus']);
    Route::post('company-delete-multiple', [PmsApi\CompanyController::class, 'deleteMultipleRecord']);
     //----------------------End of Company api -----------------------------------//
    //----------------------Home Type api -----------------------------------//
    Route::resource('hometype', PmsApi\HomeTypeController::class);
    Route::post('hometype/{id}', [PmsApi\HomeTypeController::class, 'update']);
    Route::post('hometype-update-status/{id}', [PmsApi\HomeTypeController::class, 'updateStatus']);
    Route::post('hometype-delete-multiple', [PmsApi\HomeTypeController::class, 'deleteMultipleRecord']);
    Route::get('home-types', [PmsApi\HomeTypeController::class, 'getHomeType']);
    //----------------------End of Home Type api -----------------------------------//

    Route::resource('gst-slabs', PmsApi\GstController::class);
    //----------------------End of GST api -----------------------------------//
     //----------------------Website Markup api -----------------------------------//
    Route::post('set-website-markup', [PmsApi\SitesettingController::class, 'setWebsiteMarkup']);
    Route::resource('setting', PmsApi\SitesettingController::class);
    Route::post('update-gst-allow-setting', [PmsApi\SitesettingController::class, 'changeGstAllowSetting']);

    Route::post('set-website-markup', [PmsApi\SitesettingController::class, 'setWebsiteMarkup']);
    Route::resource('website-markup', PmsApi\SitesettingController::class);
    //----------------------End of Website Markup api -----------------------------------//
     //----------------------Amenities api -----------------------------------//
    Route::resource('amenities', PmsApi\AmenitiesController::class);
    Route::post('amenities/{id}', [PmsApi\AmenitiesController::class, 'update']);
    Route::post('amenities-update-status/{id}', [PmsApi\AmenitiesController::class, 'updateStatus']);
    Route::post('amenities-delete-multiple', [PmsApi\AmenitiesController::class, 'deleteMultipleRecord']);
    Route::get('amenities-image-delete/{id}', [PmsApi\AmenitiesController::class, 'deleteImage']);
    Route::post('show-on-home-filter/{id}', [PmsApi\AmenitiesController::class, 'updateShowOnFilter']);
    
    //----------------------End of Amenities api -----------------------------------//
     //----------------------Home api -----------------------------------//
    Route::resource('home', PmsApi\HomeController::class);
    Route::post('home/{id}', [PmsApi\HomeController::class, 'update']);
    Route::post('home-update-status/{id}', [PmsApi\HomeController::class, 'updateStatus']);
    Route::post('show-on-home-update/{id}', [PmsApi\HomeController::class, 'updateShowOnHome']);
    Route::post('show-enquiry-update/{id}', [PmsApi\HomeController::class, 'updateOnlyForEnquiry']);
     Route::post('show-on-apartment-update/{id}', [PmsApi\HomeController::class, 'updateShowOnApartment']);
    Route::post('home-delete-multiple', [PmsApi\HomeController::class, 'deleteMultipleRecord']);
    Route::get('home-image-delete/{id}', [PmsApi\HomeController::class, 'deleteImage']);
    Route::post('home-gallery', [PmsApi\HomeController::class, 'saveGallery']);
    Route::post('home-video', [PmsApi\HomeController::class, 'saveVideo']);
    Route::post('home-video-position', [PmsApi\HomeController::class, 'saveVideoPosition']);
    Route::post('home-features', [PmsApi\HomeController::class, 'saveFeatures']);
    Route::post('delete-features', [PmsApi\HomeController::class, 'deleteFeatures']);
    Route::get('delete-image-video/{id}', [PmsApi\HomeController::class, 'deleteImageVideo']);
    Route::get('get-home-amenities/{id}', [PmsApi\HomeController::class, 'showAllAmenities']);
    Route::post('save-home-amenities', [PmsApi\HomeController::class, 'saveAmenities']);
    Route::put('save-home-review/{id?}', [PmsApi\HomeController::class, 'saveReviews']);
    Route::get('show-home-review/{id}', [PmsApi\HomeController::class, 'showHomeReviews']);
    Route::post('delete-home-reviews', [PmsApi\HomeController::class, 'deleteReviews']);
    Route::post('delete-multiple-home-reviews', [PmsApi\HomeController::class, 'deleteMultipleReviews']);
    Route::get('only-homes', [PmsApi\HomeController::class, 'getHomeName']);
    Route::post('save-additional-charges', [PmsApi\HomeController::class, 'saveHomeAdditionalCharge']);
    Route::delete('delete-additional-charge/{id}', [PmsApi\HomeController::class, 'deleteHomeAdditionalCharge']);
    Route::post('save-owner-detail', [PmsApi\HomeController::class, 'saveHomeOwnerDetail']);
    Route::get('icons-image-delete/{id}', [PmsApi\HomeController::class, 'deleteImage']);
    Route::get('get-home-tags/{id}', [PmsApi\HomeController::class, 'showAllTags']);
    Route::post('save-home-tags', [PmsApi\HomeController::class, 'saveTags']);
    Route::get('pdf-brochure-delete/{id}', [PmsApi\HomeController::class, 'deleteImagebrochure']);
    Route::get('review_image', [PmsApi\HomeController::class, 'getReviewImage']);
    
    
    //----------------------End of Home api -----------------------------------//
    //----------------------FAQs api -----------------------------------//
    Route::resource('faqs', PmsApi\FaqsController::class);
    Route::post('faqs/{id}', [PmsApi\FaqsController::class, 'update']);
    Route::post('faqs-delete-multiple', [PmsApi\FaqsController::class, 'deleteMultipleRecord']);
    Route::post('faqs-update-status/{id}', [PmsApi\FaqsController::class, 'updateStatus']);
    Route::post('faqs-save-position', [PmsApi\FaqsController::class, 'savePosition']);
    //----------------------End of FAQs api -----------------------------------//
    //-------------------------------- Teams api ----------------------------------------------//
    Route::resource('teams', PmsApi\TeamsController::class);
    Route::post('teams', [PmsApi\TeamsController::class, 'store']);
    Route::post('show', [PmsApi\TeamsController::class, 'store']);
    Route::post('teams/{id}', [PmsApi\TeamsController::class, 'update']);
    Route::post('teams-delete-multiple', [PmsApi\TeamsController::class, 'deleteMultipleRecord']);
    Route::post('teams-update-status/{id}', [PmsApi\TeamsController::class, 'updateStatus']);
    Route::post('teams-save-position', [PmsApi\TeamsController::class, 'savePosition']);
    Route::get('/teams-delete-image/{id}', [PmsApi\TeamsController::class,'deleteImage'])->name('teams-delete-image');
    // -------------------------------- About Us --------------------------------------------- //
    Route::resource('about-us', PmsApi\AboutUsController::class);
    Route::post('about-us', [PmsApi\AboutUsController::class, 'store']);
    Route::post('about-us/{id}', [PmsApi\AboutUsController::class, 'update']);
    Route::get('/about-us-delete-image/{id}', [PmsApi\AboutUsController::class,'deleteImage']);
    // -------------------------------- About Information --------------------------------------------- //
    Route::resource('about-information', PmsApi\AboutInformationController::class);
    Route::post('about-information', [PmsApi\AboutInformationController::class, 'store']);
    Route::post('about-information/{id}', [PmsApi\AboutInformationController::class, 'update']);
    Route::get('/about-information-delete-image/{id}', [PmsApi\AboutInformationController::class,'deleteImage']);
    Route::get('single-delete-record/{id}', [PmsApi\AboutInformationController::class, 'singleDelete']);
    // ----------------------------------- Home Banner -------------------------------------------------//
    Route::resource('home-banner', PmsApi\HomeBannerController::class);
    Route::post('home-banner-save-position', [PmsApi\HomeBannerController::class, 'savePosition']);
    Route::get('/home-banner-delete-image/{id}', [PmsApi\HomeBannerController::class,'deleteImage'])->name('home-banner-delete-image');
    Route::post('home-banner-update-status/{id}', [PmsApi\HomeBannerController::class, 'updateStatus']);

    Route::resource('footer-banner-content', PmsApi\HomeFooterBannerController::class);
    Route::post('home-footer-banner', [PmsApi\HomeFooterBannerController::class, 'saveFooterContent']);
    Route::post('home-footer-banner/{id}', [PmsApi\HomeFooterBannerController::class, 'updateFooterContent']);

    Route::resource('addventure-content', PmsApi\AddventureController::class);
    Route::post('save-addventure', [PmsApi\AddventureController::class, 'saveAddventure']);
    Route::delete('delete-addventure/{id}', [PmsApi\AddventureController::class, 'deleteAddventure']);


  //----------------------Tags api -----------------------------------//
   Route::resource('tags', PmsApi\TagsController::class);
   Route::post('tags/{id}', [PmsApi\TagsController::class, 'update']);
   Route::post('tags-update-status/{id}', [PmsApi\TagsController::class, 'updateStatus']);
   Route::post('tags-delete-multiple', [PmsApi\TagsController::class, 'deleteMultipleRecord']);
   Route::get('tags-image-delete/{id}', [PmsApi\TagsController::class, 'deleteImage']);
   Route::post('show-on-home-tag/{id}', [PmsApi\TagsController::class, 'updateShowOnTag']);

   //----------------------End of Tags api -----------------------------------//



    // ------------------------------------- Our Diffrence --------------------------------------------//
    Route::resource('our-difference', PmsApi\OurDifferenceController::class);
    Route::post('our-difference', [PmsApi\OurDifferenceController::class, 'store']);
    // Route::post('our-difference/{id}', [PmsApi\OurDifferenceController::class, 'update']);
    Route::get('/our-difference-delete-image/{id}', [PmsApi\OurDifferenceController::class,'deleteImage']);
    Route::get('our-difference-delete-record/{id}', [PmsApi\OurDifferenceController::class, 'deleteRecord']);
    // --------------------------------------- Special Invitations -----------------------------------//
    Route::resource('special-invitation', PmsApi\SpecialInvitationController::class);
    Route::post('special-invitation/{id}', [PmsApi\SpecialInvitationController::class, 'update']);
    Route::get('special-invitation-delete-record/{id}', [PmsApi\SpecialInvitationController::class, 'destroy']);
    Route::post('special-invitation-delete-multiple-record', [PmsApi\SpecialInvitationController::class, 'deleteMultipleRecord']);
    Route::post('special-invitation-update-status/{id}', [PmsApi\SpecialInvitationController::class, 'updateStatus']);
    Route::get('special-invitation-delete-image/{id}', [PmsApi\SpecialInvitationController::class, 'deleteimage']);
    Route::post('special-invitation-save-position', [PmsApi\SpecialInvitationController::class, 'savePosition']);
    Route::get('coupon_code', [PmsApi\SpecialInvitationController::class, 'getCouponCode']);
    
    // ---------------------------------------- Testimonial -----------------------------------------//
    Route::resource('testimonials', PmsApi\TestimonialController::class);
    Route::post('testimonials/{id}', [PmsApi\TestimonialController::class, 'update']);
    Route::post('testimonials-delete-multiple-record', [PmsApi\TestimonialController::class, 'deleteMultipleRecord']);
    Route::post('testimonials-update-status/{id}', [PmsApi\TestimonialController::class, 'updateStatus']);
    Route::get('testimonials-delete-image/{id}', [PmsApi\TestimonialController::class, 'deletefile']);
    Route::post('testimonials-save-position', [PmsApi\TestimonialController::class, 'savePosition']);
    // ----------------------------------------- Second Home ---------------------------------------//
    Route::resource('second-home', PmsApi\SecondHomeController::class);
    Route::delete('second-home', [PmsApi\SecondHomeController::class, 'destroy']);
    Route::post('second-home-delete-image', [PmsApi\SecondHomeController::class, 'deleteImage']);
    Route::get('get-ru-location-list', [PmsApi\LocationController::class, 'getRuLocationList']);
    Route::post('/property/list', [PmsApi\PropertyController::class, 'propertyList']);
    Route::post('/property/list/by/id', [PmsApi\PropertyController::class, 'propertyListById']);
    Route::post('/property/booking', [PmsApi\PropertyController::class, 'savePropertyBooking']);
    Route::get('/property/booking/list/{role?}/{userId?}', [PmsApi\PropertyController::class, 'propertyBookingList']);
    Route::get('/property/booking/{id}', [PmsApi\PropertyController::class, 'getPropertyBookingDetail']);
    Route::post('/property/booking/payment/request', [PmsApi\PropertyController::class, 'saveBookingPaymentRequest']);
    Route::get('/cancel/booking/{id}', [PmsApi\PropertyController::class, 'cancelBooking']);
    Route::get('/property/booking/list/test/{role?}/{userId?}', [PmsApi\PropertyController::class, 'propertyBookingListTest']);

    Route::post('/property/booking/deleteguestfile', [PmsApi\PropertyController::class, 'deletePropertyBookingImage']);


    Route::post('/property/booking/payment/request/update', [PmsApi\PropertyController::class, 'updatePaymentRequest']);
    Route::post('/property/booking/payment/request/status/update', [PmsApi\PropertyController::class, 'updatePaymentRequestStatus']);
    Route::post('/property/avaliability/detail', [PmsApi\PropertyController::class, 'propertyAvailabilityDetail']);
    Route::post('/property/unavaliabile/dates', [PmsApi\PropertyController::class, 'getPropertyBookingUnavailableDates']);
    Route::get('/property/booking/request/detail/{id}', [PmsApi\PropertyController::class, 'getPaymentRequestDetail']);
    // Route::post('delete-booking', [PmsApi\PropertyController::class, 'deleteBooking']);
    Route::delete('delete-booking/{id}', [PmsApi\PropertyController::class, 'deleteBooking']);
    Route::post('booking/export', [PmsApi\PropertyController::class, 'bookingExport']);
    Route::post('property/booking/ids', [PmsApi\PropertyController::class, 'savePropertyBookingIds']);
    Route::get('property/bookings/enquiry', [PmsApi\PropertyController::class, 'getBookingEnquiry']);
    Route::get('boooking-invoice/{id}', [PmsApi\PropertyController::class, 'downloadInvoice']);
    // -------------------------------------------- Blogs -------------------------------------------//
    Route::resource('blogs', PmsApi\BlogController::class);
    Route::post('blogs/{id}', [PmsApi\BlogController::class, 'update']);
    Route::post('blogs-update-status/{id}', [PmsApi\BlogController::class, 'updateStatus']);
    Route::get('blogs-delete-image/{id}', [PmsApi\BlogController::class, 'deleteImage']);
    Route::post('blogs-delete-multiple-record', [PmsApi\BlogController::class, 'deleteMultipleRecord']);
    
    Route::post('show-on-home-blog/{id}', [PmsApi\BlogController::class, 'updateShowOnBlog']);
    
    // --------------------------------------------- Join Our Network Intro --------------------------------//
    Route::resource('join-our-network-intro', PmsApi\JoinOurNetworkIntroController::class);
    Route::delete('join-our-network-intro', [PmsApi\JoinOurNetworkIntroController::class, 'destroy']);
    Route::post('join-our-network-intro-delete-image', [PmsApi\JoinOurNetworkIntroController::class, 'deleteImage']);
    // ---------------------------------------------- Join Our Network Faqs ------------------------------//
    Route::resource('join-our-network-faqs', PmsApi\JoinOurNetworkFaqsController::class);
    Route::post('join-our-network-faqs/{id}', [PmsApi\JoinOurNetworkFaqsController::class, 'update']);
    Route::post('join-our-network-faqs-delete-multiple', [PmsApi\JoinOurNetworkFaqsController::class, 'deleteMultipleRecord']);
    Route::post('join-our-network-faqs-update-status/{id}', [PmsApi\JoinOurNetworkFaqsController::class, 'updateStatus']);
    Route::post('join-our-network-faqs-save-position', [PmsApi\JoinOurNetworkFaqsController::class, 'savePosition']);

     //----------------------User api -----------------------------------//
     Route::resource('user-list', PmsApi\UserController::class);
     Route::post('delete-multiple-users', [PmsApi\UserController::class, 'deleteMultipleRecord']);
     Route::delete('delete-user/{id}', [PmsApi\UserController::class, 'destroy']);
     Route::post('user-update-status/{id}', [PmsApi\UserController::class, 'updateStatus']);
        Route::post('save-user-detail', [PmsApi\UserController::class, 'store']);
     Route::post('save-user-detail/{id}', [PmsApi\UserController::class, 'store']);
     Route::get('user-detail/{id}', [PmsApi\UserController::class, 'detail']);
     //----------------------End of User api -----------------------------------//
});
// Route::resource('location', Location::class);


Route::get('/get/property/by/propertyTypes', [PmsApi\CouponCodeController::class, 'getPropertyListByPropertyTypes']);
Route::get('/get/coupon/code/list', [PmsApi\CouponCodeController::class, 'list']);
Route::get('/coupon/code/show/{id}', [PmsApi\CouponCodeController::class, 'show']);
Route::post('/coupon/code/save', [PmsApi\CouponCodeController::class, 'save']);
Route::delete('/coupon/code/delete', [PmsApi\CouponCodeController::class, 'destroy']);
Route::post('/coupon/code/update/status/{id}', [PmsApi\CouponCodeController::class, 'updateStatus']);
Route::delete('/coupon/code/delete/multiple', [PmsApi\CouponCodeController::class, 'deleteMultipleRecord']);
Route::post('/guest/database', [PmsApi\CouponCodeController::class, 'getGuestDatabase']);
Route::post('/guest/database/export', [PmsApi\CouponCodeController::class, 'guestDatabaseExport']);
Route::get('/property/booking/detail/{id}', [PmsApi\PropertyController::class, 'getPropertyBooking']);
Route::post('couponcode/export', [PmsApi\CouponCodeController::class, 'couponCodeExport']);

Route::post('sale/report/{role?}/{userId?}', [PmsApi\PropertyController::class, 'saleReport']);
Route::post('/get/booking/sale/report/export', [PmsApi\PropertyController::class, 'saleReportExport']);
Route::post('police/verification/report', [PmsApi\PropertyController::class, 'policeVerificationReport']);
Route::post('police/verification/report/export', [PmsApi\PropertyController::class, 'policeVerificationExport']);
// -------------------------------------------- Blogs -------------------------------------------//

Route::post("/pms-login",[PmsApi\PmsAuthController::class,'pms_login']);

Route::get('/hyper-guest', [HyperGuestController::class, 'fetchHotel']);
Route::get('/hyper-guest-availibity-push', [HyperGuestAvailibilty::class, 'availibityPush']);
Route::get('/hyper-guest-rate-push', [HyperGuestRate::class, 'ratePush']);

