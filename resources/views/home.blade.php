@inject('HOME','App\Http\Controllers\HomeController')

@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')

@section('js')
    
<script>
am5.ready(function() {
    
    // Create root element
    // https://www.amcharts.com/docs/v5/getting-started/#Root_element
    var root = am5.Root.new("chartdiv");
    
    
    // Set themes
    // https://www.amcharts.com/docs/v5/concepts/themes/
    root.setThemes([
      am5themes_Animated.new(root)
    ]);
    
    
    // Create chart
    // https://www.amcharts.com/docs/v5/charts/xy-chart/
    var chart = root.container.children.push(am5xy.XYChart.new(root, {
      panX: false,
      panY: false,
      wheelX: "panX",
      wheelY: "zoomX",
      layout: root.verticalLayout
    }));
    
    
    // Add legend
    // https://www.amcharts.com/docs/v5/charts/xy-chart/legend-xy-series/
    var legend = chart.children.push(
      am5.Legend.new(root, {
        centerX: am5.p50,
        x: am5.p50
      })
    );

    var data = [
        <?php foreach ($data as $key => $val) { ?>
                {
                    "day": <?php echo $val['day']; ?>,
                    "Penjualan": <?php echo $val['sales']; ?>
                },
        <?php } ?>
    ] ;
    // for(let i = 1; i < 31; i++){
    //      a = {
    //     "day" : i,
    //     "Penjualan" : i,
    //     "Pembelian" : i
    //     };
        
    //     data.push(a);
    // }
    
    // Create axes
    // https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
    var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
      categoryField: "day",
      renderer: am5xy.AxisRendererX.new(root, {
        cellStartLocation: 0.1,
        cellEndLocation: 0.9
      }),
      tooltip: am5.Tooltip.new(root, {})
    }));
    
    xAxis.data.setAll(data);
    
    var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
      renderer: am5xy.AxisRendererY.new(root, {})
    }));
    
    
    // Add series
    // https://www.amcharts.com/docs/v5/charts/xy-chart/series/
    function makeSeries(name, fieldName) {
      var series = chart.series.push(am5xy.ColumnSeries.new(root, {
        name: name,
        xAxis: xAxis,
        yAxis: yAxis,
        valueYField: fieldName,
        categoryXField: "day",
      }));
    
      series.columns.template.setAll({
        tooltipText: "{name}, tgl {categoryX} (Rp. {valueY})",
        width: am5.percent(90),
        tooltipY: 0
      });
      chart.get("colors").set("colors", [
        am5.color(0x6794dc),
        am5.color(0xc767dc),
      ]);
    
      series.data.setAll(data);
    
      // Make stuff animate on load
      // https://www.amcharts.com/docs/v5/concepts/animations/
      series.appear();
    
      series.bullets.push(function () {
        return am5.Bullet.new(root, {
          locationY: 0,
          sprite: am5.Label.new(root, {
            text: "{valueY}",
            fill: root.interfaceColors.get("alternativeText"),
            centerY: 0,
            centerX: am5.p50,
            populateText: true
          })
        });
      });
    
      legend.data.push(series);
    }
    
    makeSeries("Penjualan", "Penjualan");

    series.columns.template.states.create("Penjualan", {
        fill: am5.color(0x76b041),
        stroke: am5.color(0x76b041)
    });

    
    // Make stuff animate on load
    // https://www.amcharts.com/docs/v5/concepts/animations/
    // chart.appear(5000, 500);

});



