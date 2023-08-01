<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformationRecord extends Model
{
    use HasFactory;

    protected $table = "information_records";

    protected $primaryKey = 'id';

    protected $fillable = ['name', 'amount'];

    protected $dates = ['created_at', 'updated_at'];
}
