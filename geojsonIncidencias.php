<?php
    
    //http://medialabmx.org/covid-19/geojsonIncidencias.php?min=0&max=10
    $servername = "mysql.medialabmx.org";
    $username = "medialabmxorg";
    $password = "b6wHWtm9";
    $dbname = "covid19mex";

    $conn = mysqli_connect($servername, $username, $password , $dbname);
   // $edo = $_GET["edo"]; 

    $min = $_GET["min"]; 
    $max = $_GET["max"]; 
    mysqli_set_charset($conn,"utf8");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    $sql = "SELECT incidencias.tipo, incidencias.edad, incidencias.sexo, incidencias.embarazo, incidencias.diabetes, incidencias.epoc, incidencias.asma, incidencias.inmunosupresion, incidencias.hipertension, incidencias.cardiovascular, incidencias.obesidad, incidencias.renal, incidencias.tabaquismo, ST_AsText(ST_Centroid(municipios.area)) AS centro FROM incidencias LEFT JOIN municipios ON incidencias.clave_entidad = municipios.clave_entidad AND incidencias.clave_mun=municipios.clave_mun WHERE incidencias.tipo <> 'negativo' AND incidencias.id>".$min." AND incidencias.id<=".$max; 

    //$sql = "SELECT incidencias.tipo, incidencias.edad, incidencias.sexo, incidencias.embarazo, incidencias.diabetes, incidencias.epoc, incidencias.asma, incidencias.inmunosupresion, incidencias.hipertension, incidencias.cardiovascular, incidencias.obesidad, incidencias.renal, incidencias.tabaquismo, ST_AsGeoJSON(ST_Centroid(municipios.area)) AS centro FROM incidencias LEFT JOIN municipios ON incidencias.clave_entidad = municipios.clave_entidad AND incidencias.clave_mun=municipios.clave_mun WHERE incidencias.tipo = 'positivo_difunto'";
    
    $result = $conn->query($sql); 
     
    if ($result->num_rows > 0) { 
      $geojson = array( 'type' => 'FeatureCollection', 'features' => array());
      while($row = $result->fetch_assoc()) { 
        //echo $row['relato'];
          //$point_value = $row['ubicacion'];

          //$coordinates = unpack('x/x/x/x/corder/Ltype/dlat/dlon', $point_value);
         setlocale(LC_ALL,"es_MX.UTF-8");

        // echo $row['nombre'];
         $collection = $row['centro'];
         $collection = explode ('(', $collection);
         $collection = explode(')', $collection[1]);
         $coordinates = explode(' ', $collection[0]);
         $lon = (float)$coordinates[0]+(rand(-99,99)/1000);
         $lat = (float)$coordinates[1]+(rand(-99,99)/1000);
         $sexo = '';
         if($row['sexo'] == 1){
           $sexo = 'Mujer';
         }elseif ($row['sexo'] == 2) {
           $sexo = 'Hombre';
           # code...
         }elseif ($row['sexo'] == 99) {
           $sexo = 'Desconocido';
           # code...
         }

         $embarazo = checaEstatus($row['embarazo']);
         $diabetes = checaEstatus($row['diabetes']);
         $epoc = checaEstatus($row['epoc']);
         $asma = checaEstatus($row['asma']);
         $inmunosupresion = checaEstatus($row['inmunosupresion']);
         $hipertension = checaEstatus($row['hipertension']);
         $cardiovascular = checaEstatus($row['cardiovascular']);
         $obesidad = checaEstatus($row['obesidad']);
         $renal = checaEstatus($row['renal']);
         $tabaquismo = checaEstatus($row['tabaquismo']);
    
          $marker = array(
            'type' => 'Feature',
            'properties' => array(
            'icon' => $row['tipo'],
            'edad' => $row['edad'],
            'sexo' => $sexo,
            'embarazo' => $embarazo, 
            'diabetes' => $diabetes,
            'epoc' => $epoc,
            'asma' => $asma,
            'inmunosupresion' => $inmunosupresion,
            'hipertension' => $hipertension,
            'cardiovascular' => $cardiovascular,
            'obesidad' => $obesidad,
            'renal' => $renal,
            'tabaquismo' =>  $tabaquismo
          ),'geometry' => array(
            'type' => 'Point',
            'coordinates' => array(
              $lon,
              $lat
            )
            
          )
        );
        array_push($geojson['features'], $marker);
         
                
       }
      header('Content-type: application/json');
      header("Expires: 0");
      header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
      header("Cache-Control: no-store, no-cache, must-revalidate");
      header("Cache-Control: post-check=0, pre-check=0", false);
      header("Pragma: no-cache");
      echo json_encode($geojson, JSON_NUMERIC_CHECK);

            
    } else { 
        echo "0 results";
    }
   
    $conn = NULL;
    function checaEstatus($var1 ){
          /*
          1 SI 
          2 NO 
          97  NO APLICA
          98  SE IGNORA
          99  NO ESPECIFICADO
*/
      if($var1 == 1){
           return  'Sí';
         }elseif ($var1 == 2) {
           return  'No';
           # code...
         }elseif ($var1 == 97) {
           return  'No aplica';
           # code...
         }elseif ($var1 == 98) {
           return  'Se ignora';
           # code...
         }elseif ($var1 == 99) {
           return  'No especificado';
           # code...
         }


    }
?>