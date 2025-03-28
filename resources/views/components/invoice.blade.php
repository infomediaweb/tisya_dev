<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        *{
            margin: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }
        p{
            margin-bottom: 1em;
        }
    </style>
</head>
<body>
    <table class="table" style=" max-width: 800px; width:100%; margin: auto;border-collapse: collapse;color: #726659;line-height: 1.5;font-size:14px;font-family: Arial, Helvetica, sans-serif;">
        <tr>
            <td style="border-bottom:1px solid #726659; vertical-align: middle;padding: 20px;">
                <img src="{{ asset('assets/images/invoice-logo.png') }}" width="218" height="65" alt="">
            </td>
            <td style="text-align:right;border-bottom:1px solid #726659; vertical-align: middle;padding: 20px;">
                <h3 style="font-size: 20px;margin-bottom:0;">Tax Invoice</h3>
                <p>
                    Invoice No: VRF/UK/PI/23-24/05
                    <br/>Date: 06-Sep-2023
                </p>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 20px 0;">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="50%" valign="top" style="font-size: 13px;padding: 0 20px;">
                            <h3 style="color:#87B23F;font-size: 16px;margin-bottom: 5px;">From</h3>
                            <p>
                                <strong>Unique Vacation Homes Private Limited</strong><br/>
                                2nd Floor, Shagun Tower, Shagun Tower<br/>
                                88 New Road/Amrit Kaur Road, Dehradun,<br/>
                                Uttarakhand-248001
                            </p>


                            <p style="margin-bottom: 0;">
                                GSTIN: 05AABCU7980M1ZR<br/>
                                CIN: U70102DL2015PTC277663<br/>
                                SAC: 9963
                            </p>
                        </td>
                        <td width="50%" valign="top" style="font-size: 13px;padding: 0 20px;">
                            <h3 style="color:#87B23F;font-size: 16px;margin-bottom: 5px;">To</h3>
                            <p>
                                <strong>Purple Martini Entertainment Private Limited</strong><br/>
                                H No. 725 2, St Anthony Praise, Anjuna Mapusa,<br/>
                                North Goa, Goa, 403509<br/>
                            </p>

                            <p>GSTIN: 30AAECP6505C2Z1</p>


                            <p style="margin-bottom: 0;">
                                State Code: <strong>02</strong><br/>
                                Place of Supply: <strong>Kasauli, Himachal Pradesh</strong>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table width="100%" cellpadding="0" cellspacing="0">
                     <tr style="background-color: #EEE5DB;">
                        <td style="border-top:1px solid #726659;border-right:1px solid #707070;border-bottom:1px solid #726659;font-weight: 700; font-size: 14px;padding: 7px 10px 7px 20px;">Particulars</td>
                        <td style="border-top:1px solid #726659;border-right:1px solid #707070;border-bottom:1px solid #726659;font-weight: 700; font-size: 14px;padding: 7px 10px 7px 10px; " align="center">SAC</td>
                        <td style="border-top:1px solid #726659;border-right:1px solid #707070;border-bottom:1px solid #726659;font-weight: 700; font-size: 14px;padding: 7px 10px; white-space: nowrap;" align="right">GST</td>
                        <td style="border-top:1px solid #726659;border-bottom:1px solid #726659;font-weight: 700; font-size: 14px;padding: 7px 20px 7px 10px; white-space: nowrap;" align="right">Total Amount</td>
                     </tr>
                     <tr>
                        <td style="border-bottom:1px solid #726659;border-right:1px solid #707070;font-size: 14px;padding: 7px 10px 7px 20px;vertical-align:top;">
                            Room charges for use of: <br><b>Lion’s Den, 7 Bedrooms, Nainital Hills</b>
                        </td>
                        <td style="border-bottom:1px solid #726659;border-right:1px solid #707070;font-size: 14px;padding: 7px 10px;white-space: nowrap;vertical-align:top;" align="center">
                            <strong>9963</strong>
                        </td>
                        <td style="border-bottom:1px solid #726659;border-right:1px solid #707070;font-size: 14px;padding: 7px 10px;white-space: nowrap;vertical-align:top;" align="right">
                            <strong>INR 15,254.24</strong>
                        </td>
                        <td style="border-bottom:1px solid #726659;font-size: 14px;padding: 7px 20px 7px 10px;white-space: nowrap;vertical-align:top;" align="right">
                            <strong>INR 1,00,000</strong>
                        </td>
                     </tr>
                     <tr>
                        <td style="border-bottom:1px solid #726659;border-right:1px solid #707070;font-size: 14px;padding: 7px 10px 7px 20px;">
                            Cook Charges
                        </td>
                        <td style="border-bottom:1px solid #726659;border-right:1px solid #707070;font-size: 14px;padding: 7px 10px;white-space: nowrap;vertical-align:top;" align="center">
                            <strong>9963</strong>
                        </td>
                        <td style="border-bottom:1px solid #726659;border-right:1px solid #707070;font-size: 14px;padding: 7px 10px;white-space: nowrap;" align="right">
                            <strong>INR 180</strong>
                        </td>
                        <td style="border-bottom:1px solid #726659;font-size: 14px;white-space: nowrap;padding: 7px 20px 7px 10px;" align="right">
                            <strong>INR 2,180</strong>
                        </td>
                     </tr>
                     <tr>
                        <td style="padding: 7px 10px 7px 20px;border-bottom:1px solid #726659;border-right:1px solid #707070;font-size: 14px;">
                            Dedicated staff charges
                        </td>
                        <td style="border-bottom:1px solid #726659;border-right:1px solid #707070;font-size: 14px;padding: 7px 10px;white-space: nowrap;vertical-align:top;" align="center">
                            <strong>9963</strong>
                        </td>
                        <td style="border-bottom:1px solid #726659;border-right:1px solid #707070;font-size: 14px;padding: 7px 10px;white-space: nowrap;" align="right">
                            <strong>INR 108</strong>
                        </td>
                        <td style="border-bottom:1px solid #726659;font-size: 14px;padding: 7px 20px 7px 10px;white-space: nowrap;" align="right">
                            <strong>INR 1,308</strong>
                        </td>
                     </tr>
                     <tr>
                        <td colspan="4" style="font-size: 14px;padding: 7px 20px;border-bottom:1px solid #726659;">
                            <b>Arrival date:</b> 25-MAY-2024<br>
                            <b>Departure date:</b> 28-MAY-2024
                        </td>
                     </tr>
                     <tr>
                        <td colspan="4" style="font-size: 14px;padding: 7px 20px;border-bottom:1px solid #726659;">
                            <strong>INR xxxxxx per day x 7 rooms</strong>
                        </td>
                     </tr>
                     <tr style="background-color: #EEE5DB;">
                        <td style="color:#000000;border-right:1px solid #707070;border-bottom:1px solid #726659;font-weight: 700; font-size: 16px;padding: 7px 10px 7px 20px;" align="right">Grand Total License Fee</td>
                        <td colspan="3" style="color:#000000;border-bottom:1px solid #726659;font-weight: 700; font-size: 16px;padding: 7px 20px 7px 10px;white-space: nowrap;" align="right">INR 1,03,488</td>
                     </tr>
                     <tr>
                        <td colspan="4" style="font-size: 14px;padding: 7px 20px;">
                            <strong>Amount in words: One lakh three thousand four hundred eighty eight only</strong>
                        </td>
                     </tr>
                     <tr style="background-color: #726659;">
                        <td colspan="4" style="font-size: 14px;color: #ffffff;padding: 7px 20px;">
                            <strong>GST Charges:</strong> <em>Rooms 18%, Cook 9%, Dedicated staff 18%</em>
                        </td>
                     </tr>
                     <tr>
                        <td colspan="4" style="font-size: 12px;padding: 7px 20px;">
                            <strong>Note </strong>
                            <ul style="padding-left:1em;">
                                <li>
                                    Make all Cheque / Demand Draft payable to “Unique Vacation Homes Private Limited”
                                </li>
                                <li>
                                    Cheque / Demand Draft to be sent to D-925 New Friends Colony, New Delhi, 110025
                                </li>
                                <li>
                                    Terms and condition of booking and use of said premises apply
                                </li>
                                <li>
                                    RCM - No (Yes/No)
                                </li>
                            </ul>
                            <br><br><br><br><br>
                            <strong>This is computer generated & does not require signature</strong>
                            <br><br>
                        </td>
                     </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
