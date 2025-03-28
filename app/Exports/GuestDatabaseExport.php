<?php

namespace App\Exports;

use App\Models\User;
use App\Models\BookingGuestId;
use App\Models\PropertyBookingPaymentRequest;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\withMapping;
use Illuminate\Support\Collection;
use DB;
class GuestDatabaseExport implements FromCollection,  WithHeadings{

    protected $request;
    public function __construct($request){
        $this->request = $request;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection(){
    //     $reqParameter = $this->request;

    //     $query = BookingGuestId::query();
    //         $query->when( isset($reqParameter['name']) && $reqParameter['name'] != '', function ($q) use ($reqParameter) {
    //             return $q->where('name', 'like', '%'.$reqParameter['name'].'%');
    //         });
    //         $query->when(isset($reqParameter['email']) && $reqParameter['email'] != '', function ($q) use ($reqParameter) {
    //             return $q->where('email', $reqParameter['email']);
    //         });
    //         $query->when(isset($reqParameter['mobile']) && $reqParameter['mobile'] != '', function ($q) use ($reqParameter) {
    //             return $q->where('mobile_no', $reqParameter['mobile']);
    //         });
    //   // return $list = $query->get(['name', 'email', 'mobile_no']);
    //   return $list = $query->groupBy('email')
    // ->selectRaw('email, MAX(name) as name, MAX(mobile_no) as mobile_no')
    // ->get();
    // }
    
    public function collection(){
        $request = $this->request;
    DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
    $query = DB::table('booking_guest_ids')
    ->whereNull('booking_guest_ids.deleted_at')
    ->select([
        'booking_guest_ids.name',
        'booking_guest_ids.email',
        'booking_guest_ids.mobile_no',
        //'property_bookings.property_id',
        'tbl_homes.home_name',
        'tbl_homes.home_type',
        'tbl_location.location_name',
    ])
    ->leftJoin('property_bookings', 'property_bookings.id', '=', 'booking_guest_ids.property_booking_id')
    ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')
    ->leftJoin('tbl_location', 'tbl_location.id', '=', 'tbl_homes.location_id');

    $query->when(!empty($request['name']), function ($q) use ($request) {
    return $q->where('booking_guest_ids.name', 'like', '%' . $request['name'] . '%');
    });
    $query->when(!empty($request['property_name']), function ($q) use ($request) {
        return $q->where('tbl_homes.home_name', 'like', '%' . $request['property_name'] . '%');
    });
    $query->when(!empty($request['email']), function ($q) use ($request) {
        return $q->where('booking_guest_ids.email', $request['email']);
    });
    
    $query->when(!empty($request['mobile']), function ($q) use ($request) {
        return $q->where('booking_guest_ids.mobile_no', $request['mobile']);
    });
    
    $list = $query->groupBy('booking_guest_ids.email')
        ->selectRaw('
            MAX(booking_guest_ids.name) as name,
            booking_guest_ids.email,
            MAX(booking_guest_ids.mobile_no) as mobile_no,
            MAX(tbl_homes.home_name) as home_name,
            MAX(tbl_homes.home_type) as home_type,
            MAX(tbl_location.location_name) as location_name
        ')
        ->get();
        return $list;
}

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array{
        return ["Name", "Email", "Mobile No.", "Property Name", "Category", "Location"];
    }
}
