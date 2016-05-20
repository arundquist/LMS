<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type='text/javascript'>
      google.charts.load('current', {'packages':['annotationchart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('date', 'Date');
        @foreach ($assignments AS $id=>$name)
          data.addColumn('number', '{{addslashes($name)}}');
          data.addColumn('string', 'title');
          data.addColumn('string', 'text');
        @endforeach
        data.addRows([
          @foreach($strings AS $string)
            {{$string}}
           @endforeach
        ]);

        var chart = new google.visualization.AnnotationChart(document.getElementById('chart_div'));

        var options = {
          displayAnnotations: true,
          min: 0,
          max: 4.5
        };

        chart.draw(data, options);
      }
    </script>
  </head>

  <body>
    <div id='chart_div' style='width: 900px; height: 500px;'></div>
  </body>
</html>