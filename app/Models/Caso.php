<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Caso extends Model
{
    use HasFactory;

    protected $table = 'casos';

    protected $fillable = [
        'id',
        'dni',
        'nombre_completo',
        'genero',
        'telefono',
        'nacionalidad',
        'direccion',
        'departamento',
        'provincia',
        'distrito',
        'lugar_caso',
        'descripcion',
        'autorizacion_comunicacion',
        'autorizacion_copia_reporte',
        'fecha_resolucion',
        'fecha_atencion',
        'asignado',
        'resolucion',
        'resolucion_url',
        'tipo_caso_id',
        'estado_id',
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id', 'id');
    }

    public function tipo_caso()
    {
        return $this->belongsTo(TipoCaso::class, 'tipo_caso_id', 'id');
    }

    public static function getCasosPorMes(): array
    {
        $thisYear = Carbon::now()->year;
        $resultados = DB::table('casos')
            ->select(
                DB::raw('MONTH(created_at) as mes'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', $thisYear)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $nombresMeses = ['E', 'F', 'M', 'A', 'M', 'J', 'J', 'A', 'S', 'O', 'N', 'D'];
        $data = [];

        foreach ($resultados as $item) {
            $data[] = [
                'mes' => $nombresMeses[$item->mes - 1],
                'casos' => $item->total
            ];
        }

        return $data;
    }

    public static function getEstadisticasEstados(): array
    {
        $estados = self::select('estado_id', DB::raw('COUNT(*) as count'))
            ->groupBy('estado_id')
            ->pluck('count', 'estado_id')
            ->toArray();

        return [
            [
                'nombre' => 'Recibido',
                'valor' => $estados['1'] ?? 0,
                'color' => '#FBBF24'
            ],
            [
                'nombre' => 'Atendido',
                'valor' => $estados['3'] ?? 0,
                'color' => '#9CA3AF'
            ],
            [
                'nombre' => 'Resuelto',
                'valor' => $estados['4'] ?? 0,
                'color' => '#EF4444'
            ],
        ];
    }

    public static function getUltimaActualizacion(): ?string
    {
        $ultimo = self::orderByDesc('updated_at')->first();

        return $ultimo?->updated_at?->toDateTimeString();
    }
}
