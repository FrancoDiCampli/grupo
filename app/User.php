<?php

namespace App;

use App\Role;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'foto', 'cliente_id', 'distributor_id', 'code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    public function cliente()
    {
        return $this->belongsTo('App\Cliente');
    }

    public function facturas()
    {
        return $this->hasMany('App\Factura');
    }

    public function ventas()
    {
        return $this->hasMany('App\Venta');
    }

    public function presupuestos()
    {
        return $this->hasMany('App\Presupuesto');
    }

    public function negocio()
    {
        return $this->belongsTo('App\Negocio');
    }

    public function distributor()
    {
        return $this->belongsTo('App\Distributor');
    }

    public function clientes()
    {
        return $this->hasMany('App\Cliente');
    }
}
