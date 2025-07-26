<?php
    
    require '../functions.php';
    $conexion = conexion($bd_config);
    //consultar institutos (ordenadas por consulta)
    $statement_inst_first = $conexion->prepare('SELECT * FROM institucion WHERE nombre = "universidad catolica del norte" OR nombre="universidad de tarapaca"');
    $statement_inst_first -> execute();
    $resultado_inst_first = $statement_inst_first->fetchAll();
    $statement_inst_second = $conexion->prepare('SELECT * FROM institucion WHERE nombre LIKE "universidad%" AND nombre!="universidad de tarapaca" AND nombre != "universidad catolica del norte" ORDER BY nombre');
    $statement_inst_second -> execute();
    $resultado_inst_second = $statement_inst_second->fetchAll();
    $statement_inst_third = $conexion->prepare('SELECT * FROM institucion WHERE nombre NOT LIKE "universidad%" AND nombre!="universidad de tarapaca" AND nombre != "universidad catolica del norte" ORDER BY nombre');
    $statement_inst_third -> execute();
    $resultado_inst_third = $statement_inst_third->fetchAll();
   
   
    //consultar titulo licenciatu
    $statement_lic = $conexion->prepare('SELECT * FROM titulo_grado WHERE tipo_grado = 1 ORDER BY nombre');
    $statement_lic -> execute();
    $resultado_lic = $statement_lic->fetchAll();
   
    //consultar titulo universitario
    $statement_un = $conexion->prepare('SELECT * FROM titulo_grado WHERE tipo_grado = 2 ORDER BY nombre');
    $statement_un -> execute();
    $resultado_un = $statement_un->fetchAll();
   
    //consultar titulo magister
    $statement_mag = $conexion->prepare('SELECT * FROM titulo_grado WHERE tipo_grado = 3 ORDER BY nombre');
    $statement_mag -> execute();
    $resultado_mag = $statement_mag->fetchAll();
   
    //consultar titulo doctorado
    $statement_doc = $conexion->prepare('SELECT * FROM titulo_grado WHERE tipo_grado = 4 ORDER BY nombre');
    $statement_doc -> execute();
    $resultado_doc = $statement_doc->fetchAll();
   
    //consultar pueblos
    $statement_pue = $conexion->prepare('SELECT * FROM pueblo ORDER BY nombre');
    $statement_pue -> execute();
    $resultado_pue = $statement_pue->fetchAll();
   
   //consultar paises Argentina | Brasil | Bolivia | Chile | Colombia | Ecuador | Paraguay | Perú | Uruguay | Venezuela
   $statement_pais_1 = $conexion->prepare('SELECT * FROM pais WHERE id_pais=13 OR id_pais=76 OR id_pais=46 OR id_pais=52 OR id_pais=66 OR id_pais=172 OR id_pais=173 OR id_pais=229 OR id_pais=33');
   $statement_pais_1 -> execute();
   $resultado_pais_1 = $statement_pais_1->fetchAll();
   $statement_pais_2 = $conexion->prepare('SELECT * FROM pais WHERE id_pais!=13 OR id_pais!=76 OR id_pais!=46 OR id_pais!=52 OR id_pais!=66 OR id_pais!=172 OR id_pais!=173 OR id_pais!=229 OR id_pais!=33');
   $statement_pais_2 -> execute();
   $resultado_pais_2 = $statement_pais_2->fetchAll();
    $busqueda = $_POST['lista'];
    
    if(isset($busqueda)){
        if($busqueda == 'pueb'){
            
            echo json_encode($resultado_pue, JSON_UNESCAPED_UNICODE);
        }
        if($busqueda == 'pais'){
            $paises = array_merge($resultado_pais_1,$resultado_pais_2);
            echo json_encode($paises, JSON_UNESCAPED_UNICODE);
        }
        if($busqueda == 'inst'){
            $institutos = array_merge_recursive($resultado_inst_first,$resultado_inst_second,$resultado_inst_third);
            echo json_encode($institutos, JSON_UNESCAPED_UNICODE);
        }
        if($busqueda == 'lic'){
            echo json_encode($resultado_lic, JSON_UNESCAPED_UNICODE);
        }
        if($busqueda == 'un'){
            echo json_encode($resultado_un, JSON_UNESCAPED_UNICODE);
        }
        if($busqueda == 'mag'){
            echo json_encode($resultado_mag, JSON_UNESCAPED_UNICODE);
        }
        if($busqueda == 'doc'){
            echo json_encode($resultado_doc, JSON_UNESCAPED_UNICODE);
        }
       
        
    }
    
    