<?php
    
    //ej. http://medialabmx.org/covid-19/geojsonMunicipios.php?edo=1
    $servername = "mysql.medialabmx.org";
    $username = "medialabmxorg";
    $password = "b6wHWtm9";
    $dbname = "covid19mex";

    $conn = mysqli_connect($servername, $username, $password , $dbname);
    $edo = $_GET["edo"]; 
    $cont_mun=$_GET["contmunicipio"]; 
    $max_mun = $cont_mun+50; 
   
    mysqli_set_charset($conn,"utf8");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    $sql = "SELECT nombre, ambulatorios, hospitalizados, defunciones, negativos, pendientes, ST_AsGeoJSON(area) AS geom FROM municipios WHERE clave_entidad =".$edo." AND clave_mun>".$cont_mun." AND clave_mun<=".$max_mun; 
    $result = $conn->query($sql); 
     
    if ($result->num_rows > 0) { 
      $geojson = array( 'type' => 'FeatureCollection', 'features' => array());
      while($row = $result->fetch_assoc()) { 
        //echo $row['relato'];
          //$point_value = $row['ubicacion'];

          //$coordinates = unpack('x/x/x/x/corder/Ltype/dlat/dlon', $point_value);
         setlocale(LC_ALL,"es_MX.UTF-8");

         //echo utf8_decode($row['nombre']).'<br>';
         $collection = $row['geom'];
         $total = (int)$row['defunciones']+(int)$row['hospitalizados']+(int)$row['ambulatorios'];
        
         // echo '<br>';
       
          
          $marker = array(
            'type' => 'Feature',
            'properties' => array(
            'nombre' => utf8_decode($row['nombre']),
            'ambulatorios' => (int)$row['ambulatorios'],
            'hospitalizados' => (int)$row['hospitalizados'],
            'defunciones' => (int)$row['defunciones'], 
            'negativos' => (int)$row['negativos'],
            'pendientes' =>  (int)$row['pendientes'],
            'total' => $total
          ),'geometry' => json_decode($collection)
        );
        array_push($geojson['features'], $marker);
         
                
       }
       
      header('Content-type: application/json; charset=utf-8');
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
?>