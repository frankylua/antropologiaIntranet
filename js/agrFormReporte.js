 
 $(document).ready(function(){
   anioInform("#inf_desde",2006,"Desde")
   anioInform("#inf_hasta",2006,"Hasta")
   anioInform("#fecha_inicio",2006,"Desde")
   anioInform("#fecha_fin",2006,"Hasta")
 //Agregar tipo Alumno/Docente
 $('#doc').click(function(){
    $('#estudiante').remove();
    $('#beca').remove();
    $('#tesis').remove();
    $('#docente').remove();
    $('#tip_doc').append('<select id="docente" class="form-select"><option selected>Tipo Docente</option><option>Claustro</option><option>Visitante</option><option>Colaborador/a</option><option>Todos/as</option></select>');
    $('#beca_tesis').append('<div class="form-check" id="tesis"><input class="form-check-input" type="checkbox"  id="tes"><label class="form-check-label" for="tes">Tesis</label></div>');

  });
  $('#est').click(function(){
    $('#docente').remove();
    $('#estudiante').remove();
    $('#beca').remove();
    $('#tesis').remove();
    $('#tip_est').append('<select id="estudiante" class="form-select"><option selected>Tipo Estudiante</option><option>Postulante</option><option>Aceptao</option><option>Matriculado/a</option><option>Graduado/a</option><option>Retirado/a</option><option>Retirado/a</option><option>Eliminado/a</option><option>Todos/as</option></select>');
    $('#beca_tesis').append('<div class="form-check" id="beca"><input class="form-check-input" type="checkbox"  id="bec"><label class="form-check-label" for="bec">Beca</label></div>');

});

  //Agregar  Publicacion/Congreso
  estado_pub=false
  estado_con=false
  $('#pub').click(function(){
    if(estado_pub == false){
      $('#publicacion').remove();
      $('#tip_pub').append('<select id="publicacion" class="form-select mb-3"><option selected>Tipo Publicación</option><option>Artículo</option><option>Libro o Capítulo</option><option>Edicion de Revista Temática </option><option>Otras</option><option>Todas</option></select>');
      estado_pub=true
    }else{
      $('#publicacion').remove();
      estado_pub=false
    }
  });
  $('#con').click(function(){
    if(estado_con == false){
      $('#congreso').remove();
      $('#tip_con').append('<select id="congreso" class="form-select mb-3"><option selected>Tipo Participación</option><option>Expositor/a Simposio</option><option>Coordinador/a Mesa</option><option>Presentación Póster</option><option>Todos</option></select>');
      estado_con=true
    }else{
      $('#congreso').remove();
      estado_con=false
    }

  });
 
     
    

  


    
  
  


  
 
  
    
});
