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
        'delete_after'
        ];


    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class);
    }

    public function rules()
    {
        return $this->hasMany(Rule::class);
    }
}
