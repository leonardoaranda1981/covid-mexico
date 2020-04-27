<?php
    
    //ej. http://medialabmx.org/covid-19/geojsonMunicipios.php?edo=1
    $servername = "mysql.medialabmx.org";
    $username = "medialabmxorg";
    $password = "b6wHWtm9";
    $dbname = "covid19mex";

    $conn = mysqli_connect($servername, $username, $password , $dbname);
   // $nombre_mun = $_POST['nombreMun'];
    $edo = $_POST["entidad"]; 
    mysqli_set_charset($conn,"utf8");


    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    $sql = "SELECT COUNT(*) AS cont FROM municipios WHERE clave_entidad =".$edo; 
    $result = $conn->query($sql); 
     
    if ($result->num_rows > 0) { 
      $geojson = array( 'type' => 'FeatureCollection', 'features' => array());
      while($row = $result->fetch_assoc()) { 
       
          echo   $row['cont'];   
       }
            
    } else { 
        echo "0";
    }
   
    $conn = NULL;
?>