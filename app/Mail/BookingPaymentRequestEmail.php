<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\TblHomeImageVideo;
use App\Models\TblHome;

class BookingPaymentRequestEmail extends Mailable{
    use Queueable, SerializesModels;

    public $mailData;

    /**
     * Create a new message instance.
     */
    public function __construct($mailData){
        $this->mailData = $mailData['bookingDetail'];
        $this->paymentRequestInfo = $mailData['paymentRequestInfo'];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope{
        return new Envelope(
            subject: 'Tisya Stay| Booking Payment Request[Your Booking Refrence Number Is- '.$this->mailData->booking_id.']',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content{
        $prorpertyAssetsDetail = TblHomeImageVideo::where('home_id', $this->mailData->property_id)->where('type', 'image')->first();
        $property = TblHome::where('id', $this->mailData->property_id)->first();
        return new Content(
            view: 'emails.booking-payment-request',
            with: [
                'data' => $this->mailData,
                'paymentRequestInfo'=>$this->paymentRequestInfo,
                'prorpertyAssetsDetail'  => $prorpertyAssetsDetail,
                'property'  => $property
            ],
        );
        die();
    }

    /**
     * Get the attachments for the message.
     *
     * @return array

     */
    public function attachments(): array{
        return [];
    }
}
