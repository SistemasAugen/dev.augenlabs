<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profitability extends Model {
    protected $table = 'profitabilities';
    protected $fillable = [
        'branch_id',
        'estimated_at',
        'base',
        'amount'    
    ];
}
