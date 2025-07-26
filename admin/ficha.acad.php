<?php
    ob_start();
    if (strlen(session_id()) < 1){
        session_start();//Validamos si existe o no la sesión
    }
    if(!isset($_SESSION['admin']) && !isset($_SESSION['comite'])){
        header('Location:../index.php');
    }else
    {
    require('../form-doc/header.php')
?>

<div class="container mt-5">
    <div class="row">
        <div class="col">
            
                <h1 class=" text-center mt-5">FICHA ACADÉMICA</h1>
              <form class="row g-3 m-5" id="form" name="form">      
                  <div class="row mt-5">
                    <div class="col mb-3">
                      <table class="table" id="table_ficha">
                        <thead>
                          <tr>
                            <td class="col-4"></td>
                            <td class="col-8"></td>
                          </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td class="col-4">Nombre del Académico</th>
                              <td class="col-8">Iván Ricardo Muñoz Ovalle</th>
                          </tr>
                            <tr>
                          <td>Carácter del vínculo (claustro/núcleo, colaborador o visitante)</td>
                          <td>Claustro</td>
                        </tr>
                        <tr>
                          <td>Título, institución, país</td>
                          <td>Arqueólogo, Universidad del Norte, Chile</td>
                        </tr>
                        <tr>
                          <td>Grado máximo (especificar área disciplinar), institución, año de graduación y país </td>
                          <td>Doctor en Antropología, Universidad Autónoma de México, 2003, México</td>
                        </tr>
                        <tr>
                          <td>Línea(s) de investigación o áreas de trabajo </td>
                          <td>Movilidad, Espacios y Fronteras</td>
                        </tr>
                        <tr>
                          <td>Número de tesis de maister dirigidas en los últimos 10 años (finalizadas) </td>
                          <td><p class="fw-bold">Como guía de tesis</p>
                              <table class="table">
                                <thead>
                                    <tr>
                                        <th>Año</th>
                                        <th>Autor</th>
                                        <th>Título de la Tesis</th>
                                        <th>Nombre del Programa</th>
                                        <th>Institución</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                       <td></td>
                                       <td></td>
                                       <td></td>
                                       <td></td>
                                       <td></td> 
                                    </tr>
                                </tbody>
                              </table>
                              <p class="fw-bold">Como coguía de tesis</p>
                              <table class="table">
                                <thead>
                                    <tr>
                                        <th>Año</th>
                                        <th>Autor</th>
                                        <th>Título de la Tesis</th>
                                        <th>Nombre del Programa</th>
                                        <th>Institución</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                              </table>
                          </td>
                        </tr>
                        <tr>
                          <td>Número de tesis de doctorado dirigidas en los últimos 10 años (finalizadas) </td>
                          <td><p class="fw-bold">Como guía de tesis</p>
                              <table class="table">
                                <thead>
                                    <tr>
                                        <th>Año</th>
                                        <th>Autor</th>
                                        <th>Título de la Tesis</th>
                                        <th>Nombre del Programa</th>
                                        <th>Institución</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                    <td>2011</td>
                                        <td>María Soledad Fernández Murillo</td>
                                        <td>“Modelando en arcilla:
aproximaciones a la producción y el consumo de cerámica durante el Período Formativo de los valles costeros del Norte de Chile (1400 a.c. – 500 d.C.)”.</td>
                                        <td>Doctorado en Antropología</td>
                                        <td>UTA/UCN</td>
                                    </tr>
                                </tbody>
                              </table>
                              <p class="fw-bold">Como coguía de tesis</p>
                              <table class="table">
                                <thead>
                                    <tr>
                                        <th>Año</th>
                                        <th>Autor</th>
                                        <th>Título de la Tesis</th>
                                        <th>Nombre del Programa</th>
                                        <th>Institución</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                  </tbody>
                                </table>
                              </td>
                            </tr>
                            <table class="table">
                              <h5 class="col-12 text-center">PRODUCTIVIDAD CIENTÍFICA (ÚLTIMOS 10 AÑOS CERRADOS)</h5>
                              <thead>
                                <tr>
                                  <th>Nº</th>
                                  <th>Autor(es)</th>
                                  <th>Año</th>
                                  <th>Título del artículo</th>
                                  <th>Nombre revista</th>
                                  <th>Estado</th>
                                  <th>ISSN</th>
                                  <th>Factor de impacto</th>
                                </tr>
                            </thead>
                            <tbody>
                              <h5 class="col-12 text-center fw-bold">WOS</h5>
                              <tr>
                                <td>1</td>
                                <td>Watson, J.T., I. Muñoz</td>
                                <td>2018</td>
                                <td>Early Andean Diaspora, Culinary Traditions, and Dietary Continuity in the Periphery</td>
                                <td>Current Anthropology</td>
                                <td>Aceptado</td>
                                <td>0011- 3204</td>
                                <td>2,326 (JCR)</td>
                              </tr>
                              <tr>
                                <td>2</td>
                                <td>Muñoz, I.</td>
                                <td>2018</td>
                                <td>Cronología del periodo Medio en el valle de Azapa, norte de Chile: estilos, fechados y contextos culturales del poblamiento humano</td>
                                <td>Chungara, Revista de Antropología Chilena</td>
                                <td>Aceptado</td>
                                <td>0717- 7356</td>
                                <td>0,713 (JCR)</td>
                              </tr>
                              <tr>
                                <td>3</td>
                                <td>Muñoz, I.</td>
                                <td>2018</td>
                                <td>Cronología del periodo Medio en el valle de Azapa, norte de Chile: estilos, fechados y contextos culturales del poblamiento humano</td>
                                <td>Chungara, Revista de Antropología Chilena</td>
                                <td>Aceptado</td>
                                <td>0717- 7356</td>
                                <td>0,713 (JCR)</td>
                              </tr>
                            </tbody>
                            
                          </table>
                        </table>
                      </tbody>
                    </div>
                </div>

                <div class="row mt-5 justify-content-between ">
              <div class="col-md-4 mb-3">
                <button type="submit" class="col-12 btn btn-dark  col-6">Editar Datos</button>
              </div>
              <div class="col-md-4 mb-3 ">
                <button type="button" class="col-12 btn btn-dark  col-6 "  id="imprimirFicha">Imprimir Ficha Docente</button>
              </div>
            </div>
                                


                  
            </form>
      </div>
  </div>
<?php
    require('../form-doc/footer.php')
?>
<?php
}
ob_end_flush();
?>