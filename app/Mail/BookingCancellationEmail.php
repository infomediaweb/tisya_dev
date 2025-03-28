<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\TblHomeImageVideo;

class BookingCancellationEmail extends Mailable{
    use Queueable, SerializesModels;

    public $mailData;

    /**
     * Create a new message instance.
     */
    public function __construct($mailData){
        $this->mailData = $mailData['bookingDetail'];
        $this->id = $mailData['id'];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope{
        return new Envelope(
            subject: 'Your Booking Is Cancelled[Your Booking Refrence Number Is- '.$this->mailData->booking_id.']',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content{
        $prorpertyAssetsDetail = TblHomeImageVideo::where('home_id', $this->mailData->property_id)->where('type', 'image')->first();
        return new Content(
            view: 'emails.booking-cancellation',
            with: [
                'data' => $this->mailData,
                'prorpertyAssetsDetail'  => $prorpertyAssetsDetail
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
