<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactInquiry extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone', 'service_id', 'subject', 'message'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
