<?php

namespace App\Events;

use App\Models\Booking;
use App\Models\Driver;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverResponseUpdated
{
    use Dispatchable, SerializesModels;

    public $booking;
    public $driver;
    public $response;
    public $responseAt;

    /**
     * Create a new event instance.
     */
    public function __construct(Booking $booking, Driver $driver, string $response, $responseAt = null)
    {
        $this->booking = $booking;
        $this->driver = $driver;
        $this->response = $response;
        $this->responseAt = $responseAt ?? now();
    }
}