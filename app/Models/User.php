<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'url',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /*RelAación 1:n Usuario-Recetas*/
    /*Un usuario pouede tener multiples recetas*/
    public function userRecetas(){
        return $this->hasMany(Receta::class);
    } 

    //Evento usuario-perfil
    protected static function booted(){
        parent::booted();

        static::created(function($user){
            $user->userPerfil()->create();

        });
    }

    //relación 1:1 usuario-perfil
    public function userPerfil(){
        return $this->hasOne(Perfil::class);
    }
}
