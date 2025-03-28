<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\TblHome;
use App\Models\TblLocation;
use App\Models\PropertyBooking;
use App\Models\RuPropertyPrice;
use App\helper\MasterHelper;
use App\Models\RuPropertyAvailability;
use App\Services\HyperGuestService;

class DashboardController extends Controller
{
    protected $hyperguest;

    public function __construct(HyperGuestService $hyperguest)
    {
        $this->hyperguest = $hyperguest;
    }
    
    public function property(){
        try{
            $property = TblHome::select('id','ru_property_id', 'home_name as property_name')->where('status',1)->orderby('home_name','asc')->get();
            // dd($property);
            return response()->json([
                'status' => true,
                'property' => $property,
                'message' => 'Successfully Retrive'
            ], 200);
        }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
        
    }
    
    public function location(){
        try{
            $location = TblLocation::select('id as location_id', 'location_name')->where('status',1)->orderby('location_name','asc')->get();
            // dd($location);
            return response()->json([
                'status' => true,
                'location' => $location,
                'message' => 'Successfully Retrive'
            ], 200);
        }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
        
    } 
    public function channel(){
        try{
            $channel = ['Airbnb','Booking.com','MakeMyTrip','Offline','RU','Website'];
            return response()->json([
                'status' => true,
                'channel' => $channel,
                'message' => 'Successfully Retrive'
            ], 200);
        }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
        
    } 
    
