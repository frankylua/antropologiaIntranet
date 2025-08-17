

$(document).ready(function(){

$.post('../ajax/pais.php', {op:'pais'}, function (response) {
        mensaje = JSON.parse(response);
        templateSelect(listas,'#pais_nac')
})
$.post('../ajax/pais.php', {op:'pais'}, function (response) {
        mensaje = JSON.parse(response);
        templateSelect(listas,'#pais_res')
})



  // mostrarPais('#pais_nac');
  // mostrarPais('#pais_res');

   //Agregar Pueblo Indigena
  $('#si').click(function(){
    $('#p').remove()
    $('#row_pueb').append('<div class="col-md-6 mb-3" id="p"><label for="pueb_ind" class="form-label" >Pueblo Indígena</label><select id="select_pueb" name="select_pueb" class="form-select"></select></div>')
    ajaxSelect('#select_pueb',ruta+'ajax/pueblo.php','Seleccione','read');
  });
  $('#no').click(function(){
    $('#p').remove()
    $('#otro_pueb').remove()
  });
  $(document).on('change','#select_pueb',function(){
    $('#otro_pueb').remove()
    if($('#select_pueb').val() == 'otro'){
      $('#row_pueb').append('<div class="col-md-6 mb-3  offset-md-6" id="otro_pue"><input type="text" class="form-control" id="otro_pueb" placeholder="Nombre del pueblo indígena al que pertenece" name="otro_pueb"></div>')
    }
  })
})
  
    
        

  
  

  

