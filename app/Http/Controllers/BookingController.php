<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\TblHome;
use App\Models\TblTestimonial;
use Livewire\Component;
use App\Models\TblLocation;
use App\Models\TblOurDifference;
use App\Models\TblSpecialInvitation;
use App\Models\TblHomeBanner;
use App\Models\TblSecondHome;
use App\Models\PropertyBooking;
use App\Models\TblBlog;
use App\Models\RuPropertyAvailability;
use Livewire\Mechanisms\FrontendAssets\FrontendAssets;
use Illuminate\Support\Facades\Validator;
use App\helper\MasterHelper;
use Illuminate\Support\Facades\Auth;
use App\Models\BookingEnquiry;
use App\Services\RazorpayService;
use App\Models\TblHomeImageVideo;
use App\Models\HomeFooterBanner;
use App\Mail\BookingConfirmationEmail;
use Mail;


class BookingController extends Controller{

    public function __construct(RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }

    public function index(Request $request) {

        $search_parameters = array('destination'=>$request->destination, 'arrival_date'=>$request->arrival_date, 'departure_date'=>$request->departure_date, 'age_18_plus'=>$request->age_18_plus, 'age_6_17'=>$request->age_6_17, 'under_5'=>$request->under_5, 'total_guests'=>(int)$request->age_18_plus + (int)$request->age_6_17 + (int)$request->under_5);

        session(['search_parameters'=> $search_parameters]);

        $home_banner = TblHomeBanner::where('status', 1)->whereNull('deleted_at')->orderBy('position', 'asc')->get();
        $diffrences = TblOurDifference::where('status', 1)->whereNull('deleted_at')->orderBy('position', 'asc')->get();
        $special_invitation = TblSpecialInvitation::where('status', 1)->whereNull('deleted_at')->orderBy('position', 'asc')->get();

        $locations = TblLocation::where('status', 1)->whereNull('deleted_at')->whereNull('deleted_at')->get();
        $second_home = TblSecondHome::where('status', 1)->whereNull('deleted_at')->get();
        $recent_blog = TblBlog::where('status', 1)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->get();
        // Iterate over each testimonial to fetch the home name
        $testimonials = TblTestimonial::leftJoin('tbl_homes', 'tbl_testimonials.home_name', '=', 'tbl_homes.id')
        ->select('tbl_testimonials.*', 'tbl_homes.home_name as home_name', 'tbl_homes.location as location')
        ->where('tbl_testimonials.status', 1)
        ->whereNull('tbl_testimonials.deleted_at')
        ->orderBy('tbl_testimonials.position', 'asc')
        ->get();

        $guestOptions  = array(
            0 => array(
                'name' => 'age_18_plus',
                'title' => 'Adults',
                'sub_title' => 'Ages 18+',
                'count' => $request->age_18_plus
            ),
            1 => array(
                'name' => 'age_6_17',
                'title' => 'Children',
                'sub_title' => 'Ages 6 -17',
                'count' => $request->age_6_17
            ),
            2 => array(
                'name' => 'under_5',
                'title' => 'Infants',
                'sub_title' => 'Under 5',
                'count' => $request->under_5
            )
        );

        $guests = $request->age_18_plus + $request->age_6_17 + $request->under_5;

        $arrivalDate = Carbon::createFromFormat('jS M', $request->arrival_date)->format('Y-m-d');
        $departureDate = Carbon::createFromFormat('jS M', $request->departure_date)->format('Y-m-d');

        $properties = DB::table('ru_property_availabilities')
            ->whereBetween('availability_date', [$arrivalDate, $departureDate])
            ->where('status', 1)
            ->where('is_available', 'yes')
            ->whereNull('deleted_at')
            ->groupBy('ru_property_id')
            ->havingRaw('SUM(CASE WHEN is_available = "no" THEN 1 ELSE 0 END) = 0')
            ->pluck('ru_property_id');

        // =========================================================

        // Initialize an array to store valid property IDs
        $validPropertyIds = [];

        // Loop through each ru_property_id in the $properties array
        foreach ($properties as $propertyId) {
            // Initialize a flag to check availability for the current property
            $propertyAvailable = true;

            // Loop through each date between arrivalDate and departureDate
            $currentDate = $arrivalDate;
            while ($currentDate <= $departureDate) {
                // Check if there exists a record in ru_property_availabilities table for the current date
                $availability = RuPropertyAvailability::where('ru_property_id', $propertyId)
                    ->where('availability_date', $currentDate)
                    ->where('is_available', 'yes')
                    ->first();

                // If availability is not found or is not available, set the flag to false and break the loop
                if (!$availability) {
                    $propertyAvailable = false;
                    break;
                }

                // Move to the next date
                $currentDate = Carbon::parse($currentDate)->addDay()->toDateString();
            }

            // If the property is available for all dates, store the property ID
            if ($propertyAvailable) {
                $validPropertyIds[] = $propertyId;
            }
        }
        // ***************  if price is empty for any property then exclude these property from final data
        $properties = TblHome::query()
            ->when(!empty($request->destination), function ($query) use ($request) {
                return $query->where('location', $request->destination);
            })
            ->whereNull('deleted_at')
            ->with(['price', 'images', 'amenities'])
            ->when($guests > 0, function ($query) use ($guests) {
                return $query->where('maximum_number_of_guests', '>=', $guests);
            })
            ->whereIn('ru_property_id', $validPropertyIds)
            ->where('status', 1)
            ->get();

        // Filter out properties with empty prices
        $properties = $properties->filter(function ($property) {
            return !$property->price->isEmpty();
        });

        return view('frontend.properties', compact( 'properties', 'home_banner', 'locations', 'diffrences', 'special_invitation', 'second_home', 'recent_blog', 'testimonials', 'guestOptions'));

    }


