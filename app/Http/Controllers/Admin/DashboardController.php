<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Producto;

class DashboardController extends Controller
{
    public function index(){
        $productos = $this->showProducts();
        $total = Producto::selectRaw("sum(ventas) as total")->first()->total;
        return view('admin.dashboard', compact("productos", "total"));
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
