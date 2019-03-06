<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Venta;
use Illuminate\Support\Collection;
use App\Producto;

class ReporteVentasController extends Controller
{
    public function reporteVentas()
    {

        $compradores = Venta::join('clientes', 'clientes.id', '=', 'ventas.id_cliente')
            ->selectRaw("sum(total) as a, clientes.nombre as y")
            ->groupBy('id_cliente')
            ->orderBy('a', 'desc')
            ->limit(10)
            ->get();

        $vendedores = Venta::join('users', 'users.id', '=', 'ventas.id_vendedor')
            ->selectRaw("sum(total) as a, users.name as y")
            ->groupBy('id_vendedor')
            ->orderBy('a', 'desc')
            ->limit(10)
            ->get();
        $total = Producto::selectRaw("sum(ventas) as total")->first()->total;
        $productos = $this->showProducts();
        return view('admin.ventas.reportes-ventas', compact("compradores", "vendedores", "productos", "total"));
    }

    public function showVentas(Request $request)
    {

        $data = Venta::selectRaw('sum(total) as ventas, DATE_FORMAT(created_at, "%Y-%m-%d") as y');

        if ($request->get('fechaInicio') && $request->get('fechaFin')) {
            $fi = \Carbon\Carbon::parse($request->get('fechaInicio'))->toDateString();
            $ff = \Carbon\Carbon::parse($request->get('fechaFin'))->toDateString();

            if ($fi === $ff) {
                $data = Venta::selectRaw('sum(total) as ventas, DATE_FORMAT(created_at, "%Y-%m-%d %HH") as y');
            }

            $data = $data->whereDate("created_at", ">=", $fi . " 00:00:00")->whereDate("created_at", "<=", $ff . " 11:59:59");
        }

        return json_encode($data->groupBy("y")->get());
    }

    public function showProducts()
    {
        $productos = Producto::selectRaw("nombre as label, ventas as value")->orderBy("value", "DESC")->limit(10)->get();
        //Colores Originales
        /*
        $color =     ["#f56954", "#00a65a", "#f39c12", "#00c0ef", "#3c8dbc", "#d2d6de"];
        $highlight = ["#f56954", "#00a65a", "#f39c12", "#00c0ef", "#3c8dbc", "#d2d6de"];
        */
        //Colores Nuevos
        $color =     ["#f44336", "#4caf50", "#ffc107", "#03a9f4", "#795548", "#673ab7", "#aed581", "#7986cb", "#ffcc80", "#b2dfdb"];
        $highlight = ["#b71c1c", "#2e7d32", "#ff8f00", "#0277bd", "#4e342e", "#4527a0", "#8bc34a", "#3f51b5", "#ff9800", "#26a69a"];
        $i=0;
        foreach ($productos as $producto) {
            $producto->color = $color[$i];
            $producto->highlight = $highlight[$i];
            $i++;
        }
        return $productos;
    }
}