    public function filter_booking(Request $request){

        $home_banner = TblHomeBanner::where('status', 1)->whereNull('deleted_at')->get();
        $locations = TblLocation::where('status', 1)->whereNull('deleted_at')->get();
        $diffrences = TblOurDifference::where('status', 1)->whereNull('deleted_at')->get();
        $special_invitation = TblSpecialInvitation::where('status', 1)
        ->whereNull('deleted_at')
        ->orderBy('position', 'asc')
        ->get();
        $second_home = TblSecondHome::where('status', 1)->whereNull('deleted_at')->get();
        $recent_blog = TblBlog::where('status', 1)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->get();
        // Iterate over each testimonial to fetch the home name
        $testimonials = TblTestimonial::leftJoin('tbl_homes', 'tbl_testimonials.home_name', '=', 'tbl_homes.id')
            ->select('tbl_testimonials.*', 'tbl_homes.home_name as home_name','tbl_homes.location as location' )
            ->where('tbl_testimonials.status', 1)
            ->whereNull('tbl_testimonials.deleted_at')
            ->get();
        $guestOptions  = array(
            0=>array(
                'name'=>'age_18_plus',
                'title'=>'Adults',
                'sub_title'=>'Ages 18+',
                'count'=>0
            ),
            1=>array(
                'name'=>'age_6_17',
                'title'=>'Children',
                'sub_title'=>'Ages 6 -17',
                'count'=>0
            ),
            2=>array(
                'name'=>'under_5',
                'title'=>'Infants',
                'sub_title'=>'Under 5',
                'count'=>0
            )
        );
        $guests = $request->age_18_plus + $request->age_6_17 + $request->under_5;
        $propertiesQuery = TblHome::with(['images', 'price']);
        // Check if pets_friendly key exists in the request
        if ($request->has('pets_friendly')) {
            $propertiesQuery->where('pet_friendly', 1);
        }
        $properties = $propertiesQuery->get();
        // If pets_friendly key doesn't exist in the request, set $properties to an empty collection
        if (!$request->has('pets_friendly')) {
            $properties = collect();
        }
        return view('frontend.properties', compact('properties','home_banner', 'locations', 'diffrences', 'special_invitation', 'second_home', 'recent_blog', 'testimonials', 'guestOptions'));
    }