am5.ready(function() {

    // Create root element
    // https://www.amcharts.com/docs/v5/getting-started/#Root_element
    var root = am5.Root.new("chartpie");


    // Set themes
    // https://www.amcharts.com/docs/v5/concepts/themes/
    root.setThemes([
    am5themes_Animated.new(root)
    ]);


    // Create chart
    // https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/
    var chart = root.container.children.push(am5percent.PieChart.new(root, {
    layout: root.verticalLayout
    }));
    

    // Create series
    // https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/#Series
    var series = chart.series.push(am5percent.PieSeries.new(root, {
    valueField: "value",
    categoryField: "category"
    }));

    // Set data
    // https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/#Setting_data
    series.data.setAll([
        <?php foreach ($item_data as $key => $val) { ?>
            <?php if($val['quantity'] > 0) {?>
                { value: <?php echo $val['quantity'] ?>, category: "<?php echo $val['item_name'] ?>" },
                <?php }?>
        <?php } ?>
    ]);
    
    series.labels.template.set("visible", false);
    series.ticks.template.set("visible", false);

    // Create legend
    // https://www.amcharts.com/docs/v5/charts/percent-charts/legend-percent-series/
    // var legend = chart.children.push(am5.Legend.new(root, {
    // centerX: am5.percent(50),
    // x: am5.percent(50),
    // marginTop: 15,
    // marginBottom: 15
    // }));

    // legend.data.setAll(series.dataItems);


    // Play initial series animation
    // https://www.amcharts.com/docs/v5/concepts/animations/#Animation_of_series
    series.appear(1000, 100);

}); // end am5.ready()


am5.ready(function() {
    
    // Create root element
    // https://www.amcharts.com/docs/v5/getting-started/#Root_element
    var root = am5.Root.new("chartday");
    
    
    // Set themes
    // https://www.amcharts.com/docs/v5/concepts/themes/
    root.setThemes([
      am5themes_Animated.new(root)
    ]);
    
    
    // Create chart
    // https://www.amcharts.com/docs/v5/charts/xy-chart/
    var chart = root.container.children.push(am5xy.XYChart.new(root, {
      panX: false,
      panY: false,
      wheelX: "panX",
      wheelY: "zoomX",
      layout: root.verticalLayout
    }));
    
    
    // Add legend
    // https://www.amcharts.com/docs/v5/charts/xy-chart/legend-xy-series/
    var legend = chart.children.push(
      am5.Legend.new(root, {
        centerX: am5.p50,
        x: am5.p50
      })
    );

    var data = [
    <?php foreach ($datasalesinvoiceweekly as $val) { ?>  
        {
            "day" : "<?php echo $val['day']; ?>",
            "Penjualan" : <?php echo $val['sales']; ?>,
        },
    <?php } ?> 
    ] ;
    // for(let i = 1; i < 31; i++){
    //      a = {
    //     "day" : i,
    //     "Penjualan" : i,
    //     "Pembelian" : i
    //     };
        
    //     data.push(a);
    // }
    
    // Create axes
    // https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
    var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
      categoryField: "day",
      renderer: am5xy.AxisRendererX.new(root, {
        cellStartLocation: 0.1,
        cellEndLocation: 0.9
      }),
      tooltip: am5.Tooltip.new(root, {})
    }));
    
    xAxis.data.setAll(data);
    
    var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
      renderer: am5xy.AxisRendererY.new(root, {})
    }));
    
    
    // Add series
    // https://www.amcharts.com/docs/v5/charts/xy-chart/series/
    function makeSeries(name, fieldName) {
      var series = chart.series.push(am5xy.ColumnSeries.new(root, {
        name: name,
        xAxis: xAxis,
        yAxis: yAxis,
        valueYField: fieldName,
        categoryXField: "day",
      }));
    
      series.columns.template.setAll({
        tooltipText: "{name}, Hari {categoryX} (Rp. {valueY})",
        width: am5.percent(90),
        tooltipY: 0
      });
      chart.get("colors").set("colors", [
        am5.color(0x6794dc),
        am5.color(0xc767dc),
      ]);
    
      series.data.setAll(data);
    
      // Make stuff animate on load
      // https://www.amcharts.com/docs/v5/concepts/animations/
      series.appear();
    
      series.bullets.push(function () {
        return am5.Bullet.new(root, {
          locationY: 0,
          sprite: am5.Label.new(root, {
            text: "{valueY}",
            fill: root.interfaceColors.get("alternativeText"),
            centerY: 0,
            centerX: am5.p50,
            populateText: true
          })
        });
      });
    
      legend.data.push(series);
    }
    
    makeSeries("Penjualan", "Penjualan");

    series.columns.template.states.create("Penjualan", {
        fill: am5.color(0x76b041),
        stroke: am5.color(0x76b041)
    });

    
    // Make stuff animate on load
    // https://www.amcharts.com/docs/v5/concepts/animations/
    // chart.appear(5000, 500);

});

