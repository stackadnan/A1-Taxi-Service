<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingUpdated
{
    use Dispatchable, SerializesModels;

    public $booking;
    public $updatedBy;
    public $changes;

    /**
     * Create a new event instance.
     */
    public function __construct(Booking $booking, $updatedBy = null, array $changes = [])
    {
        $this->booking = $booking;
        $this->updatedBy = $updatedBy;
        $this->changes = $changes;
    }
}