    public function property_details($slug) {
        // Fetch property details from TblHome modal where tbl_home.id = $id
        $property = TblHome::with(['imagesVideos', 'cprice', 'homeFeatures', 'homeReviews'])->where('url_key', $slug)->first();
        // If property is not found, you may want to handle this case based on your application logic
        if (!$property) {
            abort(404); // or return a view with a message indicating that the property was not found
        }
        // Load the view and pass the property details
        $propertyUnavailableDates = DB::table('ru_property_availabilities')->where('ru_property_id', $property->ru_property_id)->where('is_available', 'no')->get()->pluck('availability_date')->toArray();
        $propertyAailableDates = DB::table('ru_property_availabilities')->where('ru_property_id', $property->ru_property_id)->where('is_available', 'yes')->where('availability_date', '>=', date('Y-m-d'))->get()->pluck('availability_date')->toArray();
        $checkInDate = date('Y-m-d');
        $checkOutDate = date('Y-m-d');
        $age_18_plus_count = 1;
        $age_6_17_count = 0;
        $under_5_count = 0;
        $total_guest_count = 1;
        $no_of_nights = 1;

        $no_of_nights = 1;
        $no_of_nights = 1;
        $total_price = $property->cprice->price;

        if(session()->has('search_parameters')){
            $checkInDate = Carbon::createFromFormat('jS M', session('search_parameters')['arrival_date'])->format('d M Y');
            $checkOutDate = Carbon::createFromFormat('jS M', session('search_parameters')['departure_date'])->format('d M Y');
            $age_18_plus_count = session('search_parameters')['age_18_plus'];
            $age_6_17_count = session('search_parameters')['age_6_17'];
            $under_5_count = session('search_parameters')['under_5'];
            $total_guest_count = session('search_parameters')['total_guests'];
            $day = MasterHelper::getDateDifference($checkInDate, $checkOutDate);
            $no_of_nights = (int)$day;
            $total_price = $total_price*$no_of_nights;
        }
        else{
            foreach($propertyAailableDates as $propertyAailableDateKey=>$propertyAailableDate){
                $day = MasterHelper::getDateDifference($propertyAailableDate, $propertyAailableDates[$propertyAailableDateKey + 1]);
                if($day == 1){
                    $checkInDate =  date("d M Y", strtotime($propertyAailableDate));
                    $checkOutDate = date("d M Y", strtotime($propertyAailableDates[$propertyAailableDateKey + 1]));
                    break;
                }
            }
        }

        $guestOptions  = array(
            0=>array(
                'name'=>'age_18_plus',
                'title'=>'Adults',
                'sub_title'=>'Ages 18+',
                'count'=>$age_18_plus_count,
                'min'=>1,
                'max'=>$property->maximum_number_of_guests
            ),
            1=>array(
                'name'=>'age_6_17',
                'title'=>'Children',
                'sub_title'=>'Ages 6 -17',
                'count'=>$age_6_17_count,
                'min'=>0,
                'max'=>$property->maximum_number_of_guests
            ),
            2=>array(
                'name'=>'under_5',
                'title'=>'Infants',
                'sub_title'=>'Under 5',
                'count'=>$under_5_count,
                'min'=>0,
                'max'=>$property->maximum_number_of_guests
            )
        );
        return view('frontend.property_details', compact('property', 'guestOptions', 'propertyUnavailableDates', 'checkInDate', 'checkOutDate', 'total_guest_count', 'no_of_nights', 'total_price'));
    }


    public function customerPropertyBook(Request $request){
        // dd($request->all());
        $properties = TblHome::with(['imagesVideos', 'cprice', 'homeFeatures', 'homeReviews'])->where('id', $request->id)->first();
        // If property is not found, you may want to handle this case based on your application logic
        $bookingInfo = $request->all();
        return view('frontend.book-property', compact('properties', 'bookingInfo'));
    }


    public function customerPropertyBookFormPost(Request $request){
        $rules = array();
        $rules['first_name'] = 'required';
        $rules['last_name'] = 'required';
        $rules['phone_number'] = 'required';
        $rules['email'] = 'required';
        $rules['state'] = 'required';
        $rules['city'] = 'required';
        if(isset($request->company_info)){
            $rules['company_name'] = 'required';
            $rules['gst_no'] = 'required';
            $rules['company_state'] = 'required';
            $rules['company_city'] = 'required';
        }
        $validated = $request->validate($rules);
    }