// $('body').append('<div style="" id="loadingDiv"><div class="loader">Loading...</div></div>');
// $(window).on('load', function(){
//   setTimeout(removeLoader, 2000); //wait for page load PLUS two seconds.
// });
// function removeLoader(){
//     $( "#loadingDiv" ).fadeOut(500, function() {
//       // fadeOut complete. Remove the loading div
//       $( "#loadingDiv" ).remove(); //makes page more lightweight 
//   });  
// }

// $('body').append('<div class="modal" id="modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="staticBackdropLabel">Modal title</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body">...</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="button" class="btn btn-primary">Understood</button></div></div></div></div>');

// $(document).ready(function(){
//     $('#modal').modal('show');
// });
</script>

@stop

@section('content')
<br>

<div class="row">
    <div class="col-md-12">
        <div class="card border border-dark">
            <div class="card-header border-dark bg-dark">
                <h5 class="mb-0 float-left">
                    <?php
                    $month = [
                        1 => 'Januari',
                        2 => 'Februari',
                        3 => 'Maret',
                        4 => 'April',
                        5 => 'Mei',
                        6 => 'Juni',
                        7 => 'Juli',
                        8 => 'Agustus',
                        9 => 'September',
                        10 => 'Oktober',
                        11 => 'November',
                        12 => 'Desember'
                    ];
                    ?>
                    Grafik Penjualan Bulan {{ $month[date('n')] }}
                </h5>
            </div>
        
            <div class="card-body">
               <div style="width: 100%; height: 450px;" id="chartdiv"></div>
            </div>
        </div>
    </div>   
    <div class="col-md-6">
        <div class="card border border-dark">
            <div class="card-header border-dark bg-dark">
                <h5 class="mb-0 float-left">
                    <?php
                    $month = [
                        1 => 'Januari',
                        2 => 'Februari',
                        3 => 'Maret',
                        4 => 'April',
                        5 => 'Mei',
                        6 => 'Juni',
                        7 => 'Juli',
                        8 => 'Agustus',
                        9 => 'September',
                        10 => 'Oktober',
                        11 => 'November',
                        12 => 'Desember'
                    ];
                    ?>
                    Grafik Penjualan Barang Bulan {{ $month[date('n')] }}
                </h5>
            </div>
        
            <div class="card-body">
               <div style="width: 100%; height: 450px;" id="chartpie"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border border-dark">
            <div class="card-header border-dark bg-dark">
                <h5 class="mb-0 float-left">
                    <?php
                    $dayname = [
                        'Monday'    => 'Senin',
                        'Tuesday'   => 'Selasa',
                        'Wednesday' => 'Rabu',
                        'Thursday'  => 'Kamis',
                        'Friday'    => 'Jumat',
                        'Saturday'  => 'Sabtu',
                        'Sunday'    => 'Minggu',
                    ];
                    ?>
                    <?php 
                    $x            = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
                    $day          = date("l", $x); 
                    ?>
                    Grafik Penjualan Mingguan : <?php echo $dayname[$day].", ".date('d-m-Y'); ?>
                </h5>
            </div>
        
            <div class="card-body">
               <div style="width: 100%; height: 450px;" id="chartday"></div>
            </div>
        </div>
    </div>
</div>
</div>


@stop

@section('css')
    
@stop

@section('js')
    
@stop