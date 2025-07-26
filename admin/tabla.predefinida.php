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
<div class="container mt-2">
    <div class="row">
        <div class="col g-3 m-5">

                    <h3 class="card-title mb-5 text-center">TABLAS PREDEFINIDAS</h3>
                    
                    <div class="row ">
                        <label for="fecha_inicio" class="form-check-label mb-3">Rango de años</label>
                        <div class="col-md-6 mb-4">
                            
                            <select  id="fecha_inicio" class="form-select"></select>
                        </div>
                        <div class="col-md-6 mb-4">
                            
                            <select  id="fecha_fin" class="form-select"></select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="list-group">
                                <button type="button" class="list-group-item list-group-item-action" >Integrantes de Cuerpo Académico por Línea de Investigación</button>
                                <button type="button" class="list-group-item list-group-item-action">Resumen Postulantes, Seleccionados, Matriculados por Cohorte</button>
                                <button type="button" class="list-group-item list-group-item-action">Origen disciplinar de los/as Estudiantes</button>
                                <button type="button" class="list-group-item list-group-item-action">Cursos asociados al Programa</button>
                                <button type="button" class="list-group-item list-group-item-action">Progresión de estudiantes y Evolución de Resultados</button>
                                <button type="button" class="list-group-item list-group-item-action">Situación de los alumnos graduados</button>
                                <button type="button" class="list-group-item list-group-item-action">Cuerpo Académico del Programa</button>
                                <button type="button" class="list-group-item list-group-item-action">Claustro/Núcleo Académico del Programa</button>
                                <button type="button" class="list-group-item list-group-item-action">Productividad Cuerpo Académico del Programa, Publicaciones y Proyectos</button>
                                <button type="button" class="list-group-item list-group-item-action">Beneficiarios/as Becas</button>
                                <button type="button" class="list-group-item list-group-item-action">Internación del Programa</button>    
                                <button type="button" class="list-group-item list-group-item-action" >Relación entre postulantes y aceptados</button>
                                <button type="button" class="list-group-item list-group-item-action">Origen deisciplinar e institucional de los estudiantes</button>
                                <button type="button" class="list-group-item list-group-item-action">Desglose de actividades curriculares obligatorias y electivas</button>
                                <button type="button" class="list-group-item list-group-item-action">Progresión de estudiantes</button>
                                <button type="button" class="list-group-item list-group-item-action">Seguimientos de estudiantes que se encuentran realizado su tesis</button>
                                <button type="button" class="list-group-item list-group-item-action">Permanencia en el programa</button>
                                <button type="button" class="list-group-item list-group-item-action">Tabla deserción</button>
                                <button type="button" class="list-group-item list-group-item-action">Informacion y resultados de tesis de graduados</button>
                                <button type="button" class="list-group-item list-group-item-action">Caracterización por género y nacionalidad alumnos/as ingresados/as</button>
                                <button type="button" class="list-group-item list-group-item-action">Informacion y resultados de tesis de graduados</button>
                                <button type="button" class="list-group-item list-group-item-action">Caracterización por género y nacionalidad alumnos/as ingresados/as por cohorte</button>
                                <button type="button" class="list-group-item list-group-item-action">Caracterización por género y nacionalidad Matrícula y graduación por año</button>
                                <button type="button" class="list-group-item list-group-item-action">Estudiantes con Beca y tipo de Beca</button>
                                <button type="button" class="list-group-item list-group-item-action">Académicos</button>
                                <button type="button" class="list-group-item list-group-item-action">Productividad Académicos del Claustro/Núcleo</button>
                                <button type="button" class="list-group-item list-group-item-action">Estudiantes Tesistas</button>
                                <button type="button" class="list-group-item list-group-item-action">Detalle de Publicaciones</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>   
        </div>
    </form>
</div>
</div>


<?php
    require('../form-doc/footer.php');
    ?>
    <?php
}
ob_end_flush();
?>
    