
// limpiar las cajas de datos

//validar estado de radio
function estadoRadio(id_radio){
  if(!$(id_radio).is('checked')){
    alert('true')
    estado=true
  }else{
    alert('false')
    estado=false
  }
  return estado
}
//validar select

function validSelect( id){
  if($(id).val() == '0'){
    $(id).addClass('is-invalid')
    return true;
  }else{
    $(id).removeClass('is-invalid')
    $(id).addClass('is-valid')
    return false;
  }
}
function validRadio(name,clase){
  if($('input:radio[name='+name+']:checked').attr("value")==null){
    $(clase).addClass('is-invalid');
    return true;
  }else{
    $(clase).removeClass('is-invalid');
    return false;
  }
} 

 //validar campos vacios
 function validCampoVacio(id){
   if($(id).val() == ''){
    $(id).addClass('is-invalid');
    return true;
  }else {
    $(id).removeClass('is-invalid');
    $(id).addClass('is-valid');
    return false;
  }
}
//habilitar/desabilitar radio e imput
function desabRadInp(estado,cont,id_radio,id_input,col){
  i=id_radio.slice(cont)  
  if(estado == false){
    $(id_input+i).prop('disabled',true)
    $(id_input+i).removeClass('is-invalid');
    $(col+i).remove();
    $('#'+id_radio).prop('checked',true)
    estado=true
  }else{
    alert('olaaa')
    $('#'+id_radio).prop('checked',false)
    $(id_input+i).prop('disabled',false)
    estado=false
  }
}
//validar select dinamicos
function validSelectDinamico (id, row, col,id_contenedor){
    var v = 1;
    while($(id_contenedor+v).length){
      if($(id+v).val() == '0'){
        $('#'+col+v).remove();
        $(row+v).append('<div class="col  text-danger" id="'+col+v+'">Seleccione una opción</div>');
        $(id+v).addClass('is-invalid');
      }else{
        $('#'+col+v).remove();
        $(id+v).addClass('is-valid');
      }
      v++;
    }
  }
  function quitarSeleccRadio(i,id,name){
    
    if(($("input[name="+name+i+"]:radio").is(':checked'))){
     $(id+i).prop('checked',false)
    }
    
  }
  //validar campos vacios dinamicos
  function validarInputSelDinamic(id,row,col,id_contenedor){
    var v = 1;
    while($(id_contenedor+v).length){
      if($(id+v).val() == ''){
        $('#'+col+v).remove();
        $(row+v).append('<div class="col  text-danger" id="'+col+v+'">Este campo es obligatorio</div>');
        $(id+v).addClass('is-invalid');
      }else{
        $('#'+col+v).remove();
        $(id+v).removeClass('is-invalid');
        $(id+v).addClass('is-valid');
      }
      v++;
    }
  }
  //validar campos vacios dinamicos
  function validarCamposVacios(id,row,col){
    var v = 1;
    while($(id+v).length){
      if($(id+v).val() == ''){
        $('#'+col+v).remove();
        $(row+v).append('<div class="col  text-danger" id="'+col+v+'">Este campo es obligatorio</div>');
        $(id+v).addClass('is-invalid');
      }else{
        $('#'+col+v).remove();
        $(id+v).removeClass('is-invalid');
        $(id+v).addClass('is-valid');
      }
      v++;
    }
  }
  function validarCamposVaciosRadio(id,row,col,name,id_contenedor){
   var v = 1;
   while($(id_contenedor+v).length){
     if($(id+v).val() == '' && ($("input[name="+name+v+"]:radio").is(':checked'))){
       $('#'+col+v).remove();
       $(id+v).removeClass('is-invalid');
     }else{
       $('#'+col+v).remove();
       $(row+v).append('<div class="col  text-danger" id="'+col+v+'">Este campo es obligatorio</div>');
       $(id+v).addClass('is-invalid');
     }
     v++;
   }
 }

 // validar radio dinamicos
 function valRadioDinamic(row,col,name,id_contenedor){
   var v = 1;
   while($(id_contenedor+v).length){
     if(($("input[name="+name+v+"]:radio").is(':checked'))){
       $('#'+col+v).remove();
      
     }else{
       $('#'+col+v).remove();
       $(row+v).append('<div class="col  text-danger" id="'+col+v+'">Seleccione una opción</div>');
      
     }
     v++;
   }
 }
  

 //limpiar select
 function limpiarSelect(id){
   $(id).removeClass('is-invalid');
   $(id).removeClass('is-valid');
   
 }
 //limpiar radio
 function limpiarRadio(col){
   $(col).remove()
 }
 //limpiar radio dinamico
 function limpiarRadioDinamic(col){
    
    $(col).remove()
 }
 //limpiar select dinamico
 function limpiarSelectDinam(id,col){
   //nro_id = id.slice(cont);
   if($('#'+id).val() != '0'){
       $(col).remove();
       $('#'+id).removeClass('is-invalid');
       $('#'+id).removeClass('is-valid');
     }else{
       $('#'+id).removeClass('is-valid');
     }
 }
 function limpiarInput(id){
   $(id).removeClass('is-invalid');
   $(id).removeClass('is-valid');
   
 }
 function limpiarInputDinamico(id,col){
   
   $(col).remove();
   $("#"+id).removeClass('is-invalid');
 }

   
    
    


    
    
    

    
    

  
        





        





 

  


  

       
      



    
    
    
    
    
    
      
    
    
    


    
      
  


              

    
    
    

