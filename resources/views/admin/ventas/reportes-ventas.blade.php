@extends('admin.layout')

@push("css")
<link rel="stylesheet" href="/css/reportes/index.css">
@endpush
@section('header')
    <h1>
        Reportes de Ventas
        <small>Reportes</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Reportes</a></li>
        <li class="active">Administrar</li>
    </ol>
@stop

@section('contenido')
    <div class="box box-primary">
        <div class="box-header">
            <div class="form-group">
                <button type="button" class="btn btn-default pull-right" id="daterange-btn2">
                    <span><i class="fa fa-calendar"></i> Rango de Fecha</span>
                    <i class="fa fa-caret-down"></i>
                </button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-solid bg-teal-gradient">
                        <div class="box-header">
                            <i class="fa fa-th">
                                <h3 class="box-title">Gráfico de Ventas</h3>
                            </i>
                        </div>
                        <div class="box-body border-radius-none nuevoGraficoVentas" id="registroVentas">
                            <div class="chart grafica" id="line-chart-ventas" style="height: 250px;"></div>
                            <p class="txtNoRegistro" style="color: inherit;">No hay registros...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">Productos más vendidos</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body" class="grafica">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="chart-responsive">
                                <canvas id="pieChart" height="150"></canvas>
                            </div>
                            <!-- ./chart-responsive -->
                        </div>
                        <!-- /.col -->
                        <div class="col-md-4">
                            <ul class="chart-legend clearfix">
                                @php
                                    foreach ($productos as $producto) {
                                        $name = substr($producto->label, 0, 15);
                                        if (strlen($producto->label)>15){
                                            $name .= "...";
                                        }
                                        echo "<li title=\"{$producto->label}\"><i class=\"fa fa-circle-o\" style=\"color:{$producto->color};\"></i>{$name}</li>";
                                    }
                                @endphp
                            </ul>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.box-body -->
                <div class="box-footer no-padding grafica--data">
                    <ul class="nav nav-pills nav-stacked">
                        @php
                        for ($i = 0; ($i<5 && $i<count($productos)); $i++){
                            echo "<li><a href=\"#\">{$productos[$i]->label}
                                <span class=\"pull-right\" style='color: {$productos[$i]->highlight};'><i class=\"fa fa-angle-down\"></i> ".ceil((($productos[$i]->value)*100)/$total)."%</span></a></li>";
                        }
                        @endphp
                    </ul>
                </div>
                <!-- /.footer -->
                <p class="txtNoRegistro">No hay registros...</p>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Vendedores</h3>
                </div>
                <div class="box-body chart-responsive {{ isset($vendedores[0])?"":"sinDatos" }}" id="graficaVendedores">
                    <div class="chart grafica" id="bar-chart-vendedores" style="height: 300px;"></div>
                    <p class="txtNoRegistro">No hay registros...</p>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
        <div class="col-xs-6">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Compradores</h3>
                </div>
                <div class="box-body chart-responsive {{ isset($compradores[0])?"":"sinDatos" }}" id="graficaCompradores">
                    <div class="chart grafica" id="bar-chart-compradores" style="height: 300px;"></div>
                    <p class="txtNoRegistro">No hay registros...</p>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div>
@stop
@section('graficas')
    <script>
    const lang = "es";
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': "{{csrf_token()}}"
        }
    });
    </script>
    <script src="/js/storage.js"></script>
    <script src="/js/daterangepicker.js"></script>
    <script>

    var lineChar =new Morris.Line({
            element: 'line-chart-ventas',
            resize: true,
            data: [],
            xkey: 'y',
            ykeys: ['ventas'],
            labels: ['ventas'],
            lineColors: ['#efefef'],
            lineWidth: 2,
            hideHover: 'auto',
            gridTextColor: '#fff',
            gridStrokeWidth: 0.4,
            pointSize: 4,
            pointStrokeColors: ['#efefef'],
            gridLineColor: '#efefef',
            gridTextFamily: 'Open Sans',
            preUnits: '$',
            gridTextSize: 10
        });
    function init(){
        let nameDate = "daterange-btn2";
        let dataVentas = new Object();

        function getDatos(){
            const url = "{{ route("grafica-ventas") }}";
            const success = function (data){
                if (data && data.length > 0){
                    lineChar.setData(data);
                    registroVentas.classList.remove("sinDatos");
                }else{
                    registroVentas.classList.add("sinDatos");
                }
            }
            $.post(
                url,
                dataVentas,
                success,
                "json"
            );
        }
        getDatos();

        let date = initDateRange(nameDate, function (fechaInicio, fechaFin, label){
            dataVentas.fechaInicio = fechaInicio.format("YYYY/MM/DD");
            dataVentas.fechaFin = fechaFin.format("YYYY/MM/DD");
            getDatos();
        });
    }
    init();
    </script>
    <script>

        var vendedores = new Morris.Bar({
            element: 'bar-chart-vendedores',
            resize: true,
            data: {!! json_encode($vendedores) !!},
            barColors: ['#00a65a', '#f56954'],
            xkey: 'y',
            ykeys: ['a'],
            labels: ['Ventas'],
            preUnits: '$',
            hideHover: 'auto'
        });

        var compradores = new Morris.Bar({
            element: 'bar-chart-compradores',
            resize: true,
            data: {!! json_encode($compradores) !!},
            barColors: ['#E1493F'],
            xkey: 'y',
            ykeys: ['a'],
            labels: ['Ventas'],
            preUnits: '$',
            hideHover: 'auto'
        });

        //-------------
        //- PIE CHART -
        //-------------
        // Get context with jQuery - using jQuery's .get() method.
        var pieChartCanvas = $("#pieChart").get(0).getContext("2d");
        var pieChart = new Chart(pieChartCanvas);
        var PieData = {!! json_encode($productos) !!};
        var pieOptions = {
            //Boolean - Whether we should show a stroke on each segment
            segmentShowStroke: true,
            //String - The colour of each segment stroke
            segmentStrokeColor: "#fff",
            //Number - The width of each segment stroke
            segmentStrokeWidth: 1,
            //Number - The percentage of the chart that we cut out of the middle
            percentageInnerCutout: 50, // This is 0 for Pie charts
            //Number - Amount of animation steps
            animationSteps: 100,
            //String - Animation easing effect
            animationEasing: "easeOutBounce",
            //Boolean - Whether we animate the rotation of the Doughnut
            animateRotate: true,
            //Boolean - Whether we animate scaling the Doughnut from the centre
            animateScale: false,
            //Boolean - whether to make the chart responsive to window resizing
            responsive: true,
            // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
            maintainAspectRatio: false,
            //String - A legend template
            legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=value%> - <%=label%>"
  };
  pieChart.Doughnut(PieData, pieOptions);


    </script>
@endsection
