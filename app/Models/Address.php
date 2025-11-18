<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Address extends Model
{
    protected $guarded = [];

    /** @return MorphToMany<Admin> */
    public function admins(): MorphToMany
    {
        return $this->morphedByMany(Admin::class, 'addressable');
    }

    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'addressable');
    }
}