    public function dashboardLineChart(Request $request){
       // $list = PropertyBooking::where('channel', 'Website')->where('total_amount', 0)->get();
         try{
            $location_id = (int) $request->location_id;
            $ru_property_id = (int) $request->ru_property_id;
            $type = $request->type;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            
            if($ru_property_id){
                $tblHome = TblHome::select('id as property_id')->where('ru_property_id',$ru_property_id)->where('status',1)->first();
                $property_id = (int) $tblHome->property_id ?? 0;
            }else{
                $property_id = 0;
            }
            
            $days = $request->days;
            
            
            if($days){
                if($days == '7_next'){
                    $number = preg_replace('/_next$/', '', $days);
                    $next_7days = $number;
                    
                    $from_date = date('Y-m-d');
                    $to_date = date('Y-m-d', strtotime("+{$next_7days} days"));
                    
                    $percentage_from_date = date('Y-m-d', strtotime("-7 days"));
                    $percentage_to_date = date('Y-m-d');
                    
                }else{
                    $from_date = date('Y-m-d', strtotime("-{$days} days"));
                    $to_date = date('Y-m-d', strtotime('-1 day'));
                    if($days == 60){
                        $percentage_from_date = date('Y-m-d', strtotime("-120 days"));
                        $percentage_to_date = date('Y-m-d', strtotime("-60 days"));
                    }elseif($days == 90){
                        $percentage_from_date = date('Y-m-d', strtotime("-180 days"));
                        $percentage_to_date = date('Y-m-d', strtotime("-90 days"));
                    }elseif($days == 30){
                        $percentage_from_date = date('Y-m-d', strtotime("-60 days"));
                        $percentage_to_date = date('Y-m-d', strtotime("-30 days"));
                    }elseif($days == 7){
                        $percentage_from_date = date('Y-m-d', strtotime("-14 days"));
                        $percentage_to_date = date('Y-m-d', strtotime("-7 days"));
                    } 
                }
               
            }elseif($from_date && $to_date){
                $from_date = date('Y-m-d', strtotime($from_date));
                $to_date = date('Y-m-d', strtotime($to_date));
                
                $percentage_from_date = null;
                $percentage_to_date = null;
            }else{
                $from_date = null;
                $to_date = null;
                $percentage_from_date = null;
                $percentage_to_date = null;
            }
          
            /*Net Revenue*/
            $netRevenueValue = PropertyBooking::select(DB::raw('checkin_date as label, ROUND(SUM(total_amount), 0) as price'))
                ->where('payment_status', 'Paid')
                ->where('property_booking_status', 'Confirmed')
                ->when($location_id, function ($query, $location_id) {
                    return $query->where('location_id', $location_id);
                })
                ->when($property_id, function ($query, $property_id) {
                    return $query->where('property_id', $property_id);
                })
                ->whereBetween('checkin_date', [$from_date, $to_date])
                ->groupBy('checkin_date') 
                ->orderBy('checkin_date', 'desc') 
                ->get()
                ->toArray();
            
            $netRevenue = PropertyBooking::where('payment_status', 'Paid')
                    ->where('property_booking_status', 'Confirmed')
                    ->when($location_id, function ($query, $location_id) {
                        return $query->where('location_id', $location_id);
                    })
                    ->when($property_id, function ($query, $property_id) {
                        return $query->where('property_id', $property_id);
                    })
                    ->whereBetween('checkin_date', [$from_date, $to_date])
                    ->sum('total_amount');
            $netRevenue =  (int) round($netRevenue);
  
         
            $perNetRevenue = PropertyBooking::where('payment_status', 'Paid')
                    ->where('property_booking_status', 'Confirmed')
                    ->when($location_id, function ($query, $location_id) {
                        return $query->where('location_id', $location_id);
                    })
                    ->when($property_id, function ($query, $property_id) {
                        return $query->where('property_id', $property_id);
                    })
                    ->whereBetween('checkin_date', [$percentage_from_date, $percentage_to_date])
                    ->sum('total_amount');
            $perNetRevenue =  (int) round($perNetRevenue);
            
            if ($perNetRevenue > 0) {
                $netRevenuePercentageChange = (($netRevenue - $perNetRevenue) / $perNetRevenue) * 100;
                $netRevenuePercentageChange = round($netRevenuePercentageChange, 2);
            } else {
                $netRevenuePercentageChange = $netRevenue > 0 ? 100 : 0;
            }
            
            
            /* Average Price Per Night for Current Period */
            $averagePricesValue = PropertyBooking::selectRaw("checkin_date as label, AVG(CAST(per_night_price AS DECIMAL(10, 2))) AS price")
                ->where('payment_status', 'Paid')
                ->where('property_booking_status', 'Confirmed')
                ->when($location_id, function ($query, $location_id) {
                    return $query->where('location_id', $location_id);
                })
                ->when($property_id, function ($query, $property_id) {
                    return $query->where('property_id', $property_id);
                })
                ->whereBetween('checkin_date', [$from_date, $to_date])
                ->groupBy('checkin_date')
                ->orderBy('checkin_date', 'asc')
                ->get()
                ->toArray();

        
           $averagePrices = PropertyBooking::selectRaw("checkin_date as label, AVG(CAST(per_night_price AS DECIMAL(10, 2))) AS price")
                ->where('payment_status', 'Paid')
                ->where('property_booking_status', 'Confirmed')
                ->when($location_id, function ($query, $location_id) {
                    return $query->where('location_id', $location_id);
                })
                ->when($property_id, function ($query, $property_id) {
                    return $query->where('property_id', $property_id);
                })
                ->whereBetween('checkin_date', [$from_date, $to_date])
                ->groupBy('checkin_date')
                ->orderBy('checkin_date', 'asc')
                ->get();

            $sumAveragePrice = $averagePrices->sum('price');
            $totalCount = $averagePrices->count();
            $averagePricePerNight = $totalCount > 0 ? $sumAveragePrice / $totalCount : 0;
            $averagePricePerNight = (int) round($averagePricePerNight);
  
            
            /* Average Price Per Night for Previous Period */
            $perAveragePrices = PropertyBooking::selectRaw("checkin_date as label, AVG(CAST(per_night_price AS DECIMAL(10, 2))) AS price")
                ->where('payment_status', 'Paid')
                ->where('property_booking_status', 'Confirmed')
                ->when($location_id, function ($query, $location_id) {
                    return $query->where('location_id', $location_id);
                })
                ->when($property_id, function ($query, $property_id) {
                    return $query->where('property_id', $property_id);
                })
                ->whereBetween('checkin_date', [$percentage_from_date, $percentage_to_date])
                ->groupBy('checkin_date')
                ->orderBy('checkin_date', 'asc')
                ->get();
            
            $sumPerAveragePrice = $perAveragePrices->sum('price');
            $totalPerCount = $perAveragePrices->count();
            $perAveragePricePerNight = $totalPerCount > 0 ? $sumPerAveragePrice / $totalPerCount : 0;
            $perAveragePricePerNight = (int) round($perAveragePricePerNight);

            if ($perAveragePricePerNight > 0) {
                $averagePercentagePerNightChange = (($averagePricePerNight - $perAveragePricePerNight) / $perAveragePricePerNight) * 100;
                $averagePercentagePerNightChange = round($averagePercentagePerNightChange, 2);
            } else {
                $averagePercentagePerNightChange = $averagePricePerNight > 0 ? 100 : 0;
            }
        
            /* Created Bookings for Current Period */
           $createdBookingsValue = DB::table('property_bookings')
                ->select(
                    DB::raw('DATE_FORMAT(checkin_date, "%Y-%m-%d") AS label'),
                    DB::raw('ROUND(SUM(payable_amount), 0) AS price') 
                )
                ->where('payment_status', 'Paid')
                ->where('property_booking_status', 'Confirmed')
                ->when($property_id, function ($query, $property_id) {
                    return $query->where('property_id', $property_id);
                })
                ->when($location_id, function ($query, $location_id) {
                    return $query->where('location_id', $location_id);
                })
                ->whereBetween('checkin_date', [$from_date, $to_date])
                ->groupBy(DB::raw('DATE_FORMAT(checkin_date, "%Y-%m-%d")'))
                ->orderBy(DB::raw('DATE_FORMAT(checkin_date, "%Y-%m-%d")'), 'desc')
                ->get();

           
            $createdBookings = DB::table('property_bookings')->where('payment_status', 'Paid')
                ->where('property_booking_status', 'Confirmed')
                ->when($property_id, function ($query, $property_id) {
                    return $query->where('property_id', $property_id);
                })
                ->when($location_id, function ($query, $location_id) {
                    return $query->where('location_id', $location_id);
                })
                ->whereBetween('checkin_date', [$from_date, $to_date])
                ->count();
            
            /* Created Bookings for Previous Period */
            $perCreatedBookings = DB::table('property_bookings')->where('payment_status', 'Paid')
                ->where('property_booking_status', 'Confirmed')
                ->when($property_id, function ($query, $property_id) {
                    return $query->where('property_id', $property_id);
                })
                ->when($location_id, function ($query, $location_id) {
                    return $query->where('location_id', $location_id);
                })
                ->whereBetween('checkin_date', [$percentage_from_date, $percentage_to_date])
                ->count();
            
            /* Calculate Percentage Change */
            if ($perCreatedBookings > 0) {
                $createdBookingsPercentageChange = (($createdBookings - $perCreatedBookings) / $perCreatedBookings) * 100;
                $createdBookingsPercentageChange = round($createdBookingsPercentageChange, 2);
            } else {
                
                $createdBookingsPercentageChange = $createdBookings > 0 ? 100 : 0;
            }
            
            
             /* Nights Filled for Current Period */
           $nightFilledValue = DB::table('property_bookings')
                ->selectRaw('DATE(checkin_date) AS label,  COALESCE(SUM(no_of_nights), 0) AS price')
                ->where('payment_status', 'Paid')
                ->where('property_booking_status', 'Confirmed')
                ->when($location_id, function ($query, $location_id) {
                    return $query->where('location_id', $location_id);
                })
                ->when($property_id, function ($query, $property_id) {
                    return $query->where('property_id', $property_id);
                })
                ->whereBetween('checkin_date', [$from_date, $to_date])
                ->groupBy(DB::raw('DATE(checkin_date)'))
                ->orderBy(DB::raw('DATE(checkin_date)'), 'DESC')
                ->get()
                ->toArray();
 
       
            $nightFilled = DB::table('property_bookings')
                ->when($location_id, function ($query, $location_id) {
                    return $query->where('location_id', $location_id);
                })
                ->when($property_id, function ($query, $property_id) {
                    return $query->where('property_id', $property_id);
                })
                ->whereBetween('checkin_date', [$from_date, $to_date])
                ->where('payment_status', 'Paid')
                ->where('property_booking_status', 'Confirmed')
                ->sum('no_of_nights');
                
            $nightFilled = (int) $nightFilled;
     
            /* Nights Filled for Previous Period */
            $perNightFilled = DB::table('property_bookings')
                ->when($location_id, function ($query, $location_id) {
                    return $query->where('location_id', $location_id);
                })
                ->when($property_id, function ($query, $property_id) {
                    return $query->where('property_id', $property_id);
                })
                ->whereBetween('checkin_date', [$percentage_from_date, $percentage_to_date])
                ->where('payment_status', 'Paid')
                ->where('property_booking_status', 'Confirmed')
                ->sum('no_of_nights');
            $perNightFilled = (int) $perNightFilled;
            
            /* Calculate Percentage Change */
            if ($perNightFilled > 0) {
                $nightFilledPercentageChange = (($nightFilled - $perNightFilled) / $perNightFilled) * 100;
                $nightFilledPercentageChange = round($nightFilledPercentageChange, 2);
            } else {
                
                $nightFilledPercentageChange = $nightFilled > 0 ? 100 : 0;
            }
            
            
            /* Occupancy Rates */
          $results = DB::table('tbl_homes')
            ->join('property_bookings', 'tbl_homes.id', '=', 'property_bookings.property_id')
            ->selectRaw('DATE(property_bookings.checkin_date) AS label, COUNT(property_bookings.id) AS price')
            ->where('property_bookings.payment_status', 'Paid')
            ->where('property_bookings.property_booking_status', 'Confirmed')
            ->when($location_id, function ($query, $location_id) {
                return $query->where('property_bookings.location_id', $location_id);
            })
            ->when($property_id, function ($query, $property_id) {
                return $query->where('property_bookings.property_id', $property_id);
            })
            ->whereBetween('property_bookings.checkin_date', [$from_date, $to_date])
            ->groupBy(DB::raw('DATE(property_bookings.checkin_date)'))
            ->orderBy(DB::raw('DATE(property_bookings.checkin_date)'), 'DESC')
            ->get()
            ->toArray();
            
            $occupancyPercentage = DB::table('tbl_homes')
                ->selectRaw(
                    '(COUNT(DISTINCT tbl_homes.id) * 100.0 / (SELECT COUNT(DISTINCT id) FROM tbl_homes)) AS percentage'
                )
                ->whereIn('tbl_homes.id', function ($query) use ($location_id, $property_id, $from_date, $to_date) {
                    $query->select('property_id')
                        ->distinct()
                        ->from('property_bookings')
                        ->where('payment_status', 'Paid')
                        ->where('property_booking_status', 'Confirmed')
                        ->when($location_id, function ($query, $location_id) {
                            return $query->where('location_id', $location_id);
                        })
                        ->when($property_id, function ($query, $property_id) {
                            return $query->where('property_id', $property_id);
                        })
                        ->whereBetween('checkin_date', [$from_date, $to_date]);
                })
                ->value('percentage');
            
            // Calculate the previous occupancy percentage
            $occupancyPreviousPercentage = DB::table('tbl_homes')
                ->selectRaw(
                    '(COUNT(DISTINCT tbl_homes.id) * 100.0 / (SELECT COUNT(DISTINCT id) FROM tbl_homes)) AS percentage'
                )
                ->whereIn('tbl_homes.id', function ($query) use ($location_id, $property_id, $percentage_from_date, $percentage_to_date) {
                    $query->select('property_id')
                        ->distinct()
                        ->from('property_bookings')
                        ->where('payment_status', 'Paid')
                        ->where('property_booking_status', 'Confirmed')
                        ->when($location_id, function ($query, $location_id) {
                            return $query->where('location_id', $location_id);
                        })
                        ->when($property_id, function ($query, $property_id) {
                            return $query->where('property_id', $property_id);
                        })
                        ->whereBetween('checkin_date', [$percentage_from_date, $percentage_to_date]);
                })
                ->value('percentage');
            
            if ($occupancyPreviousPercentage != 0) {
                $percentageChange = (($occupancyPercentage - $occupancyPreviousPercentage) / $occupancyPreviousPercentage) * 100;
                $percentageChange = (int) round($percentageChange, 0);
            } else {
                $percentageChange = $occupancyPercentage > 0 ? 100 : 0;
            }           
               
                
            return response()->json([
                'status' => true,
                'data' => [
                    0 => [
                            'data' => $netRevenueValue,
                            'value' => $netRevenue,
                            'type' => 'price',
                            'percentage' => $netRevenuePercentageChange,
                            'title' => 'Net Revenue',
                            'url' => 'net-revenue'
                        ],
                    1 =>[
                            'data' => $averagePricesValue,
                            'value' => $averagePricePerNight,
                            'type' => 'price',
                            'percentage' => $averagePercentagePerNightChange,
                            'title' => 'Average Price Per Night',
                            'url' => 'average-price-per-night'
                        ],
                    2 =>[
                           
                            'data' => $createdBookingsValue,
                            'value' => $createdBookings,
                            'type' => 'number',
                            'percentage' => $createdBookingsPercentageChange,
                            'title' => 'Bookings Created',
                            'url' => 'bookings-created'
                        ],
                    3 =>[
                           
                            'data' => $nightFilledValue,
                            'value' => $nightFilled,
                            'type' => 'number',
                            'percentage' =>   $nightFilledPercentageChange ,
                            'title' => 'Nights Filled',
                            'url' => ''
                        ],
                    4 =>[
                           
                            'data' => $results,
                            'value' => $occupancyPercentage,
                            'type' => 'percentage',
                            'percentage' => $percentageChange,
                            'title' => 'Occupancy Rates',
                            'url' => ''
                        ]
                ],
                'message' => 'Successfully Retrieved'
            ], 200);
            
         }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    
    public function dashboardWeeklyReport(Request $request){
        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))");
        try{
            $location_id = (int) $request->location_id;
            $ru_property_id = (int) $request->ru_property_id;
            $type = $request->type;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            
            if($ru_property_id){
                $tblHome = TblHome::select('id as property_id')->where('ru_property_id',$ru_property_id)->where('status',1)->first();
                $property_id = (int) $tblHome->property_id ?? 0;
            }else{
                $property_id = 0;
            }
            
            $days = $request->days;
            
            if($days){
                if($days == '7_next'){
                    $number = preg_replace('/_next$/', '', $days);
                    $next_7days = $number;
                    
                    $from_date = date('Y-m-d');
                    
                    $to_date = date('Y-m-d', strtotime("+{$next_7days} days"));
                    
                } else {
                    $from_date = date('Y-m-d', strtotime("-{$days} days"));
                    $to_date = date('Y-m-d', strtotime('-1 day'));
                }
            } elseif($from_date && $to_date) {
                $from_date = date('Y-m-d', strtotime($from_date));
                $to_date = date('Y-m-d', strtotime($to_date));
            } else {
                $from_date = null;
                $to_date = null;
            }
            
            /* Weekly Revenue Analysis*/
            if($type == 'gross'){
                $weeklyRevenue = PropertyBooking::where('payment_status', 'Paid')
                    ->where('property_booking_status', 'Confirmed')
                    ->when($location_id, function ($query, $location_id) {
                        return $query->where('location_id', $location_id);
                    })
                    ->when($property_id, function ($query, $property_id) {
                        return $query->where('property_id', $property_id);
                    })
                    ->whereBetween('checkin_date', [$from_date, $to_date])
                    ->select(
                        DB::raw("WEEK(checkin_date) as week_number"),
                        DB::raw("YEAR(checkin_date) as year"),
                        DB::raw("SUM(payable_amount) as total_revenue"),
                        DB::raw("DATE_FORMAT(checkin_date, '%Y-%m-%d') as price_date")
                    )
                    ->groupBy('year', 'week_number')
                    ->orderBy('year', 'asc')
                    ->orderBy('week_number', 'asc')
                    ->get()
                    ->toArray();
                
                // dd($weeklyRevenue);
                    
            }else{
                $weeklyRevenue = PropertyBooking::where('payment_status', 'Paid')
                    ->where('property_booking_status', 'Confirmed')
                    ->when($location_id, function ($query, $location_id) {
                        return $query->where('location_id', $location_id);
                    })
                    ->when($property_id, function ($query, $property_id) {
                        return $query->where('property_id', $property_id);
                    })
                    ->whereBetween('checkin_date', [$from_date, $to_date])
                    ->select(
                        DB::raw("WEEK(checkin_date) as week_number"),
                        DB::raw("YEAR(checkin_date) as year"),
                        DB::raw("SUM(total_amount) as total_revenue"),
                        DB::raw("DATE_FORMAT(checkin_date, '%Y-%m-%d') as price_date")
                    )
                    ->groupBy('year', 'week_number')
                    ->orderBy('year', 'asc')
                    ->orderBy('week_number', 'asc')
                    ->get()
                    ->toArray();
            }
            
          
            
            return response()->json([
                'status' => true,
                'data' => $weeklyRevenue,
                'message' => 'Successfully Retrive'
            ], 200);
            
        }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }    
    
    public function dashboardChannelRevenue(Request $request){
        try{
            $location_id = (int) $request->location_id;
            $ru_property_id = (int) $request->ru_property_id;
            $type = $request->type;
            
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            
            if($ru_property_id){
                $tblHome = TblHome::select('id as property_id')->where('ru_property_id',$ru_property_id)->where('status',1)->first();
                $property_id = (int) $tblHome->property_id ?? 0;
            }else{
                $property_id = 0;
            }
            
            $days = $request->days;
            
           if($days){
                if($days == '7_next'){
                    $number = preg_replace('/_next$/', '', $days);
                    $next_7days = $number;
                    
                    $from_date = date('Y-m-d');
                    
                    $to_date = date('Y-m-d', strtotime("+{$next_7days} days"));
                    
                } else {
                    $from_date = date('Y-m-d', strtotime("-{$days} days"));
                    $to_date = date('Y-m-d', strtotime('-1 day'));
                }
            } elseif($from_date && $to_date) {
                $from_date = date('Y-m-d', strtotime($from_date));
                $to_date = date('Y-m-d', strtotime($to_date));
            } else {
                $from_date = null;
                $to_date = null;
            }
            
            /* Channel Distribution / Revenue*/
            if($type == 'gross'){
                 $channelDistributionRevenue = DB::table('property_bookings')
                    ->where('property_booking_status', 'Confirmed')
                    ->select('channel', DB::raw('COUNT(*) as total_count'), DB::raw('ROUND(SUM(payable_amount), 0) as total_amount'))
                    ->when($location_id, function ($query, $location_id) {
                        return $query->where('location_id', $location_id);
                    })
                    ->when($property_id, function ($query, $property_id) {
                        return $query->where('property_id', $property_id);
                    })
                    ->whereBetween('checkin_date', [$from_date, $to_date])
                    ->where('payment_status', 'Paid')
                    ->groupBy('channel')
                    ->get();
                    
                    
                $totalChannelRevenue = DB::table('property_bookings')
                    ->when($location_id, function ($query, $location_id) {
                        return $query->where('location_id', $location_id);
                    })
                    ->when($property_id, function ($query, $property_id) {
                        return $query->where('property_id', $property_id);
                    })
                    ->whereBetween('checkin_date', [$from_date, $to_date])
                    ->where('payment_status', 'Paid')
                    ->where('property_booking_status', 'Confirmed')
                    ->groupBy('channel')
                    ->select(DB::raw('SUM(payable_amount) as total_revenue'))
                    ->get();
                
                $totalChannelRevenueSum = (int) round($totalChannelRevenue->sum('total_revenue'));
                
            }else{
                
                $channelDistributionRevenue = DB::table('property_bookings')
                    ->select('channel', DB::raw('COUNT(*) as total_count'), DB::raw('ROUND(SUM(total_amount), 0) as total_amount'))
                    ->when($location_id, function ($query, $location_id) {
                        return $query->where('location_id', $location_id);
                    })
                    ->when($property_id, function ($query, $property_id) {
                        return $query->where('property_id', $property_id);
                    })
                    ->whereBetween('checkin_date', [$from_date, $to_date])
                    ->where('payment_status', 'Paid')
                    ->where('property_booking_status', 'Confirmed')
                    ->groupBy('channel')
                    ->get();
                    
                $totalChannelRevenue = DB::table('property_bookings')
                    ->when($location_id, function ($query, $location_id) {
                        return $query->where('location_id', $location_id);
                    })
                    ->when($property_id, function ($query, $property_id) {
                        return $query->where('property_id', $property_id);
                    })
                    ->whereBetween('checkin_date', [$from_date, $to_date])
                    ->where('payment_status', 'Paid')
                    ->where('property_booking_status', 'Confirmed')
                    ->groupBy('channel')
                    ->select(DB::raw('SUM(total_amount) as total_revenue'))
                    ->get();
                
                $totalChannelRevenueSum = (int) round($totalChannelRevenue->sum('total_revenue'));
            }
            
            return response()->json([
                'status' => true,
                'data' => [
                    'channel_distribution_revenue' => $channelDistributionRevenue,
                    'total_channel_revenue' => $totalChannelRevenueSum,
                ],
                'message' => 'Successfully Retrive'
            ], 200);
            
        }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    } 
    
    public function dashboardAnalytics(Request $request){
        try{
            $location_id = (int) $request->location_id;
            $ru_property_id = (int) $request->ru_property_id;
            $type = $request->type;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            
            if($ru_property_id){
                $tblHome = TblHome::select('id as property_id')->where('ru_property_id',$ru_property_id)->where('status',1)->first();
                $property_id = (int) $tblHome->property_id ?? 0;
            }else{
                $property_id = 0;
            }
            
            $days = $request->days;
            
            if($days){
                if($days == '7_next'){
                    $number = preg_replace('/_next$/', '', $days);
                    $next_7days = $number;
                    
                    $from_date = date('Y-m-d');
                    
                    $to_date = date('Y-m-d', strtotime("+{$next_7days} days"));
                    
                } else {
                    $from_date = date('Y-m-d', strtotime("-{$days} days"));
                    $to_date = date('Y-m-d', strtotime('-1 day'));
                }
            } elseif($from_date && $to_date) {
                $from_date = date('Y-m-d', strtotime($from_date));
                $to_date = date('Y-m-d', strtotime($to_date));
            } else {
                $from_date = null;
                $to_date = null;
            }

       
            /*Average Length of Stay*/
            $averageLengthOfStay = PropertyBooking::where('payment_status', 'Paid')
            ->where('property_booking_status', 'Confirmed')
                ->when($location_id, function ($query, $location_id) {
                    return $query->where('location_id', $location_id);
                })
                ->when($property_id, function ($query, $property_id) {
                    return $query->where('property_id', $property_id);
                })
                ->whereBetween('checkin_date', [$from_date, $to_date])
                ->avg('no_of_nights');
                //   dd($averageLengthOfStay);
            $averageLengthOfStay = (int) round($averageLengthOfStay) ?? 0; //in Nights
            $nightName = ($averageLengthOfStay == 1) ? 'Night' : 'Nights';
           
            /*Average Lead Time*/
            $averageLeadTime = DB::table('property_bookings')
                ->where('payment_status', 'Paid')
                ->where('property_booking_status', 'Confirmed')
                ->when($location_id, function ($query, $location_id) {
                    return $query->where('location_id', $location_id);
                })
                ->when($property_id, function ($query, $property_id) {
                    return $query->where('property_id', $property_id);
                })
                ->whereBetween('checkin_date', [$from_date, $to_date])
                ->select(DB::raw('AVG(DATEDIFF(checkin_date, created_at)) AS average_days_difference'))
                ->value('average_days_difference');
                
            $averageLeadTime = (int) round($averageLeadTime) ?? 0;   // In days
            $daystName = ($averageLeadTime == 1) ? 'Day' : 'Days';
            
            return response()->json([
                'status' => true,
                'data' =>[
                     0 => [
                        'title'=> 'Average Length of Stay',
                        'name'=> $nightName,
                        'value' => $averageLengthOfStay,
                    ],
                    1 => [
                        'title'=> 'Average Lead Time',
                        'name'=> $daystName,
                        'value' => $averageLeadTime 
                    ]    
                ],
                'message' => 'Successfully Retrive'
            ], 200);
            
            
        }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function netRevenueDetails(Request $request){
        try{
            if($request->type == 'gross'){
                $location_id = $request->location_id;
                $property_id = $request->property_id;
                $channel = $request->channel;
                $days = $request->days;
                $from_date = $request->from_date;
                $to_date = $request->to_date;
                
                if ($days) {
                    if($days == '7_next'){
                        $number = preg_replace('/_next$/', '', $days);
                        $next_7days = $number;
                        
                        $from_date = date('Y-m-d');
                        $to_date = date('Y-m-d', strtotime("+{$next_7days} days"));
                        
                    }else{
                        $from_date = date('Y-m-d', strtotime("-{$days} days"));
                        $to_date = date('Y-m-d', strtotime('-1 day'));
                    }
                   
                } elseif ($from_date && $to_date) {
                    $from_date = date('Y-m-d', strtotime($from_date));
                    $to_date = date('Y-m-d', strtotime($to_date));
                } else {
                    $from_date = null;
                    $to_date = null;
                }
                
                $netRevenueDetails = PropertyBooking::select(
                    'tbl_homes.home_name as property_name',
                    'tbl_location.location_name as location',
                    'property_bookings.channel',
                    'property_bookings.checkin_date',
                    DB::raw('ROUND(property_bookings.payable_amount, 2) as revenue')
                )
                ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')
                ->leftJoin('tbl_location', 'tbl_location.id', '=', 'property_bookings.location_id')
                ->when($location_id, function ($query, $location_id) {
                    return $query->where('property_bookings.location_id', $location_id);
                })
                ->when($property_id, function ($query, $property_id) {
                    return $query->where('property_bookings.property_id', $property_id);
                })
                ->when($channel, function ($query, $channel) {
                    return $query->where('property_bookings.channel', $channel);
                })
                ->when($from_date && $to_date, function ($query) use ($from_date, $to_date) {
                    return $query->whereBetween('property_bookings.checkin_date', [$from_date, $to_date]);
                })
                ->where('property_bookings.payment_status', 'Paid')
                ->where('property_bookings.property_booking_status', 'Confirmed')
                ->orderby('property_bookings.checkin_date','asc')
                ->paginate(50);
                
                $netRevenueTotal = PropertyBooking::select(
                    'tbl_homes.home_name as property_name',
                    'tbl_location.location_name as location',
                    'property_bookings.channel',
                    'property_bookings.checkin_date',
                    DB::raw('ROUND(property_bookings.payable_amount, 2) as revenue')
                )
                ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')
                ->leftJoin('tbl_location', 'tbl_location.id', '=', 'property_bookings.location_id')
                ->when($location_id, function ($query, $location_id) {
                    return $query->where('property_bookings.location_id', $location_id);
                })
                ->when($property_id, function ($query, $property_id) {
                    return $query->where('property_bookings.property_id', $property_id);
                })
                ->when($channel, function ($query, $channel) {
                    return $query->where('property_bookings.channel', $channel);
                })
                ->when($from_date && $to_date, function ($query) use ($from_date, $to_date) {
                    return $query->whereBetween('property_bookings.checkin_date', [$from_date, $to_date]);
                })
                ->where('property_bookings.payment_status', 'Paid')
                ->where('property_bookings.property_booking_status', 'Confirmed')
                ->get();
                
                
                $groupedData = [];
                
                foreach ($netRevenueDetails as $item) {
                    $key = $item['property_name'] . '|' . $item['location'] . '|' . $item['channel'] . '|' . $item['checkin_date'];
                    
                    if (!isset($groupedData[$key])) {
                        $groupedData[$key] = $item;
                    } else {
                        $groupedData[$key]['revenue'] += $item['revenue'];
                    }
                }
                
                $groupedDataTotal = [];
                $grandTotalRevenue = 0;
                
                foreach ($netRevenueTotal as $item) {
                    $key = $item['property_name'] . '|' . $item['location'] . '|' . $item['channel'] . '|' . $item['checkin_date'];
                    
                    if (!isset($groupedDataTotal[$key])) {
                        $groupedDataTotal[$key] = $item;
                    } else {
                        $groupedDataTotal[$key]['revenue'] += $item['revenue'];
                    }
                }
                foreach ($groupedDataTotal as $data) {
                    $grandTotalRevenue += $data['revenue'];
                }
                $grandTotalRevenue = round($grandTotalRevenue, 2);
                
                
                // dd($grandTotalRevenue);
                // dd($result);
                $result = array_values($groupedData);
            }else{
                $location_id = $request->location_id;
                $property_id = $request->property_id;
                $channel = $request->channel;
                $days = $request->days;
                $from_date = $request->from_date;
                $to_date = $request->to_date;
                
                if ($days) {
                    if($days == '7_next'){
                        $number = preg_replace('/_next$/', '', $days);
                        $next_7days = $number;
                        
                        $from_date = date('Y-m-d');
                        $to_date = date('Y-m-d', strtotime("+{$next_7days} days"));
                        
                    }else{
                        $from_date = date('Y-m-d', strtotime("-{$days} days"));
                        $to_date = date('Y-m-d', strtotime('-1 day'));
                    }
                } elseif ($from_date && $to_date) {
                    $from_date = date('Y-m-d', strtotime($from_date));
                    $to_date = date('Y-m-d', strtotime($to_date));
                } else {
                    $from_date = null;
                    $to_date = null;
                }
                
                $netRevenueDetails = PropertyBooking::select(
                    'tbl_homes.home_name as property_name',
                    'tbl_location.location_name as location',
                    'property_bookings.channel',
                    'property_bookings.checkin_date',
                    DB::raw('ROUND(property_bookings.total_amount, 2) as revenue')
                )
                ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')
                ->leftJoin('tbl_location', 'tbl_location.id', '=', 'property_bookings.location_id')
                ->when($location_id, function ($query, $location_id) {
                    return $query->where('property_bookings.location_id', $location_id);
                })
                ->when($property_id, function ($query, $property_id) {
                    return $query->where('property_bookings.property_id', $property_id);
                })
                ->when($channel, function ($query, $channel) {
                    return $query->where('property_bookings.channel', $channel);
                })
                ->when($from_date && $to_date, function ($query) use ($from_date, $to_date) {
                    return $query->whereBetween('property_bookings.checkin_date', [$from_date, $to_date]);
                })
                ->where('property_bookings.payment_status', 'Paid')
                ->where('property_bookings.property_booking_status', 'Confirmed')
                ->orderby('property_bookings.checkin_date','asc')
                ->paginate(50);
                
                $netRevenueTotal = PropertyBooking::select(
                    'tbl_homes.home_name as property_name',
                    'tbl_location.location_name as location',
                    'property_bookings.channel',
                    'property_bookings.checkin_date',
                    DB::raw('ROUND(property_bookings.total_amount, 2) as revenue')
                )
                ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')
                ->leftJoin('tbl_location', 'tbl_location.id', '=', 'property_bookings.location_id')
                ->when($location_id, function ($query, $location_id) {
                    return $query->where('property_bookings.location_id', $location_id);
                })
                ->when($property_id, function ($query, $property_id) {
                    return $query->where('property_bookings.property_id', $property_id);
                })
                ->when($channel, function ($query, $channel) {
                    return $query->where('property_bookings.channel', $channel);
                })
                ->when($from_date && $to_date, function ($query) use ($from_date, $to_date) {
                    return $query->whereBetween('property_bookings.checkin_date', [$from_date, $to_date]);
                })
                ->where('property_bookings.payment_status', 'Paid')
                ->where('property_bookings.property_booking_status', 'Confirmed')
                ->get();
                
                
                $groupedData = [];
                
                foreach ($netRevenueDetails as $item) {
                    $key = $item['property_name'] . '|' . $item['location'] . '|' . $item['channel'] . '|' . $item['checkin_date'];
                    
                    if (!isset($groupedData[$key])) {
                        $groupedData[$key] = $item;
                    } else {
                        $groupedData[$key]['revenue'] += $item['revenue'];
                    }
                }
                
                $groupedDataTotal = [];
                $grandTotalRevenue = 0;
                
                foreach ($netRevenueTotal as $item) {
                    $key = $item['property_name'] . '|' . $item['location'] . '|' . $item['channel'] . '|' . $item['checkin_date'];
                    
                    if (!isset($groupedDataTotal[$key])) {
                        $groupedDataTotal[$key] = $item;
                    } else {
                        $groupedDataTotal[$key]['revenue'] += $item['revenue'];
                    }
                }
                foreach ($groupedDataTotal as $data) {
                    $grandTotalRevenue += $data['revenue'];
                }
                $grandTotalRevenue = round($grandTotalRevenue, 2);
                
                
                // dd($grandTotalRevenue);
                // dd($result);
                $result = array_values($groupedData);
            }
            
           
            return response()->json([
                'status' => true,
                'data' => $result,
                'net_revenue' => $grandTotalRevenue,
                'pagination' => [
                    'total' => $netRevenueDetails->total(),         
                    'per_page' => $netRevenueDetails->perPage(),    
                    'current_page' => $netRevenueDetails->currentPage(),
                    'last_page' => $netRevenueDetails->lastPage(),
                ],
                'message' => 'Successfully Retrive'
            ], 200);
            
        }catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => "Internal Error",
                    'error' => $e->getMessage(),
                ], 500);
            }
    }
    
    public function averagePricePerNightDetails(Request $request){
        try {
            $property_id = $request->property_id;
            $days = $request->days;
        
            if ($days) {
                if($days == '7_next'){
                    $number = preg_replace('/_next$/', '', $days);
                    $next_7days = $number;
                    
                    $from_date = date('Y-m-d');
                    $to_date = date('Y-m-d', strtotime("+{$next_7days} days"));
                    
                }else{
                    $from_date = date('Y-m-d', strtotime("-{$days} days"));
                    $to_date = date('Y-m-d', strtotime('-1 day'));
                }
            } else {
                $from_date = null;
                $to_date = null;
            }
        
            $averagePricePerNightDetails = PropertyBooking::select(
                'tbl_homes.home_name as property_name',
                DB::raw('COUNT(property_bookings.property_id) as count'),
                DB::raw('SUM(property_bookings.per_night_price) as total_price'),
                DB::raw('ROUND(AVG(property_bookings.per_night_price), 2) as average_price_per_night')
            )
            ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')
            ->when($property_id, function ($query, $property_id) {
                return $query->where('property_bookings.property_id', $property_id);
            })
            ->when($from_date && $to_date, function ($query) use ($from_date, $to_date) {
                return $query->whereBetween('property_bookings.checkin_date', [$from_date, $to_date]);
            })
            ->where('property_bookings.payment_status', 'Paid')
            ->where('property_bookings.property_booking_status', 'Confirmed')
            ->groupBy('tbl_homes.home_name')
            ->paginate(50);
            
            $averagePricePerNightTotalDetails = PropertyBooking::select(
                'tbl_homes.home_name as property_name',
                DB::raw('COUNT(property_bookings.property_id) as count'),
                DB::raw('SUM(property_bookings.per_night_price) as total_price'),
                DB::raw('ROUND(AVG(property_bookings.per_night_price), 2) as average_price_per_night')
            )
            ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')
            ->when($property_id, function ($query, $property_id) {
                return $query->where('property_bookings.property_id', $property_id);
            })
            ->when($from_date && $to_date, function ($query) use ($from_date, $to_date) {
                return $query->whereBetween('property_bookings.checkin_date', [$from_date, $to_date]);
            })
            ->where('property_bookings.payment_status', 'Paid')
            ->where('property_bookings.property_booking_status', 'Confirmed')
            ->groupBy('tbl_homes.home_name')
            ->get();
        
            $totalAveragePrice = 0;
            $totalCountAveragePrice = 0;
            foreach ($averagePricePerNightTotalDetails as $item) {
                $totalAveragePrice += $item['average_price_per_night'];
                $totalCountAveragePrice += $item['count'];
            }
            $totalCountAveragePrice = round($totalCountAveragePrice, 2);
            $totalAveragePrice = round($totalAveragePrice, 2);
            // dump($totalAveragePrice);
            // dd($totalCountAveragePrice);
            
        
            // Custom Pagination Structure
            $pagination = [
                'total' => $averagePricePerNightDetails->total(),
                'per_page' => $averagePricePerNightDetails->perPage(),
                'current_page' => $averagePricePerNightDetails->currentPage(),
                'last_page' => $averagePricePerNightDetails->lastPage(),
            ];
        
            return response()->json([
                'status' => true,
                'data' => $averagePricePerNightDetails->items(), // Use items() to get the paginated data
                'pagination' => $pagination,
                'total_average_price' => $totalAveragePrice,
                'message' => 'Successfully Retrieved'
            ], 200);
        
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }

    }
    
    public function bookingsCreatedDetails(Request $request){
        try {
            $location_id = $request->location_id;
            $property_id = $request->property_id;
            $channel = $request->channel;
            $days = $request->days;
        
            if ($days) {
                if($days == '7_next'){
                    $number = preg_replace('/_next$/', '', $days);
                    $next_7days = $number;
                    
                    $from_date = date('Y-m-d');
                    $to_date = date('Y-m-d', strtotime("+{$next_7days} days"));
                    
                }else{
                    $from_date = date('Y-m-d', strtotime("-{$days} days"));
                    $to_date = date('Y-m-d', strtotime('-1 day'));
                }
            } else {
                $from_date = null;
                $to_date = null;
            }
        
            $createdBookingsdetails = PropertyBooking::select(
                    'tbl_homes.home_name as property_name',
                    'property_bookings.channel',
                    'tbl_location.location_name',
                    DB::raw('COUNT(property_bookings.property_id) as count'),
                    DB::raw('ROUND(SUM(property_bookings.payable_amount), 2) as total_amount')
                )
                ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')
                ->leftJoin('tbl_location', 'tbl_location.id', '=', 'property_bookings.location_id')
                ->when($location_id, function ($query, $location_id) {
                    return $query->where('property_bookings.location_id', $location_id);
                })
                ->when($property_id, function ($query, $property_id) {
                    return $query->where('property_bookings.property_id', $property_id);
                })
                ->when($channel, function ($query, $channel) {
                    return $query->where('property_bookings.channel', $channel);
                })
                ->when($from_date && $to_date, function ($query) use ($from_date, $to_date) {
                    return $query->whereBetween('property_bookings.checkin_date', [$from_date, $to_date]);
                })
                ->where('property_bookings.payment_status', 'Paid')
                ->where('property_bookings.property_booking_status', 'Confirmed')
                ->groupBy('tbl_homes.home_name', 'property_bookings.channel', 'property_bookings.location_id')
                ->paginate(50);
                
            $createdBookingsTotaldetails = PropertyBooking::select(
                    'tbl_homes.home_name as property_name',
                    'property_bookings.channel',
                    'tbl_location.location_name',
                    DB::raw('COUNT(property_bookings.property_id) as count'),
                    DB::raw('ROUND(SUM(property_bookings.payable_amount), 2) as total_amount')
                )
                ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')
                ->leftJoin('tbl_location', 'tbl_location.id', '=', 'property_bookings.location_id')
                ->when($location_id, function ($query, $location_id) {
                    return $query->where('property_bookings.location_id', $location_id);
                })
                ->when($property_id, function ($query, $property_id) {
                    return $query->where('property_bookings.property_id', $property_id);
                })
                ->when($channel, function ($query, $channel) {
                    return $query->where('property_bookings.channel', $channel);
                })
                ->when($from_date && $to_date, function ($query) use ($from_date, $to_date) {
                    return $query->whereBetween('property_bookings.checkin_date', [$from_date, $to_date]);
                })
                ->where('property_bookings.payment_status', 'Paid')
                ->where('property_bookings.property_booking_status', 'Confirmed')
                ->groupBy('tbl_homes.home_name', 'property_bookings.channel', 'property_bookings.location_id')
                ->get();
        
            $grandTotal = 0;
        
            foreach ($createdBookingsTotaldetails as $data) {
                $grandTotal += $data['total_amount'];
            }
        
            $grandTotal = round($grandTotal, 2);
        
            // Custom Pagination Structure
            $pagination = [
                'total' => $createdBookingsdetails->total(),
                'per_page' => $createdBookingsdetails->perPage(),
                'current_page' => $createdBookingsdetails->currentPage(),
                'last_page' => $createdBookingsdetails->lastPage()
            ];
        
            return response()->json([
                'status' => true,
                'data' => $createdBookingsdetails->items(),
                'pagination' => $pagination,
                'grand_total' => $grandTotal,
                'message' => 'Successfully Retrieved'
            ], 200);
        
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }

    }
    
    public function hyperGuestResponse(Request $request){
        // $fileName = 'hyper_guest_booking_' . time() . '.txt';
        // Storage::disk('local')->put('hyperguest/' . $fileName, $data);
        // return $data;
        
        $data = file_get_contents('php://input');
    
        libxml_use_internal_errors(true);
        $xmlObject = simplexml_load_string($data);
    
        if ($xmlObject === false) {
            return response()->json(['error' => 'Invalid XML format'], 400);
        }
    
        $xmlObject->registerXPathNamespace('soap', 'http://www.w3.org/2003/05/soap-envelope');
        $xmlObject->registerXPathNamespace('ota', 'http://www.opentravel.org/OTA/2003/05');
    
        $body = $xmlObject->xpath('//soap:Body');
    
        if (empty($body)) {
            return response()->json(['error' => 'SOAP Body not found'], 400);
        }
    
        $bodyContent = $body[0]->asXML();
        $parsedXml = simplexml_load_string($bodyContent, 'SimpleXMLElement', LIBXML_NOCDATA);
        
        $resultArray = json_decode(json_encode($parsedXml), true);
    
        return response()->json($resultArray);
    }
    
    /*public function hyperGuestResponseNew(Request $request){
       
        if($request->missing('webhook_username')){
            return response()->json([
                'status' => false,
                'message' => "Invalid Credentials!",
                
            ], 403);
        }
        
        if($request->missing('webhook_password')){
            return response()->json([
                'status' => false,
                'message' => "Invalid Credentials!",
                
            ], 403);
        }
        
        if($request->webhook_username !=env('HYPER_GUEST_WEBHOOK_USERNAME')){
            return response()->json([
                'status' => false,
                'message' => "Invalid Credentials!",
                
            ], 403);
        }
        
        if($request->webhook_password != env('HYPER_GUEST_WEBHOOK_PASSWORD')){
            return response()->json([
                'status' => false,
                'message' => "Invalid Credentials!",
                
            ], 403);
        }
        
        $data = file_get_contents('php://input');
    
        libxml_use_internal_errors(true);
        $xmlObject = simplexml_load_string($data);
    
        if ($xmlObject === false) {
            return response()->json(['error' => 'Invalid XML format'], 400);
        }
    
        $xmlObject->registerXPathNamespace('soap', 'http://www.w3.org/2003/05/soap-envelope');
        $xmlObject->registerXPathNamespace('ota', 'http://www.opentravel.org/OTA/2003/05');
    
        $body = $xmlObject->xpath('//soap:Body');
    
        if (empty($body)) {
            return response()->json(['error' => 'SOAP Body not found'], 400);
        }
    
        $bodyContent = $body[0]->asXML();
        $parsedXml = simplexml_load_string($bodyContent, 'SimpleXMLElement', LIBXML_NOCDATA);
        
        $resultArray = json_decode(json_encode($parsedXml), true);
        if($resultArray){
            $echoToken = $resultArray['OTA_HotelResNotifRQ']['@attributes']['EchoToken'];
            $TimeStamp = $resultArray['OTA_HotelResNotifRQ']['@attributes']['TimeStamp'];
            $CreateDateTime = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation']['@attributes']['CreateDateTime'];
            $dateMonth = date('Y/m', strtotime($CreateDateTime));
            $Type = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation']['UniqueID']['@attributes']['Type'];
            $ID = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation']['UniqueID']['@attributes']['ID'];
            $rand = rand(999999999,6);
        }
        
        return $response = "<OTA_HotelResNotifRS xmlns='http://www.opentravel.org/OTA/{$dateMonth}' EchoToken='".$echoToken."'
                        TimeStamp='".$TimeStamp."'>
                        <HotelReservations>
                            <HotelReservation CreateDateTime='".date('Y-m-d H:i:s')."' ResStatus='Commit'>
                                <UniqueID Type='".$Type."' ID='".$ID."' />
                                <ResGlobalInfo>
                                    <HotelReservationIDs>
                                        <HotelReservationID ResID_Value='{$rand}' ResID_Type='14' />
                                    </HotelReservationIDs>
                                </ResGlobalInfo>
                            </HotelReservation>
                        </HotelReservations>
                        <Success />
                    </OTA_HotelResNotifRS>";

    }*/
    
    // hyperguestbooking
    
    public function hyperGuestResponseNew(Request $request){
        $data = file_get_contents('php://input');
        Storage::disk('local')->put('hyperguest/hyperguest_'.time().'.txt', $data);
        
        if($request->missing('webhook_username')){
            return response()->json([
                'status' => false,
                'message' => "Invalid Credentials!",
                
            ], 403);
        }
        
        if($request->missing('webhook_password')){
            return response()->json([
                'status' => false,
                'message' => "Invalid Credentials!",
                
            ], 403);
        }

        
        if($request->webhook_username !=env('HYPER_GUEST_WEBHOOK_USERNAME')){
            return response()->json([
                'status' => false,
                'message' => "Invalid Credentials!",
                
            ], 403);
        }
        
        if($request->webhook_password != env('HYPER_GUEST_WEBHOOK_PASSWORD')){
            return response()->json([
                'status' => false,
                'message' => "Invalid Credentials!",
                
            ], 403);
        }
        
        $data = file_get_contents('php://input');
    
        libxml_use_internal_errors(true);
        $xmlObject = simplexml_load_string($data);
    
        if ($xmlObject === false) {
            return response()->json(['error' => 'Invalid XML format'], 400);
        }
    
        $xmlObject->registerXPathNamespace('soap', 'http://www.w3.org/2003/05/soap-envelope');
        $xmlObject->registerXPathNamespace('ota', 'http://www.opentravel.org/OTA/2003/05');
    
        $body = $xmlObject->xpath('//soap:Body');
    
        if (empty($body)) {
            return response()->json(['error' => 'SOAP Body not found'], 400);
        }
    
        $bodyContent = $body[0]->asXML();
        $parsedXml = simplexml_load_string($bodyContent, 'SimpleXMLElement', LIBXML_NOCDATA);
        
        $resultArray = json_decode(json_encode($parsedXml), true);
        // dd($resultArray);
        if($resultArray){
            $echoToken = $resultArray['OTA_HotelResNotifRQ']['@attributes']['EchoToken'];
            $ResStatus = $resultArray['OTA_HotelResNotifRQ']['@attributes']['ResStatus'];
            $TimeStamp = $resultArray['OTA_HotelResNotifRQ']['@attributes']['TimeStamp'];
            $CreateDateTime = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation']['@attributes']['CreateDateTime'];
            $dateMonth = date('Y/m', strtotime($CreateDateTime));
            $Type = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation']['UniqueID']['@attributes']['Type'];
            $ID = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation']['UniqueID']['@attributes']['ID'];
            $rand = rand(999999999,6);
                
            $hyperguestid = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation']['RoomStays']['RoomStay']['BasicPropertyInfo']['@attributes']['HotelCode'];
            $total_amount = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation']['RoomStays']['RoomStay']['Total']['@attributes']['AmountBeforeTax'];
            $payable_amount = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation']['RoomStays']['RoomStay']['Total']['@attributes']['AmountAfterTax'];
            // $no_of_adult = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation']['RoomStays']['RoomStay']['GuestCounts']['GuestCount']['@attributes']['Count'];
            $guestCounts = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation']['RoomStays']['RoomStay']['GuestCounts']['GuestCount'];

            // dd($hyperguestid);
            $no_of_adult = 0;
            $no_of_child = 0;
            
            if (isset($guestCounts['@attributes'])) {
                // Single entry case, convert it into an array for consistency
                $guestCounts = [$guestCounts];
            }
            
            foreach ($guestCounts as $guest) {
                $attributes = $guest['@attributes'];
            
                if ($attributes['AgeQualifyingCode'] == "10") {
                    $no_of_adult = $attributes['Count'];
                } elseif ($attributes['AgeQualifyingCode'] == "8") {
                    $no_of_child = $attributes['Count'];
                }
            }
            // dd($no_of_adult);
            // booking save
            
            if (isset($resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation'])) {
                $reservation = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation'];
                $roomStay = $reservation['RoomStays']['RoomStay'] ?? [];
                
                // dd($roomStay['RoomRates']['RoomRate']['@attributes']['RoomTypeCode']);
                $home  = TblHome::where('hyper_guest_id', $hyperguestid)->first();
                $date_difference_count = MasterHelper::getDateDifference($roomStay['TimeSpan']['@attributes']['Start'], $roomStay['TimeSpan']['@attributes']['End']);

                $propertyBooking = [];
                if ($home) {
                    $propertyBooking['location_id'] = $home->location_id;
                    $propertyBooking['property_id'] = $home->id;
                    $propertyBooking['total_amount'] = $total_amount;
                    $propertyBooking['payable_amount'] = $payable_amount;
                    $propertyBooking['booking_id'] = $ID;
                   
                    $propertyBooking['booking_status'] = 'paid';
                   
                    $propertyBooking['booking_created_by'] = 'ru';
                    $propertyBooking['booking_from'] = 'hyperguest';
                    if($ResStatus == 'Commit'){
                    $propertyBooking['ru_booking_status'] = 'Confirmed';
                    }else{
                    $propertyBooking['ru_booking_status'] = 'Canceled';
                    }
                    $propertyBooking['type'] = 'Location';
                    $propertyBooking['channel'] = 'HyperGuest';
                    $propertyBooking['no_of_adult'] = $no_of_adult;
                    $propertyBooking['no_of_children'] = $no_of_child;
                    
                    $profile = $reservation['ResGuests']['ResGuest']['Profiles']['ProfileInfo']['Profile']['Customer'] ?? [];
                    $propertyBooking['customer_detail'] = json_encode([
                        'first_name' => $profile['PersonName']['GivenName'] ?? '',
                        'last_name' => $profile['PersonName']['Surname'] ?? '',
                        'email' => $profile['Email'] ?? '',
                        'mobile_number' => $profile['Telephone']['@attributes']['PhoneNumber'] ?? ''
                    ]);
                    
                
                    $propertyBooking['checkin_date'] = $roomStay['TimeSpan']['@attributes']['Start'] ?? null;
                    $propertyBooking['checkout_date'] = $roomStay['TimeSpan']['@attributes']['End'] ?? null;
                    
                    // $hotelCode = $roomStay['BasicPropertyInfo']['@attributes']['HotelCode'] ?? null;
                    // $home = TblHome::where('hyper_guest_id', $hotelCode)->first();
                }
        
                // GST calculation
                $price = $payable_amount;
                $propertyBooking['paid_amount'] = $price;
                $getAppliedGst = getAppliedGst($price);
                if ($getAppliedGst) {
                    $precentageAmount = ($price * $getAppliedGst->gst_percentage) / 100;
                    $gst_amount = $precentageAmount;
                    $gstPrecentage = (int)$getAppliedGst->gst_percentage;
                    $propertyBooking['tax'] = $gstPrecentage;
                    $propertyBooking['taxable_amount'] = $gst_amount;
                }
                
                if($ResStatus == 'Commit'){
                    $propertyBooking['property_booking_status'] = 'Confirmed';
                }else{
                    $propertyBooking['property_booking_status'] = 'Canceled';
                }
        
                $count = PropertyBooking::where('booking_id', $ID)->count();
                if ($count == 0) {
                    PropertyBooking::create($propertyBooking);
                } else {
                    PropertyBooking::where('booking_id', $propertyBooking['booking_id'])->update($propertyBooking);
                }
        
                if ($home) {
                    RuPropertyAvailability::where('ru_property_id', $home->ru_property_id)
                        ->whereBetween('availability_date', [
                            $roomStay['TimeSpan']['@attributes']['Start'],
                            $roomStay['TimeSpan']['@attributes']['End']
                        ])->update(['is_available' => 'no']);
                }
                
                // blockPropertyAvailabilityInRu($home->ru_property_id, $roomStay['TimeSpan']['@attributes']['Start'] , $roomStay['TimeSpan']['@attributes']['End']);
            
                
            } else {
                dd('No Hotel Reservations found in the response.');
            }
            
        }
        
        // $response = $this->hyperguest->availibityPush(
        //     $hyperguestid,
        //     $roomStay['TimeSpan']['@attributes']['Start'] ?? null,
        //     $roomStay['TimeSpan']['@attributes']['End'] ?? null,
        //     $roomStay['RoomRates']['RoomRate']['@attributes']['RoomTypeCode'],
        //     $roomStay['RoomRates']['RoomRate']['@attributes']['RatePlanCode']
        // );
        
        // return response()->json([
        //     'success' => "<OTA_HotelResNotifRS xmlns='http://www.opentravel.org/OTA/{$dateMonth}' EchoToken='".$echoToken."'
        //                     TimeStamp='".$TimeStamp."'>
        //                     <HotelReservations>
        //                         <HotelReservation CreateDateTime='".date('Y-m-d H:i:s')."' ResStatus='Commit'>
        //                             <UniqueID Type='".$Type."' ID='".$ID."' />
        //                             <ResGlobalInfo>
        //                                 <HotelReservationIDs>
        //                                     <HotelReservationID ResID_Value='{$rand}' ResID_Type='14' />
        //                                 </HotelReservationIDs>
        //                             </ResGlobalInfo>
        //                         </HotelReservation>
        //                     </HotelReservations>
        //                     <Success />
        //                 </OTA_HotelResNotifRS>",
        //     'apiResponse' => $response
        // ]);

        
        return $response = "<OTA_HotelResNotifRS xmlns='http://www.opentravel.org/OTA/{$dateMonth}' EchoToken='".$echoToken."'
                        TimeStamp='".$TimeStamp."'>
                        <HotelReservations>
                            <HotelReservation CreateDateTime='".date('Y-m-d H:i:s')."' ResStatus='". $ResStatus ."'>
                                <UniqueID Type='".$Type."' ID='".$ID."' />
                                <ResGlobalInfo>
                                    <HotelReservationIDs>
                                        <HotelReservationID ResID_Value='{$rand}' ResID_Type='14' />
                                    </HotelReservationIDs>
                                </ResGlobalInfo>
                            </HotelReservation>
                        </HotelReservations>
                        <Success />
                    </OTA_HotelResNotifRS>";
        
        // Call availibityPush AFTER the success response
        $apiResponse = $this->availibityPush($roomStay, $hyperguestid);
    
        // Return only the success response
        return response($response)->header('Content-Type', 'application/xml');

    }
    
    public function availibityPush($roomStay, $hyperguestid)
    {
        $hotelCode = $hyperguestid;
        $start = $roomStay['TimeSpan']['@attributes']['Start'] ?? null;
        $end = $roomStay['TimeSpan']['@attributes']['End'] ?? null;
        $invTypeCode = $roomStay['RoomRates']['RoomRate']['@attributes']['RoomTypeCode'];
        $ratePlanCode = $roomStay['RoomRates']['RoomRate']['@attributes']['RatePlanCode'];  
        $bookinglimit = 0;
        $bookingstatus = 'Close';
        return $this->hyperguest->availibityPush(
            $hotelCode,
            $start,
            $end,
            $invTypeCode,
            $ratePlanCode,
            $bookinglimit,
            $bookingstatus,
        );
    }
    
}