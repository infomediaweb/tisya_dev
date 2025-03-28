<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HyperGuestService
{
    public function availibityPush($hotelCode, $start, $end , $invTypeCode , $ratePlanCode, $bookinglimit, $bookingstatus)
    {
        $url = 'https://hcm.hyperguest.io/api/hcm/pms/tisyastays/v1/OTA_HotelAvailNotifRQ';
        $bookinglimit = $bookinglimit ?? '1';
        $bookingstatus = $bookingstatus ?? 'Open';
        
        $xmlData = '<?xml version="1.0" encoding="UTF-8"?>
        <soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope/">
            <soap:Header>
                <wsse:Security soap:mustUnderstand="1" xmlns:wsse="http://schemas.xmlsoap.org/ws/2003/06/secext" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                    <wsse:UsernameToken>
                        <wsse:Username>tisyastays-ota</wsse:Username>
                        <wsse:Password Type="wsse:PasswordText">3g4oht2825ng0669597006295572600</wsse:Password>
                    </wsse:UsernameToken>
                </wsse:Security>
            </soap:Header>
            <soap:Body>
                <OTA_HotelAvailNotifRQ xmlns="http://www.opentravel.org/OTA/2003/05" Version="1.0" EchoToken="1234">
                    <AvailStatusMessages HotelCode="' . $hotelCode . '">
                        <AvailStatusMessage BookingLimit="' . $bookinglimit . '">
                            <StatusApplicationControl Start="' . $start . '" End="' . $end . '" InvTypeCode="' . $invTypeCode . '" RatePlanCode="' . $ratePlanCode . '" />
                            
                            <RestrictionStatus Status="' . $bookingstatus . '" />
                        </AvailStatusMessage>
                    </AvailStatusMessages>
                </OTA_HotelAvailNotifRQ>
            </soap:Body>
        </soap:Envelope>';
    
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'text/xml',
            ])->withBody($xmlData, 'text/xml')->post($url);
    
            if ($response->successful()) {
                $xmlObject = simplexml_load_string($response->body());
                $xmlObject->registerXPathNamespace('ns', 'http://www.opentravel.org/OTA/2003/05');
                $success = $xmlObject->xpath('//ns:Success');
    
                if (!empty($success)) {
                    $startDate = Carbon::parse($start);
                    $endDate = Carbon::parse($end);
                    $dates = [];
                    while ($startDate <= $endDate) {
                        DB::table('hyper_guest_availabilities')->updateOrInsert([
                            'hyper_guest_id' => $hotelCode,
                            'availability_date' => $startDate->format('Y-m-d'),
                        ], [
                            'is_available' => 'yes',
                            'status' => '1',
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]);
                        $startDate->addDay();
                    }
    
                    return [
                        'success' => true,
                        'message' => 'Availability push successfully!',
                    ];
                }
            }
    
            return [
                'success' => false,
                'message' => 'Request failed or no success element found!',
                'response' => $response->body(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
    
    public function ratePush($hotelCode, $start, $end , $invTypeCode, $ratePlanCode, $amountAftertax)
    {
        $url = 'https://hcm.hyperguest.io/api/hcm/pms/tisyastays/v1/OTA_HotelRateAmountNotifRQ';
    
        $xmlData = '<?xml version="1.0" encoding="UTF-8"?>
        <soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope/">
            <soap:Header>
                <wsse:Security soap:mustUnderstand="1" xmlns:wsse="http://schemas.xmlsoap.org/ws/2003/06/secext" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                    <wsse:UsernameToken>
                        <wsse:Username>tisyastays-ota</wsse:Username>
                        <wsse:Password Type="wsse:PasswordText">3g4oht2825ng0669597006295572600</wsse:Password>
                    </wsse:UsernameToken>
                </wsse:Security>
            </soap:Header>
            <soap:Body>
                <OTA_HotelRateAmountNotifRQ xmlns="http://www.opentravel.org/OTA/2003/05" Version="1.0" EchoToken="1234">
                    <RateAmountMessages HotelCode="' . $hotelCode . '">
                        <RateAmountMessage>
                        <StatusApplicationControl Start="' . $start . '" End="' . $end . '" InvTypeCode="' . $invTypeCode . '" RatePlanCode="' . $ratePlanCode . '" />
                            <Rates>
                                <Rate>  
                                    <BaseByGuestAmts>
                                        <BaseByGuestAmt AgeQualifyingCode="10" AmountAfterTax="'. $amountAftertax .'"/>
                                    </BaseByGuestAmts>
                                </Rate>
                            </Rates>
                        </RateAmountMessage>
                    </RateAmountMessages>
                </OTA_HotelRateAmountNotifRQ>
            </soap:Body>
        </soap:Envelope>';
    
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'text/xml',
            ])->withBody($xmlData, 'text/xml')->post($url);
    
            if ($response->successful()) {
                $xmlObject = simplexml_load_string($response->body());
                $xmlObject->registerXPathNamespace('ns', 'http://www.opentravel.org/OTA/2003/05');
                $success = $xmlObject->xpath('//ns:Success');
    
                if (!empty($success)) {
                    $startDate = Carbon::parse($start);
                    $endDate = Carbon::parse($end);
                    $dates = [];
                    while ($startDate <= $endDate) {
                        DB::table('hyper_guest_price')->updateOrInsert([
                            'hyper_guest_id' => $hotelCode,
                            'price_date' => $startDate->format('Y-m-d'),
                        ], [
                            'price' => $amountAftertax,
                            'status' => '1',
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]);
                        $startDate->addDay();
                        // $dates[] = [
                        //     'hyper_guest_id' => $hotelCode,
                        //     'price' => $amountAftertax,
                        //     'price_date' => $startDate->format('Y-m-d'),
                        //     'status' => '1',
                        //     'created_at' => now(),
                        //     'updated_at' => now(),
                        // ];
                        $startDate->addDay();
                    }
    
                    // DB::table('hyper_guest_price')->insert($dates);
    
                    return [
                        'success' => true,
                        'message' => 'Ratepush data saved successfully!',
                    ];
                }
            }
    
            return [
                'success' => false,
                'message' => 'Request failed or no success element found!',
                'response' => $response->body(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
    
    /*public function bookPush($hotelCode, $start, $end , $ratePlanCode, $amountBeforetax, $amountAftertax, $tax, $firstname, $lastname, $phone, $email, $postalcode, $address, $state, $city, $birthdate, $age )
    {
        $url = 'https://hcm.hyperguest.io/envelope/booking/OTA/reservation';
    
        $xmlData = '<?xml version="1.0" encoding="UTF-8"?>
        <soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope/">
            <soap:Header>
                <wsse:Security soap:mustUnderstand="1" xmlns:wsse="http://schemas.xmlsoap.org/ws/2003/06/secext" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                    <wsse:UsernameToken>
                        <wsse:Username>tisyastays-ota</wsse:Username>
                        <wsse:Password Type="wsse:PasswordText">3g4oht2825ng0669597006295572600</wsse:Password>
                    </wsse:UsernameToken>
                </wsse:Security>
            </soap:Header>
            <soap:Body>
                <OTA_HotelResNotifRQ xmlns="http://www.opentravel.org/OTA/2003/05" Version="1.0" EchoToken="123abc" ResStatus="Commit" TimeStamp="2021-06-17T14:31:00Z">
                    <HotelReservations>
                        <HotelReservation CreateDateTime="2021-06-17T14:31:00Z">
                            <UniqueID Type="14" ID="' . $hotelCode . '"/>
                            <RoomStays>
                                <RoomStay>
                                    <RatePlans>
                                        <RatePlan RatePlanCode="'.$ratePlanCode.'"/>
                                    </RatePlans>
                                    <RoomRates>
                                        <RoomRate RoomTypeCode="STD" RatePlanCode="'.$ratePlanCode.'" NumberOfUnits="1">
                                            <Rates>
                                                <Rate>
                                                    <Base AmountBeforeTax="'. $amountBeforetax .'" AmountAfterTax="'. $amountAftertax .'" CurrencyCode="INR">
                                                        <Taxes>
                                                            <Tax Amount="'.$tax.'" CurrencyCode="INR" Type="exclusive"/>
                                                        </Taxes>
                                                    </Base>
                                                    <Total AmountBeforeTax="'. $amountBeforetax .'" AmountAfterTax="'. $amountAftertax .'" CurrencyCode="INR">
                                                        <Taxes>
                                                            <Tax Amount="'.$tax.'" CurrencyCode="INR" Type="exclusive"/>
                                                        </Taxes>
                                                    </Total>
                                                </Rate>
                                            </Rates>
                                        </RoomRate>
                                    </RoomRates>
                                    <ResGuestRPHs>
                                        <ResGuestRPH RPH="1"/>
                                    </ResGuestRPHs>
                                    <GuestCounts IsPerRoom="1">
                                        <GuestCount AgeQualifyingCode="10" Count="1"/>
                                    </GuestCounts>
                                    <TimeSpan Start="'.$start.'" End="'.$end.'"/>
                                    <Total AmountBeforeTax="'. $amountBeforetax .'" AmountAfterTax="'. $amountAftertax .'" CurrencyCode="INR">
                                        <Taxes>
                                            <Tax Amount="'.$tax.'" CurrencyCode="INR" Type="exclusive"/>
                                        </Taxes>
                                    </Total>
                                    <BasicPropertyInfo HotelCode="' . $hotelCode . '"/>
                                </RoomStay>
                            </RoomStays>
                            <ResGuests>
                                <ResGuest ResGuestRPH="1" PrimaryIndicator="1">
                                    <Profiles>
                                        <ProfileInfo>
                                            <Profile ProfileType="1">
                                                <Customer>
                                                    <PersonName>
                                                        <GivenName>'.$firstname.'</GivenName>
                                                        <Surname>'.$lastname.'</Surname>
                                                    </PersonName>
                                                    <Telephone PhoneNumber="'.$phone.'"/>
                                                    <Email>'.$email.'</Email>
                                                    <Address>
                                                        <PostalCode>'.$postalcode.'</PostalCode>
                                                        <AddressLine>'.$address.'</AddressLine>
                                                        <Country Code="IN"/>
                                                        <StateProv>'.$state.'</StateProv>
                                                        <City>'.$city.'</City>
                                                    </Address>
                                                    <BirthDate>'.$birthdate.'</BirthDate>
                                                    <Age>'.$age.'</Age>
                                                </Customer>
                                            </Profile>
                                        </ProfileInfo>
                                    </Profiles>
                                </ResGuest>
                            </ResGuests>
                            <ResGlobalInfo>
                                <Total AmountBeforeTax="'. $amountBeforetax .'" AmountAfterTax="'. $amountAftertax .'" CurrencyCode="INR">
                                    <Taxes>
                                        <Tax Amount="'.$tax.'" CurrencyCode="INR" Type="exclusive"/>
                                    </Taxes>
                                </Total>
                                <Guarantee>
                                    <GuaranteesAccepted>
                                        <GuaranteeAccepted>
                                            <PaymentCard CardType="1" CardCode="MC" CardNumber="4321-4321-4321-4327" ExpireDate="1225" SeriesCode="123">
                                                <CardHolderName>Billing Department</CardHolderName>
                                            </PaymentCard>
                                        </GuaranteeAccepted>
                                    </GuaranteesAccepted>
                                </Guarantee>
                                <Profiles>
                                    <ProfileInfo>
                                        <Profile>
                                            <Customer>
                                                <PersonName>
                                                    <GivenName>Morty</GivenName>
                                                    <Surname>Smith</Surname>
                                                </PersonName>
                                                <Telephone PhoneNumber="1-800-Morty"/>
                                                <Email>morty@example.com</Email>
                                                <BirthDate>2075-05-05</BirthDate>
                                                <Age>50</Age>
                                            </Customer>
                                        </Profile>
                                    </ProfileInfo>
                                </Profiles>
                            </ResGlobalInfo>
                            <TPA_Extensions>
                                <AgencyName>Booking Engine</AgencyName>
                                <Source>I copy and pasted this from the HyperGuest documentation</Source>
                                <IsTest>true</IsTest>
                                <GroupBooking>false</GroupBooking>
                            </TPA_Extensions>
                        </HotelReservation>
                    </HotelReservations>
                </OTA_HotelResNotifRQ>
            </soap:Body>
        </soap:Envelope>';
    
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'text/xml',
            ])->withBody($xmlData, 'text/xml')->post($url);
    
            if ($response->successful()) {
                $xmlObject = simplexml_load_string($response->body());
                $xmlObject->registerXPathNamespace('ns', 'http://www.opentravel.org/OTA/2003/05');
                $success = $xmlObject->xpath('//ns:Success');
    
                if (!empty($success)) {
                    // $startDate = Carbon::parse($start);
                    // $endDate = Carbon::parse($end);
                    // $dates = [];
                    // while ($startDate <= $endDate) {
                    //     $dates[] = [
                    //         'hyper_guest_id' => $hotelCode,
                    //         'price' => $amountAftertax,
                    //         'price_date' => $startDate->format('Y-m-d'),
                    //         'status' => '1',
                    //         'created_at' => now(),
                    //         'updated_at' => now(),
                    //     ];
                    //     $startDate->addDay();
                    // }
    
                    // DB::table('hyper_guest_availabilities')->update($dates);
    
                    return [
                        'success' => true,
                        'message' => 'Booking created successfully!',
                    ];
                }
            }
    
            return [
                'success' => false,
                'message' => 'Request failed or no success element found!',
                'response' => $response->body(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }*/
}