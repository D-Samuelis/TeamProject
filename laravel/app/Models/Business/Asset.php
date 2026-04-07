<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

use App\Models\Auth\User;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'delete_after',
        'branch_id',
        ];


    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function rules()
    {
        return $this->hasMany(Rule::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
