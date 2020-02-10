<?php

namespace App\Http\Controllers\API;

use App\Cheque;
use App\Articulo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');
    }

    public function getNotifications()
    {

        $notifications = collect();

        // Notificaciones de Productos por stock
        $productosStock = Articulo::all();
        foreach ($productosStock as $proStock) {
            $stock = $proStock->inventarios->sum('cantidad');
            if ($stock <= 0) {
                $notifications->push([
                    'id' => $proStock->id,
                    'type' => 'producto',
                    'icon' => 'fas fa-exclamation-circle',
                    'item' => 'El producto ' . $proStock->articulo,
                    'msg' => 'Necesita reposición',
                    'color' => 'error',
                    'url' => '/productos/show/' . $proStock->id
                ]);
            } elseif ($stock <= $proStock->stockminimo && $stock > 0) {
                $notifications->push([
                    'id' => $proStock->id,
                    'type' => 'producto',
                    'icon' => 'fas fa-box-open',
                    'item' => 'El producto ' . $proStock->articulo,
                    'msg' => 'No posee suficiente stock',
                    'color' => 'warning',
                    'url' => '/productos/show/' . $proStock->id
                ]);
            }
        }

        // Cheques
        $cheques = Cheque::all();
        foreach ($cheques as $cheque) {
            $hoy = Carbon::today()->format('d-m-Y');
            $fecha = new Carbon($cheque->fechacobro);
            $diff = $fecha->diffInDays($hoy);
            if ($diff == 1) {
                $notifications->push([
                    'id' => $cheque->id,
                    'type' => 'cheque',
                    'icon' => 'fas fa-user-clock',
                    'item' => 'El cheque Nº ' . $cheque->numero,
                    'msg' => 'Próximo a cobrar ' . $fecha->format('d-m-Y'),
                    'color' => 'success',
                    'url' => 'N/D',
                ]);
            }
        }

        return $notifications;
    }
}
