<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'product_id',
        'student_id',
        'quantity',
        'total_price',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}