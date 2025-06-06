<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function settings()
    {
        return $this->hasOne(UserSetting::class);
    }

    public function assets()
    {
        return $this->hasMany(\App\Models\Asset::class);
    }

    /**
     * | Add User Details
     */
    public function addUser($req)
    {
        $mUser = new User();
        $mUser->create($req);
    }

    /**
     * | Get User by Email
     */
    public function getUserByEmail($email)
    {
        return User::where('email', $email)
            ->first();
    }

    /**
     * | Get User by Id
     */
    public function getUserDetails($id)
    {
        return User::select(
            '*',
            DB::raw("CONCAT('" . config('app.url').'/' . "', id_document_path) AS id_document_path")
        )
            ->where('id', $id)
            ->first();
    }

    /**
     * | Edit User Details
     */
    public function editUser($req)
    {
        $mUser = User::findorfail($req['id']);
        $mUser->update($req);
    }

    public function location()
    {
        return $this->hasOne(UserLocation::class);
    }
}
