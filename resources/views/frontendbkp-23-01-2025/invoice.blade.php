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
                <img src="https://tisyastays.rentals.management/assets/images/invoice-logo.png" width="218" height="65" alt="">
            </td>
            <td style="text-align:right;border-bottom:1px solid #726659; vertical-align: middle;padding: 20px;">
                <h3 style="font-size: 20px;margin-bottom:0;">Tax Invoice</h3>
                <p>
                    Invoice No: 2024-25/@php  echo 'TS/005'; @endphp
                    <br/>Date: {{ date('F j, Y', strtotime($detail->checkout_date))  }}
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
                                <strong>{{ $companyInfo->company_name }}</strong><br/>
                                {{ $companyInfo->company_address }}
                            </p>
                            <p style="margin-bottom: 0;">
                                GSTIN: {{ $companyInfo->gst_no }}<br/>
                                CIN: {{ $companyInfo->cin_no }}<br/>
                                SAC: {{ $sac }}
                            </p>
                        </td>
                        <td width="50%" valign="top" style="font-size: 13px;padding: 0 20px;">
                            <h3 style="color:#87B23F;font-size: 16px;margin-bottom: 5px;">To</h3>
                            <p>
                                <strong>{{ $detail->customer_detail['first_name'] }} {{ $detail->customer_detail['last_name'] }}</strong><br/>
                            </p>
                            <p>Email: {{ $detail->customer_detail['email'] }}<br/>
                                Mobile No.: {{ $detail->customer_detail['mobile_number'] }}
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
                        <td style="border-top:1px solid #726659;border-right:1px solid #707070;border-bottom:1px solid #726659;font-weight: 700; font-size: 14px;padding: 7px 10px 7px 10px; " align="center">Taxable Amount</td>
                        <td style="border-top:1px solid #726659;border-right:1px solid #707070;border-bottom:1px solid #726659;font-weight: 700; font-size: 14px;padding: 7px 10px; white-space: nowrap;" align="right">GST</td>
                        <td style="border-top:1px solid #726659;border-bottom:1px solid #726659;font-weight: 700; font-size: 14px;padding: 7px 20px 7px 10px; white-space: nowrap;" align="right">Total Amount</td>
                    </tr>
                    <tr>
                        <td style="border-bottom:1px solid #726659;border-right:1px solid #707070;font-size: 14px;padding: 7px 10px 7px 20px;vertical-align:top;">
                            Room charges for use of: <br><b>{{ $detail->home->home_name }}</b>
                        </td>
                        <td style="border-bottom:1px solid #726659;border-right:1px solid #707070;font-size: 14px;padding: 7px 10px;white-space: nowrap;vertical-align:top;" align="center">
                            <strong>{{ $sac }}</strong>
                        </td>
                        <td style="border-bottom:1px solid #726659;border-right:1px solid #707070;font-size: 14px;padding: 7px 10px;white-space: nowrap;vertical-align:top;" align="right">
                            <strong>INR {{ $detail->payable_amount - $detail->tax_amount }}</strong>
                        </td>
                        <td style="border-bottom:1px solid #726659;border-right:1px solid #707070;font-size: 14px;padding: 7px 10px;white-space: nowrap;vertical-align:top;" align="right">
                            <strong>INR {{ number_format($detail->tax_amount) }}</strong>
                        </td>
                        <td style="border-bottom:1px solid #726659;font-size: 14px;padding: 7px 20px 7px 10px;white-space: nowrap;vertical-align:top;" align="right">
                            <strong>INR {{ number_format($detail->payable_amount) }}</strong>
                        </td>
                    </tr>
                   
                    <tr>
                        <td colspan="5" style="font-size: 14px;padding: 7px 20px;border-bottom:1px solid #726659;">
                            <b>Arrival date:</b> {{ date('F j, Y', strtotime($detail->checkin_date))  }}<br>
                            <b>Departure date:</b> {{ date('F j, Y', strtotime($detail->checkout_date))  }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" style="font-size: 14px;padding: 7px 20px;border-bottom:1px solid #726659;">
                            <strong>INR {{ number_format($detail->per_night_price) }} per day x {{ $date_difference_count }} rooms</strong>
                        </td>
                    </tr>
                    <tr style="background-color: #EEE5DB;">
                        <td style="color:#000000;border-right:1px solid #707070;border-bottom:1px solid #726659;font-weight: 700; font-size: 16px;padding: 7px 10px 7px 20px;" align="right">Grand Total</td>
                        <td colspan="4" style="color:#000000;border-bottom:1px solid #726659;font-weight: 700; font-size: 16px;padding: 7px 20px 7px 10px;white-space: nowrap;" align="right">INR {{ number_format($totalPrice) }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" style="font-size: 14px;padding: 7px 20px;">
                            <strong style="text-transform: capitalize">{{ $totalPriceInWords }}</strong>
                        </td>
                    </tr>
                    <tr style="background-color: #726659;">
                        <td colspan="5" style="font-size: 14px;color: #ffffff;padding: 7px 20px;">
                            <strong>GST Charges:</strong> <em>Rooms 12%</em>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" style="font-size: 12px;padding: 7px 20px;">
                            <strong>Note </strong>
                            <ul style="padding-left:1em;">
                                <li>
                                    Make all Cheque / Demand Draft payable to {{ $companyInfo->company_name }}
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
