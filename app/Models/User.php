<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\DB\Core\IntegerField;
use App\DB\Core\StringField;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Config;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

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

    public function saveableFields(): array
    {
        return [
            'role' => IntegerField::new(),
            'first_name'=>StringField::new(),
            'middle_name'=>StringField::new(),
            'last_name'=>StringField::new(),
            'email'=>StringField::new(),
            'phone_no'=>StringField::new(),
            'gender'=>StringField::new(),
            'password'=>IntegerField::new(),
            'dob' => StringField::new(),
        ];
    }

    public static function isAdmin()
    {
        $userid = auth()->user()->id;
        $userDetais = User::find($userid);
        if ($userDetais['role'] ===   Config::get('variables.ONE')) {
            return true;
        }
        return false;
    }

    public function books():HasMany
    {
        return $this->hasMany(Book::class);
    }

}
