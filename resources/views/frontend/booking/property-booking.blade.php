@extends('layout.main')
@section('content')
<style>
    .booking-information .booking-amount .input-group .btn.btn-danger{
        background-color: #dc3545 !important
    }
</style>
<section class="section section-top section-booking">
    <div class="container">
        <form action="#" method="post" id="yourFormId">
            @csrf
            <div class="row g-5">
                <div class="col-12 col-lg-7">
                     <div class="form-wrapper pt-lg-4">
                        <h2>Guest Information</h2>
                        <div class="row g-3">
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label>First Name <sup>*</sup></label>
                                    <input type="text" class="form-control" name="first_name" id="first_name"  value="{{ old('first_name') }}">
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label>Last Name <sup>*</sup></label>
                                    <input type="text" class="form-control" name="last_name" id="last_name" value="{{ old('last_name') }}">
                                </div>
                            </div>
                            <!--<div class="col-12 col-lg-6">-->
                            <!--    <div class="form-group">-->
                            <!--        <label>Phone Number <sup>*</sup></label>-->
                            <!--        <input type="text" class="form-control" name="phone_number" id="phone_number" value="{{ old('phone_number') }}">-->
                            <!--    </div>-->
                            <!--</div>-->
                            
                            
                            <div class="col-12">
                                <div class="form-group d-flex">
                                    <div class="me-2">
                                        <label class="mb-1">Phone Number <sup class="text-danger">*</sup></label>
                                        <select name="country_code" id="country_code" class="form-control country_code" style="width: 200px;">
                                            <?php
                                            $countries = [
                                                ["code" => "91", "name" => "India (+91)", "countryCode" => "IN"],
                                                ["code" => "47", "name" => "Norway (+47)", "countryCode" => "GB"],
                                                ["code" => "44", "name" => "UK (+44)", "countryCode" => "US"],
                                                ["code" => "213", "name" => "Algeria (+213)", "countryCode" => "DZ"],
                                                ["code" => "376", "name" => "Andorra (+376)", "countryCode" => "AD"],
                                                ["code" => "244", "name" => "Angola (+244)", "countryCode" => "AO"],
                                                ["code" => "1264", "name" => "Anguilla (+1264)", "countryCode" => "AI"],
                                                ["code" => "1268", "name" => "Antigua & Barbuda (+1268)", "countryCode" => "AG"],
                                                ["code" => "54", "name" => "Argentina (+54)", "countryCode" => "AR"],
                                                ["code" => "374", "name" => "Armenia (+374)", "countryCode" => "AM"],
                                                ["code" => "297", "name" => "Aruba (+297)", "countryCode" => "AW"],
                                                ["code" => "61", "name" => "Australia (+61)", "countryCode" => "AU"],
                                                ["code" => "43", "name" => "Austria (+43)", "countryCode" => "AT"],
                                                ["code" => "994", "name" => "Azerbaijan (+994)", "countryCode" => "AZ"],
                                                ["code" => "1242", "name" => "Bahamas (+1242)", "countryCode" => "BS"],
                                                ["code" => "973", "name" => "Bahrain (+973)", "countryCode" => "BH"],
                                                ["code" => "880", "name" => "Bangladesh (+880)", "countryCode" => "BD"],
                                                ["code" => "1246", "name" => "Barbados (+1246)", "countryCode" => "BB"],
                                                ["code" => "375", "name" => "Belarus (+375)", "countryCode" => "BY"],
                                                ["code" => "32", "name" => "Belgium (+32)", "countryCode" => "BE"],
                                                ["code" => "501", "name" => "Belize (+501)", "countryCode" => "BZ"],
                                                ["code" => "229", "name" => "Benin (+229)", "countryCode" => "BJ"],
                                                ["code" => "1441", "name" => "Bermuda (+1441)", "countryCode" => "BM"],
                                                ["code" => "975", "name" => "Bhutan (+975)", "countryCode" => "BT"],
                                                ["code" => "591", "name" => "Bolivia (+591)", "countryCode" => "BO"],
                                                ["code" => "387", "name" => "Bosnia Herzegovina (+387)", "countryCode" => "BA"],
                                                ["code" => "267", "name" => "Botswana (+267)", "countryCode" => "BW"],
                                                ["code" => "55", "name" => "Brazil (+55)", "countryCode" => "BR"],
                                                ["code" => "673", "name" => "Brunei (+673)", "countryCode" => "BN"],
                                                ["code" => "359", "name" => "Bulgaria (+359)", "countryCode" => "BG"],
                                                ["code" => "226", "name" => "Burkina Faso (+226)", "countryCode" => "BF"],
                                                ["code" => "257", "name" => "Burundi (+257)", "countryCode" => "BI"],
                                                ["code" => "855", "name" => "Cambodia (+855)", "countryCode" => "KH"],
                                                ["code" => "237", "name" => "Cameroon (+237)", "countryCode" => "CM"],
                                                ["code" => "1", "name" => "Canada (+1)", "countryCode" => "CA"],
                                                ["code" => "238", "name" => "Cape Verde Islands (+238)", "countryCode" => "CV"],
                                                ["code" => "1345", "name" => "Cayman Islands (+1345)", "countryCode" => "KY"],
                                                ["code" => "236", "name" => "Central African Republic (+236)", "countryCode" => "CF"],
                                                ["code" => "56", "name" => "Chile (+56)", "countryCode" => "CL"],
                                                ["code" => "86", "name" => "China (+86)", "countryCode" => "CN"],
                                                ["code" => "57", "name" => "Colombia (+57)", "countryCode" => "CO"],
                                                ["code" => "269", "name" => "Comoros (+269)", "countryCode" => "KM"],
                                                ["code" => "242", "name" => "Congo (+242)", "countryCode" => "CG"],
                                                ["code" => "682", "name" => "Cook Islands (+682)", "countryCode" => "CK"],
                                                ["code" => "506", "name" => "Costa Rica (+506)", "countryCode" => "CR"],
                                                ["code" => "385", "name" => "Croatia (+385)", "countryCode" => "HR"],
                                                ["code" => "53", "name" => "Cuba (+53)", "countryCode" => "CU"],
                                                ["code" => "90392", "name" => "Cyprus North (+90392)", "countryCode" => "CY"],
                                                ["code" => "357", "name" => "Cyprus South (+357)", "countryCode" => "CY"],
                                                ["code" => "42", "name" => "Czech Republic (+42)", "countryCode" => "CZ"],
                                                ["code" => "45", "name" => "Denmark (+45)", "countryCode" => "DK"],
                                                ["code" => "253", "name" => "Djibouti (+253)", "countryCode" => "DJ"],
                                                ["code" => "1809", "name" => "Dominica (+1809)", "countryCode" => "DM"],
                                                ["code" => "1809", "name" => "Dominican Republic (+1809)", "countryCode" => "DO"],
                                                ["code" => "593", "name" => "Ecuador (+593)", "countryCode" => "EC"],
                                                ["code" => "20", "name" => "Egypt (+20)", "countryCode" => "EG"],
                                                ["code" => "503", "name" => "El Salvador (+503)", "countryCode" => "SV"],
                                                ["code" => "240", "name" => "Equatorial Guinea (+240)", "countryCode" => "GQ"],
                                                ["code" => "291", "name" => "Eritrea (+291)", "countryCode" => "ER"],
                                                ["code" => "372", "name" => "Estonia (+372)", "countryCode" => "EE"],
                                                ["code" => "251", "name" => "Ethiopia (+251)", "countryCode" => "ET"],
                                                ["code" => "500", "name" => "Falkland Islands (+500)", "countryCode" => "FK"],
                                                ["code" => "298", "name" => "Faroe Islands (+298)", "countryCode" => "FO"],
                                                ["code" => "679", "name" => "Fiji (+679)", "countryCode" => "FJ"],
                                                ["code" => "358", "name" => "Finland (+358)", "countryCode" => "FI"],
                                                ["code" => "33", "name" => "France (+33)", "countryCode" => "FR"],
                                                ["code" => "594", "name" => "French Guiana (+594)", "countryCode" => "GF"],
                                                ["code" => "689", "name" => "French Polynesia (+689)", "countryCode" => "PF"],
                                                ["code" => "241", "name" => "Gabon (+241)", "countryCode" => "GA"],
                                                ["code" => "220", "name" => "Gambia (+220)", "countryCode" => "GM"],
                                                ["code" => "7880", "name" => "Georgia (+7880)", "countryCode" => "GE"],
                                                ["code" => "49", "name" => "Germany (+49)", "countryCode" => "DE"],
                                                ["code" => "233", "name" => "Ghana (+233)", "countryCode" => "GH"],
                                                ["code" => "350", "name" => "Gibraltar (+350)", "countryCode" => "GI"],
                                                ["code" => "30", "name" => "Greece (+30)", "countryCode" => "GR"],
                                                ["code" => "299", "name" => "Greenland (+299)", "countryCode" => "GL"],
                                                ["code" => "1473", "name" => "Grenada (+1473)", "countryCode" => "GD"],
                                                ["code" => "590", "name" => "Guadeloupe (+590)", "countryCode" => "GP"],
                                                ["code" => "671", "name" => "Guam (+671)", "countryCode" => "GU"],
                                                ["code" => "502", "name" => "Guatemala (+502)", "countryCode" => "GT"],
                                                ["code" => "224", "name" => "Guinea (+224)", "countryCode" => "GN"],
                                                ["code" => "245", "name" => "Guinea - Bissau (+245)", "countryCode" => "GW"],
                                                ["code" => "592", "name" => "Guyana (+592)", "countryCode" => "GY"],
                                                ["code" => "509", "name" => "Haiti (+509)", "countryCode" => "HT"],
                                                ["code" => "504", "name" => "Honduras (+504)", "countryCode" => "HN"],
                                                ["code" => "852", "name" => "Hong Kong (+852)", "countryCode" => "HK"],
                                                ["code" => "36", "name" => "Hungary (+36)", "countryCode" => "HU"],
                                                ["code" => "354", "name" => "Iceland (+354)", "countryCode" => "IS"],
                                                ["code" => "62", "name" => "Indonesia (+62)", "countryCode" => "ID"],
                                                ["code" => "98", "name" => "Iran (+98)", "countryCode" => "IR"],
                                                ["code" => "964", "name" => "Iraq (+964)", "countryCode" => "IQ"],
                                                ["code" => "353", "name" => "Ireland (+353)", "countryCode" => "IE"],
                                                ["code" => "972", "name" => "Israel (+972)", "countryCode" => "IL"],
                                                ["code" => "39", "name" => "Italy (+39)", "countryCode" => "IT"],
                                                ["code" => "1876", "name" => "Jamaica (+1876)", "countryCode" => "JM"],
                                                ["code" => "81", "name" => "Japan (+81)", "countryCode" => "JP"],
                                                ["code" => "962", "name" => "Jordan (+962)", "countryCode" => "JO"],
                                                ["code" => "7", "name" => "Kazakhstan (+7)", "countryCode" => "KZ"],
                                                ["code" => "254", "name" => "Kenya (+254)", "countryCode" => "KE"],
                                                ["code" => "686", "name" => "Kiribati (+686)", "countryCode" => "KI"],
                                                ["code" => "850", "name" => "Korea North (+850)", "countryCode" => "KP"],
                                                ["code" => "82", "name" => "Korea South (+82)", "countryCode" => "KR"],
                                                ["code" => "965", "name" => "Kuwait (+965)", "countryCode" => "KW"],
                                                ["code" => "996", "name" => "Kyrgyzstan (+996)", "countryCode" => "KG"],
                                                ["code" => "856", "name" => "Laos (+856)", "countryCode" => "LA"],
                                                ["code" => "371", "name" => "Latvia (+371)", "countryCode" => "LV"],
                                                ["code" => "961", "name" => "Lebanon (+961)", "countryCode" => "LB"],
                                                ["code" => "266", "name" => "Lesotho (+266)", "countryCode" => "LS"],
                                                ["code" => "231", "name" => "Liberia (+231)", "countryCode" => "LR"],
                                                ["code" => "218", "name" => "Libya (+218)", "countryCode" => "LY"],
                                                ["code" => "417", "name" => "Liechtenstein (+417)", "countryCode" => "LI"],
                                                ["code" => "370", "name" => "Lithuania (+370)", "countryCode" => "LT"],
                                                ["code" => "352", "name" => "Luxembourg (+352)", "countryCode" => "LU"],
                                                ["code" => "853", "name" => "Macao (+853)", "countryCode" => "MO"],
                                                ["code" => "389", "name" => "Macedonia (+389)", "countryCode" => "MK"],
                                                ["code" => "261", "name" => "Madagascar (+261)", "countryCode" => "MG"],
                                                ["code" => "265", "name" => "Malawi (+265)", "countryCode" => "MW"],
                                                ["code" => "60", "name" => "Malaysia (+60)", "countryCode" => "MY"],
                                                ["code" => "960", "name" => "Maldives (+960)", "countryCode" => "MV"],
                                                ["code" => "223", "name" => "Mali (+223)", "countryCode" => "ML"],
                                                ["code" => "356", "name" => "Malta (+356)", "countryCode" => "MT"],
                                                ["code" => "692", "name" => "Marshall Islands (+692)", "countryCode" => "MH"],
                                                ["code" => "596", "name" => "Martinique (+596)", "countryCode" => "MQ"],
                                                ["code" => "222", "name" => "Mauritania (+222)", "countryCode" => "MR"],
                                                ["code" => "269", "name" => "Mayotte (+269)", "countryCode" => "YT"],
                                                ["code" => "52", "name" => "Mexico (+52)", "countryCode" => "MX"],
                                                ["code" => "691", "name" => "Micronesia (+691)", "countryCode" => "FM"],
                                                ["code" => "373", "name" => "Moldova (+373)", "countryCode" => "MD"],
                                                ["code" => "377", "name" => "Monaco (+377)", "countryCode" => "MC"],
                                                ["code" => "976", "name" => "Mongolia (+976)", "countryCode" => "MN"],
                                                ["code" => "1664", "name" => "Montserrat (+1664)", "countryCode" => "MS"],
                                                ["code" => "212", "name" => "Morocco (+212)", "countryCode" => "MA"],
                                                ["code" => "258", "name" => "Mozambique (+258)", "countryCode" => "MZ"],
                                                ["code" => "95", "name" => "Myanmar (+95)", "countryCode" => "MN"],
                                                ["code" => "264", "name" => "Namibia (+264)", "countryCode" => "NA"],
                                                ["code" => "674", "name" => "Nauru (+674)", "countryCode" => "NR"],
                                                ["code" => "977", "name" => "Nepal (+977)", "countryCode" => "NP"],
                                                ["code" => "31", "name" => "Netherlands (+31)", "countryCode" => "NL"],
                                                ["code" => "687", "name" => "New Caledonia (+687)", "countryCode" => "NC"],
                                                ["code" => "64", "name" => "New Zealand (+64)", "countryCode" => "NZ"],
                                                ["code" => "505", "name" => "Nicaragua (+505)", "countryCode" => "NI"],
                                                ["code" => "227", "name" => "Niger (+227)", "countryCode" => "NE"],
                                                ["code" => "234", "name" => "Nigeria (+234)", "countryCode" => "NG"],
                                                ["code" => "683", "name" => "Niue (+683)", "countryCode" => "NU"],
                                                ["code" => "672", "name" => "Norfolk Islands (+672)", "countryCode" => "NF"],
                                                ["code" => "670", "name" => "Northern Marianas (+670)", "countryCode" => "NP"],
                                                ["code" => "47", "name" => "Norway (+47)", "countryCode" => "NO"],
                                                ["code" => "968", "name" => "Oman (+968)", "countryCode" => "OM"],
                                                ["code" => "680", "name" => "Palau (+680)", "countryCode" => "PW"],
                                                ["code" => "507", "name" => "Panama (+507)", "countryCode" => "PA"],
                                                ["code" => "675", "name" => "Papua New Guinea (+675)", "countryCode" => "PG"],
                                                ["code" => "595", "name" => "Paraguay (+595)", "countryCode" => "PY"],
                                                ["code" => "51", "name" => "Peru (+51)", "countryCode" => "PE"],
                                                ["code" => "63", "name" => "Philippines (+63)", "countryCode" => "PH"],
                                                ["code" => "48", "name" => "Poland (+48)", "countryCode" => "PL"],
                                                ["code" => "351", "name" => "Portugal (+351)", "countryCode" => "PT"],
                                                ["code" => "1787", "name" => "Puerto Rico (+1787)", "countryCode" => "PR"],
                                                ["code" => "974", "name" => "Qatar (+974)", "countryCode" => "QA"],
                                                ["code" => "262", "name" => "Reunion (+262)", "countryCode" => "RE"],
                                                ["code" => "40", "name" => "Romania (+40)", "countryCode" => "RO"],
                                                ["code" => "7", "name" => "Russia (+7)", "countryCode" => "RU"],
                                                ["code" => "250", "name" => "Rwanda (+250)", "countryCode" => "RW"],
                                                ["code" => "378", "name" => "San Marino (+378)", "countryCode" => "SM"],
                                                ["code" => "239", "name" => "Sao Tome & Principe (+239)", "countryCode" => "ST"],
                                                ["code" => "966", "name" => "Saudi Arabia (+966)", "countryCode" => "SA"],
                                                ["code" => "221", "name" => "Senegal (+221)", "countryCode" => "SN"],
                                                ["code" => "381", "name" => "Serbia (+381)", "countryCode" => "CS"],
                                                ["code" => "248", "name" => "Seychelles (+248)", "countryCode" => "SC"],
                                                ["code" => "232", "name" => "Sierra Leone (+232)", "countryCode" => "SL"],
                                                ["code" => "65", "name" => "Singapore (+65)", "countryCode" => "SG"],
                                                ["code" => "421", "name" => "Slovak Republic (+421)", "countryCode" => "SK"],
                                                ["code" => "386", "name" => "Slovenia (+386)", "countryCode" => "SI"],
                                                ["code" => "677", "name" => "Solomon Islands (+677)", "countryCode" => "SB"],
                                                ["code" => "252", "name" => "Somalia (+252)", "countryCode" => "SO"],
                                                ["code" => "27", "name" => "South Africa (+27)", "countryCode" => "ZA"],
                                                ["code" => "34", "name" => "Spain (+34)", "countryCode" => "ES"],
                                                ["code" => "94", "name" => "Sri Lanka (+94)", "countryCode" => "LK"],
                                                ["code" => "290", "name" => "St. Helena (+290)", "countryCode" => "SH"],
                                                ["code" => "1869", "name" => "St. Kitts (+1869)", "countryCode" => "KN"],
                                                ["code" => "1758", "name" => "St. Lucia (+1758)", "countryCode" => "SC"],
                                                ["code" => "249", "name" => "Sudan (+249)", "countryCode" => "SD"],
                                                ["code" => "597", "name" => "Suriname (+597)", "countryCode" => "SR"],
                                                ["code" => "268", "name" => "Swaziland (+268)", "countryCode" => "SZ"],
                                                ["code" => "46", "name" => "Sweden (+46)", "countryCode" => "SE"],
                                                ["code" => "41", "name" => "Switzerland (+41)", "countryCode" => "CH"],
                                                ["code" => "963", "name" => "Syria (+963)", "countryCode" => "SI"],
                                                ["code" => "886", "name" => "Taiwan (+886)", "countryCode" => "TW"],
                                                ["code" => "7", "name" => "Tajikstan (+7)", "countryCode" => "TJ"],
                                                ["code" => "66", "name" => "Thailand (+66)", "countryCode" => "TH"],
                                                ["code" => "228", "name" => "Togo (+228)", "countryCode" => "TG"],
                                                ["code" => "676", "name" => "Tonga (+676)", "countryCode" => "TO"],
                                                ["code" => "1868", "name" => "Trinidad & Tobago (+1868)", "countryCode" => "TT"],
                                                ["code" => "216", "name" => "Tunisia (+216)", "countryCode" => "TN"],
                                                ["code" => "90", "name" => "Turkey (+90)", "countryCode" => "TR"],
                                                ["code" => "7", "name" => "Turkmenistan (+7)", "countryCode" => "TM"],
                                                ["code" => "993", "name" => "Turkmenistan (+993)", "countryCode" => "TM"],
                                                ["code" => "1649", "name" => "Turks & Caicos Islands (+1649)", "countryCode" => "TC"],
                                                ["code" => "688", "name" => "Tuvalu (+688)", "countryCode" => "TV"],
                                                ["code" => "256", "name" => "Uganda (+256)", "countryCode" => "UG"],
                                                ["code" => "380", "name" => "Ukraine (+380)", "countryCode" => "UA"],
                                                ["code" => "971", "name" => "United Arab Emirates (+971)", "countryCode" => "AE"],
                                                ["code" => "598", "name" => "Uruguay (+598)", "countryCode" => "UY"],
                                                ["code" => "7", "name" => "Uzbekistan (+7)", "countryCode" => "UZ"],
                                                ["code" => "678", "name" => "Vanuatu (+678)", "countryCode" => "VU"],
                                                ["code" => "379", "name" => "Vatican City (+379)", "countryCode" => "VA"],
                                                ["code" => "58", "name" => "Venezuela (+58)", "countryCode" => "VE"],
                                                ["code" => "84", "name" => "Vietnam (+84)", "countryCode" => "VN"],
                                                ["code" => "1284", "name" => "Virgin Islands - British (+1284)", "countryCode" => "VG"],
                                                ["code" => "1340", "name" => "Virgin Islands - US (+1340)", "countryCode" => "VI"],
                                                ["code" => "681", "name" => "Wallis & Futuna (+681)", "countryCode" => "WF"],
                                                ["code" => "969", "name" => "Yemen (North) (+969)", "countryCode" => "YE"],
                                                ["code" => "967", "name" => "Yemen (South) (+967)", "countryCode" => "YE"],
                                                ["code" => "260", "name" => "Zambia (+260)", "countryCode" => "ZM"],
                                                ["code" => "263", "name" => "Zimbabwe (+263)", "countryCode" => "ZW"],
                                            ];
                                            foreach ($countries as $country) {
                                                echo "<option data-countryCode='{$country['countryCode']}' value='{$country['name']}'>{$country['name']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="flex-grow-1">
                                        <label class="mb-1 d-block">&nbsp;</label> <!-- Empty label for alignment -->
                                        <input type="text" class="form-control" name="phone_number" id="phone_number" 
                                               maxlength="12" minlength="6" >
                                    </div>
                                </div>
                            </div>
                            
                            
                            
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label>Email Address <sup>*</sup></label>
                                    <input type="text" class="form-control @error('email') border-danger  @enderror" name="email" id="email" value="{{ old('email') }}">
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label>State <sup>*</sup></label>
                                    <input type="text" class="form-control" name="state" id="state" value="{{ old('state') }}">
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label>City <sup>*</sup></label>
                                    <input type="text" class="form-control" name="city" id="city" value="{{ old('city') }}">
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label>Address</label>
                                    <input type="text" class="form-control" name="address" id="address" value="{{ old('addresss') }}">
                                </div>
                            </div>
                            
                        </div>
                     </div>
                </div>
                <div class="col-12 col-lg-5">
                    <div class="booking-information py-4 rounded-4">
                        <h3>Booking Information</h3>
                        <input type="hidden" name="property_id" id="property_id" value="{{ $property->id }}">
                        
                        <div class="swiper card-slider swiper-property-image swiper-property-image rounded-4">
                            <div class="swiper-wrapper">
                                @foreach($property->imagesVideos->where('type', 'image') as $imagesKey=>$images)
                                    <div class="swiper-slide">
                                        <div class="imgBox">
                                            <img loading="lazy"
                                                src="{{ asset($images->filename) }}"
                                                class="w-100" alt="Image Title Goes Here">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="swiper-button-prev"></div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-pagination"></div>
                        </div>
                        <div class="booking-info-text">
                            <div class="property-name mb-3">
                                <h3 class="mb-2 text-primary fw-medium mb-3">{{ $property->home_name }}</h3>
                            </div>
                            <ul class="list-unstyled small">
                                <li class="mb-3">
                                   <div class="row gx-5">
                                       <div class="col-6">
                                           Arrival<br> <strong>{{ date('d M Y', strtotime($requestParameters['ci_date'] ?? '')) }}</strong>
                                       </div>
                                       <div class="col-6 border-start">
                                           Departure<br><strong>{{ date('d M Y', strtotime($requestParameters['co_date'] ?? '')) }}</strong>
                                       </div>
                                   </div>
                                </li> 
                                <li>
                                    <strong>No. of Nights</strong> -  {{ $requestParameters['tot_no_of_days'] == 1 ? $requestParameters['tot_no_of_days'] . ' night' : $requestParameters['tot_no_of_days'] . ' nights' }}
                                </li>
                                <li>
                                <strong>No. of Guests</strong> -  {{ $requestParameters['tot_guest'] == 1 ? $requestParameters['tot_guest'] . ' Guest' : $requestParameters['tot_guest'] . ' Guests' }} , {{ $requestParameters['childrenCount'] == 1 ? $requestParameters['childrenCount'] . ' Children' : $requestParameters['childrenCount'] . ' Children' }}
                               
                                </li>
                           </ul>



                            <div class="col-12">
                                <div class="table-subtotal">
                                    <table class="table  table-borderless">
                                        <tr class="first-tr">
                                            <td>&#8377;<span class="PricePerNight">{{ $requestParameters['price_per_night_num_formatted'] ?? '' }}</span> x <span class="totalNight">{{ $requestParameters['tot_no_of_days'] == 1 ? $requestParameters['tot_no_of_days'] . ' night' : $requestParameters['tot_no_of_days'] . ' nights' }}</span></td>
                                            <td align="right">&#8377;<span class="PriceWithPerNight">{{ $requestParameters['total_price_multiple'] ?? '' }}</span></td>
                                        </tr>
                                        @if($requestParameters['additionalChargesAmount'])
                                        <tr class="second-tr">
                                            <td>{{ $requestParameters['additional_charges_name'] ?? '' }}</td>
                                            <td align="right">&#8377; <span class="additionalChargesAmount">{{ $requestParameters['additionalChargesAmount'] ?? '' }}</span></td>
                                        </tr>
                                        @endif

                                        

                                        @if($requestParameters['total_extra_guest_charge'] != 0)
                                        <tr class="second-tr">
                                            <td> Extra charge (<span class="extraGuestCharge">{{ $requestParameters['extra_guest_charge'] ?? '' }}</span>
                                                     x <span class="totalNight">{{ $requestParameters['tot_no_of_days'] ?? '' }}</span> )</td>
                                            <td align="right">&#8377; <span class="totalExtraGuestCharge">{{ $requestParameters['total_extra_guest_charge'] ?? '' }}</span></td>
                                        </tr>
                                        @endif
                                        
                                        @if($requestParameters && isset($requestParameters['discountAmount']))
                                        <tr class="second-tr">
                                            <td>Offer Code <span>{{ $requestParameters['discountCode'] ?? '' }}</span></td>
                                            <td align="right">-&#8377; <span class="additionalChargesAmount">{{ $requestParameters['discountAmount'] ?? '' }}</span></td>
                                        </tr>
                                        @endif
                                        
                                        <tr>
                                            <td>Taxes (<span class="tax">{{ $requestParameters['tax'] ?? '' }}</span>%)</td>
                                            <td align="right">&#8377; <span class="taxAmount">{{ $requestParameters['formatted_total_taxable_amount'] ?? '' }}</span></td>
                                        </tr>
                                        <tr class="fw-bold">
                                            <td>Total incl. taxes</td>
                                            <td align="right">&#8377;<span class="TotalAmount">{{ $requestParameters['num_formatted_tot_price'] ?? '' }}</span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        
                            
                        <button id="payNowcreate"  class="btn btn-primary rounded-pill fw-bold w-100 booking_form_submit_btn">PAY NOW
                        &nbsp;&nbsp;
                        <span id="spinner_new_form" class="spinner-border spinner-border-sm " style="color: #fff; display: none;" role="status"  aria-hidden="true" ></span>
                        
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    
    document.addEventListener("DOMContentLoaded", function(){
          new Swiper(".swiper-property-image", {
            spaceBetween: 30,
            allowTouchMove: false,
            pagination: {
                el: ".swiper-property-image .swiper-pagination",
                dynamicBullets: true,
                clickable: true
            },  
            navigation: {
                nextEl: ".swiper-property-image .swiper-button-next",
                prevEl: ".swiper-property-image .swiper-button-prev",
            },          
            mousewheel: {
                enabled: true,  
                forceToAxis: true             
            },
        });

        
        
        $('#payNowcreate').on('click', function(e) {
        e.preventDefault();  
        
        var button = document.getElementById('payNowcreate');
        var spinner = document.getElementById('spinner_new_form');
        spinner.style.display = 'inline-block';
        button.disabled = true;
        
        let formdata = $('#yourFormId').serialize();
        const requestParameters = @json($requestParameters);
        
        $.each(requestParameters, function (key, value) {
            formdata += '&' + encodeURIComponent(key) + '=' + encodeURIComponent(value);
        });

        $.ajax({
            url: "{{ route('property-booking') }}",  
            type: "POST",  
            data: formdata,  
            requestParameters: requestParameters,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')  
            },
            success: function(response) {
                if (response['status'] == true) {
                    
                    spinner.style.display = 'none';
                    button.disabled = false;
                    
                    if(response.orderId){
                    let order_id =  response.orderId;
                    let razorpay_id =  response.razorpayId;
                    let amount =  response.amount;
                        var options = {
                            "key": razorpay_id,
                            "amount": amount, 
                            "currency": "INR",
                            "name": "Vendor Payment",
                            "description": "enter text here",
                            "order_id": order_id, 
                            "handler": function (response){
                                console.log(response.razorpay_order_id,"payment done");
                                /* Payment Status */
                                $.ajax({
                                    type:'GET',
                                    url:"{{ route('property-booking-update') }}",
                                    data:{
                                        razorpay_order_id:response.razorpay_order_id,
                                        razorpay_payment_id:response.razorpay_payment_id
                                    },
                                    success:function(data){
                                       
                                        window.location.replace("{{ route('payment.thankYou') }}?orderId=" + data.orderId);
                                    }
                                });
                            },
                            "prefill": {
                                "name": "",
                                "email": ""
                            },
                            "theme": {
                                "color": "#3399cc"
                            },
                            "modal": {
                                "ondismiss": function () {
                                    window,location.replace("{{ route('payment-failure') }}");

                                }
                            }
                        };
                        var rzp = new Razorpay(options);
                        rzp.open();
                        rzp.on('payment.failed', function (response){
                            window,location.replace("{{ route('payment-failure') }}");
                        });
                    }
                }
                else {
                    if (response.status === false && response.redirect) {
                    window.location.href = response.redirect;
                    }
                    var errors = response['errors']; 
                    if (errors) {
                        $.each(errors, function(key, value) {
                            console.log(value[0], "value[0]"); 
                            var elementId = key.replace(/\./g, '_'); 
                            console.log(elementId, "key");
                            var inputElement = $('#' + elementId);
                            inputElement.addClass('border-danger');  
                            inputElement.next('p').addClass('text-danger').html(value[0]);
                        });
                    }
                    spinner.style.display = 'none';
                    button.disabled = false;
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX request failed:", error); 
            }
        });
    });
    
    
    $(document).ready(function() {
        $('#first_name').on('input', function() {
            $('#first_name').removeClass('border-danger').html('');
        });
        $('#last_name').on('input', function() {
            $('#last_name').removeClass('border-danger').html('');
        });
        $('#email').on('input', function() {
            $('#email').removeClass('border-danger').html('');
        });
        $('#phone_number').on('input', function() {
            $('#phone_number').removeClass('border-danger').html('');
        });
        $('#city').on('input', function() {
            $('#city').removeClass('border-danger').html('');
        });
        $('#state').on('input', function() {
            $('#state').removeClass('border-danger').html('');
        });
        $('#address').on('input', function() {
            $('#address').removeClass('border-danger').html('');
        });
       
    });
    
    
    function toggleButtonState() {
        var phoneNumber = $('#phone_number').val();
        phoneNumber = phoneNumber.replace(/[^0-9]/g, '').slice(0, 12);
        if(phoneNumber[0] == '0'){
            $('#phone_number').addClass('border-danger');
            $('#phone_number').siblings('p').addClass('text-danger').html("Phone number can't start with 0.");
            
        }else{
            $('#phone_number').siblings('p').addClass('text-danger').html("");
            $('#phone_number').removeClass('border-danger').html('');
        }
        $('#phone_number').val(phoneNumber);
    }
    $('#phone_number').on('input', function() {
        toggleButtonState();
    }); 
    
    })
    
    
    
</script>
@endsection