    public function customerEnquiry(Request $request){
        $rules = array();
        $rules['enquiry_person_name'] = 'required||regex:/^[\pL\s]+$/u|min:3';
        $rules['enquiry_person_mobile'] = 'required|numeric|digits:10';
        $rules['enquiry_person_email'] = 'required|email';
        $rules['enquiry_person_message'] = 'required';
        $validator = $request->validate($rules);

        $enquiryDetail = array();
        $enquiryDetail['location_id'] = $request->location_id;
        $enquiryDetail['property_id'] = $request->property_id;
        $enquiryDetail['no_of_guest'] = $request->total_guest;
        $enquiryDetail['no_of_night'] = $request->no_of_nights;
        $enquiryDetail['name'] = $request->enquiry_person_name;
        $enquiryDetail['phone_no'] = $request->enquiry_person_mobile;
        $enquiryDetail['email'] = $request->enquiry_person_email;
        $enquiryDetail['enquiry_message'] = $request->enquiry_person_message;
        $enquiryDetail['total_amount'] = (integer)(str_replace(",", "", $request->total_price));
        $enquiryDetail['checkin_date'] = $request->checkin_date;
        $enquiryDetail['checkout_date'] = $request->checkout_date;

        BookingEnquiry::create($enquiryDetail);

        return response()->json(['status'=>true, 'errors'=>'', 'message'=>'Thank you for your query. We will get back to you soon.']);
    }
    
    
    public function savePropertyBookingWebsite(Request $request){
       
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'country_code' => 'required',
            'phone_number' => 'required|digits_between:6,12',
            'email' => 'required',
            'state' => 'required',
            'city' => 'required',
            //'address' => 'required',
            //'postal_code' => 'required',
           // 'country' => 'required',
        ]);
        //dd($request);

        if($validator->passes()){
            $amount = filter_var($request->num_formatted_tot_price, FILTER_SANITIZE_NUMBER_INT);
            $orderId = $this->razorpayService->createOrder((integer)$amount);
            $homeDetail = TblHome::where('id', $request->property_id)->first();
            $propertyBooking = new PropertyBooking();
            $propertyBooking->location_id = $homeDetail->location_id;
            $propertyBooking->property_id = $request->property_id;
            $propertyBooking->razorpay_order_id = $orderId;
            $propertyBooking->total_amount = filter_var($request->num_formatted_tot_price, FILTER_SANITIZE_NUMBER_INT);

            if($request->discountCode && $request->discountAmount){
                $propertyBooking->discount_amount = $request->discountAmount;
                $propertyBooking->applied_discount_coupon = $request->discountCode;
            }

            $propertyBooking->payable_amount = filter_var($request->num_formatted_tot_price, FILTER_SANITIZE_NUMBER_INT); 
            if($request->additionalChargesAmount){
                //$propertyBooking->additional_charges_detail = json_encode($bookingDetail['additionalCharges']);
                $propertyBooking->tot_additional_charge = $request->additionalChargesAmount;
            }

            $propertyBooking->tax_amount = filter_var($request->formatted_total_taxable_amount, FILTER_SANITIZE_NUMBER_INT);

            $propertyBooking->booking_id = rand(10000000, 99999999);
            $propertyBooking->booking_status = 'Pending';
            $propertyBooking->booking_created_by = 'admin';
            $propertyBooking->type = 'location';
            $propertyBooking->no_of_children = $request->childrenCount;
            $propertyBooking->no_of_adult = $request->adultsCount;
            $propertyBooking->customer_detail = json_encode(array('first_name'=>$request->first_name, 'last_name'=>$request->last_name, 
            'email'=>$request->email , 'country_code'=>$request->country_code, 'mobile_number'=>$request->phone_number, 'state'=>$request->state, 'city'=>$request->city));
            $propertyBooking->checkin_date = date('Y-m-d', strtotime($request->ci_date));
            $propertyBooking->checkout_date = date('Y-m-d', strtotime($request->co_date)); 
            $propertyBooking->per_night_price = str_replace(',', '', $request->price_per_night_num_formatted);
            $propertyBooking->no_of_nights = $request->tot_no_of_days;
            $propertyBooking->tax = $request->tax;
            $propertyBooking->channel = 'Website';
          //  $propertyBooking->user_id = Auth::guard('users')->id();
            $propertyBooking->save();
            DB::table('booking_guest_ids')->insert(['name'=>$request->first_name.' '.$request->last_name, 'email'=>$request->email, 'mobile_no'=>$request->phone_number]);
            $property  = $propertyBooking->load('property')->property;
            $razorpayId = config('services.razorpay.key');
            return response()->json([
                'status' => true,
                'orderId' => $orderId,
                'razorpayId' => $razorpayId,
                'amount' => $amount,
                'message' => "Property Booked Successfully",
            ]);

        }
        else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
             ]);
        }    
    }

    public function handlePaymentBookingWebsite(Request $request){
        $PropertyBooking_data  = PropertyBooking::with('property')->where('razorpay_order_id', $request->razorpay_order_id)->first();
        $PropertyBooking_data->update([
            'payment_status' => 'Paid',
            'booking_status' => 'paid',   
            'paid_amount' => $PropertyBooking_data->payable_amount,    
            'property_booking_status' => 'Confirmed',
            'transcation_id' => $request->razorpay_payment_id,
        ]);
        //BookingQuotation::where('id', $PropertyBooking_data->booking_quotation_id)->update(['booking_status'=>'Booked']);
        blockPropertyAvailabilityInRu(
            $PropertyBooking_data->property->ru_property_id, 
            date('Y-m-d', strtotime($PropertyBooking_data->checkin_date)), 
            date('Y-m-d', strtotime($PropertyBooking_data->checkout_date))
        );
        return response()->json([
            'status' => true,
            'orderId' => base64_encode($request->razorpay_order_id),
            'message' => "Payment successful",
        ]);


    }

    public function thankYouPage(Request $request){
        $orderId = $request->query('orderId');
        $razorpay_order_id = base64_decode($orderId);
        $PropertyBooking_data  = PropertyBooking::with('property.homeImageVideo')->where('razorpay_order_id', $razorpay_order_id)->first();
        $prorpertyAssetsDetail = TblHomeImageVideo::where('home_id', $PropertyBooking_data->property_id)->where('type', 'image')->first();
        $location_data = TblLocation::with('property.homeImageVideo')
        ->where('tbl_location.status', 1)
        ->whereNull('tbl_location.deleted_at')->get();
        $home_footer_banners = HomeFooterBanner::get();
        $home_footer_banners = $home_footer_banners->map(function ($home_footer_banner) {
        $home_footer_banner->list_content = json_decode($home_footer_banner->list_content, true); // Pass true for associative array
        return $home_footer_banner;
        });
        $pagename = 'booking-confirmation.php';
        $guest_data = "";
       
       if (is_string($PropertyBooking_data->customer_detail)) {
            $customerDetails = json_decode($PropertyBooking_data->customer_detail, true);
        } else {
            $customerDetails = $PropertyBooking_data->customer_detail; // Assume it's already an array
        }
        $email = is_array($customerDetails) ? $customerDetails['email'] : $customerDetails->email;
        Mail::to($email)->send(new BookingConfirmationEmail(array('mailData'=>$PropertyBooking_data, 'type'=>'customer')));
        
        return view('frontend.booking.thankyou',  compact('guest_data', 'prorpertyAssetsDetail', 'PropertyBooking_data', 'pagename', 'location_data','home_footer_banners'));
        
    }
    public function PaymentFaild(Request $request)
    {
        $location_data = TblLocation::with('property.homeImageVideo')
        ->where('tbl_location.status', 1)
        ->whereNull('tbl_location.deleted_at')->get();
        $home_footer_banners = HomeFooterBanner::get();
        $home_footer_banners = $home_footer_banners->map(function ($home_footer_banner) {
        $home_footer_banner->list_content = json_decode($home_footer_banner->list_content, true); // Pass true for associative array
        return $home_footer_banner;
        });
        $pagename = 'booking-confirmation.php';
       // $guest_data = TBGuest::get();
       $guest_data = "";
        return view('frontend.booking.payment-faild',  compact('guest_data', 'pagename', 'location_data','home_footer_banners'));
    }
}
