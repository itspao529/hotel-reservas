<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['nombre', 'email', 'password', 'rol', 'telefono'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime', 'password' => 'hashed'];

    public function reservas() { return $this->hasMany(Reserva::class, 'cliente_id'); }
    public function valoraciones() { return $this->hasMany(Valoracion::class, 'cliente_id'); }
    public function scopeClientes($query) { return $query->where('rol', 'cliente'); }
    public function scopeAdmins($query) { return $query->where('rol', 'admin'); }
    public function esAdmin(): bool { return $this->rol === 'admin'; }
    public function esCliente(): bool { return $this->rol === 'cliente'; }
}
