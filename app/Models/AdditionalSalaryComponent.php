<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdditionalSalaryComponent extends Model
{
    protected $fillable = [
        'user_id',
        'month',
        'month_bs',
        'title',
        'amount',
        'type',
        'remarks',
        'is_taxable'
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
