<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\UploadedFile;

class PhotoUploadedEvent
{
    use Dispatchable, SerializesModels;

    public $file;

    /**
     * Create a new event instance.
     *
     * @param UploadedFile $file
     */
    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
    }
}
