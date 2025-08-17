<?php
    ob_start();
    if (strlen(session_id()) < 1){
        session_start();//Validamos si existe o no la sesión
    }
    if(!isset($_SESSION['admin']) && !isset($_SESSION['comite'])){
        header('Location:../index.php');
    }else
    {
    require('../form-doc/header.php');
?>
<div class="container mt-5">
    <div class="row m mb-5">
        <div class="col text-center">
            <h3 class=" mb-5">INTRANET DOCTORADO EN ANTROPOLOGÍA</h3>

        
   

        <div class="row justify-content-around">
            <div class="col-md-3 ">
                <button class="btn-inicio w-100 p-5" id="btn_estudiante"  ><i class=" fa-solid fa-user-graduate fs-1"></i><br><p class="fs-5 mt-3">ESTUDIANTES</p></button>
            </div>
            <div class="col-md-3"><button class=" btn-inicio w-100  p-5" id="btn_docente" ><i class="fa-solid fa-user fs-1"></i><br><p class="fs-5  mt-3">DOCENTES</p></button></div>
            <div class="col-md-3"><button class=" btn-inicio w-100  p-5" id="btn_curso_inicio"><i class="fa-solid fa-building-columns fs-1"></i><br><p class="fs-5 mt-3">CURSOS</p></button></div>
        </div>
        <div class="row justify-content-around">
            <div class="col-md-3"><button class=" btn-inicio w-100  p-5" id="btn_cal_acad" ><i class="fa-solid fa-calendar fs-1"></i><br><p class="fs-5 mt-3">CALENDARIO ACADÉMICO</p></button></div>
            <div class="col-md-3"><button class=" btn-inicio w-100  p-5" id="btn_regl"><i class="fa-solid fa-book fs-1"></i><br><p class="fs-5 mt-3">REGLAMENTO</p></button></div>
            <div class="col-md-3"><button class=" btn-inicio w-100  p-5" id="btn_reporte"><i class="fa-solid fa-file-lines fs-1"></i><br><p class="fs-5 mt-3">REPORTE DEL PROGRAMA</p></button></div>
            
        </div>
        <div class="row justify-content-around">
            <div class="col-md-3"><button class=" btn-inicio w-100  p-5" id="btn_perfil" ><i class="fa-solid fa-circle-user fs-1"></i><br><p class="fs-5 mt-3">MI PERFIL</p></button></div>
            
        </div>
     </div>   
</div> 
</div> 

<?php
    require('../form-doc/footer.php');
?>
<?php
}
ob_end_flush();
?>
</html>