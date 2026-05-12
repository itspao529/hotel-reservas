<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Habitacion extends Model {
    use HasFactory, SoftDeletes;
    protected $table = 'habitaciones';
    protected $fillable = ['numero', 'tipo_id', 'precio_noche', 'capacidad', 'descripcion', 'foto', 'estado'];
    protected $casts = ['precio_noche' => 'decimal:2', 'capacidad' => 'integer'];

    public function tipo() { return $this->belongsTo(TipoHabitacion::class, 'tipo_id'); }
    public function reservas() { return $this->hasMany(Reserva::class, 'habitacion_id'); }
    public function valoraciones() { return $this->hasMany(Valoracion::class, 'habitacion_id'); }

    public function scopeDisponibles($query) { return $query->where('estado', 'disponible'); }
    public function scopePorTipo($query, $tipoId) { return $query->where('tipo_id', $tipoId); }
    public function scopeDisponibleEnFechas($query, $fechaInicio, $fechaFin) {
        return $query->where('estado', 'disponible')
            ->whereDoesntHave('reservas', function ($q) use ($fechaInicio, $fechaFin) {
                $q->whereIn('estado', ['pendiente', 'confirmada'])
                  ->where(function ($q2) use ($fechaInicio, $fechaFin) {
                      $q2->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                         ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                         ->orWhere(function ($q3) use ($fechaInicio, $fechaFin) {
                             $q3->where('fecha_inicio', '<=', $fechaInicio)
                                ->where('fecha_fin', '>=', $fechaFin);
                         });
                  });
            });
    }
    public function getPromedioValoracionAttribute(): float {
        return round($this->valoraciones()->avg('puntuacion') ?? 0, 1);
    }
}
