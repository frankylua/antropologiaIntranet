//Agregar Beca
$('#btn_beca').click(function(){
    $('#boton_beca').hide();
    $('#beca').append('<div class="card mb-3" id="ingresar_beca"><div class="card-body" id="card_beca"> </div></div>');
    $('#card_beca').append('<div class="row justify-content-between"><div class="col-auto mb-3"><h4 class="card-title">Beca</h4></div><div class="col-auto"><button type="button"  id=""  class="btn btn-close btn-sm borrar_beca"></button></div></div>');
    $('#card_beca').append('<div class="row" id="row_nombre"><div class="col-md-6 mb-3"><label for="tipo_beca" class="form-label">Tipo Beca</label><select class="form-select" id="tipo_beca"><option selected value="0">Seleccione</option><option value="1">Beca Interna</option><option value="2">Beca Externa</option></select></div><div class="col-md-6 mb-3" id="id_nom_beca" name="0"><label for="nom_beca" class="form-label">Nombre Beca</label><select class="form-select" id="nom_beca"><option value="0"></option></select></div></div>');
    $('#card_beca').append('<div class="row" id="row_inst"><div class="col-md-6 mb-3" id="id_inst_beca" name="0"><label for="inst" class="form-label">Institución</label><select class="form-select" id="inst_beca"></select></div><div class="col-md-6"></div></div>');
    $('#card_beca').append('<div class="row "> <div class="col-md-6 mb-3"><label for="fech_in_beca" class="form-label">Fecha Inicio</label><input type="date" class="form-control" id="fech_in_beca" ></div><div class="col-md-6 mb-3"><label for="fech_ter_beca" class="form-label">Fecha Término</label><input type="date" class="form-control" id="fech_ter_beca" name="fecha_ter"> </div></div>');
    $('#card_beca').append(' <div class="row justify-content-center " id="mnsj_row_beca"><div class="col-lg-8 alert  text-center alert-danger" role="alert" id="mnsj_beca"></div></div>')
    $('#card_beca').append('<div class="row justify-content-center" ><div class="col-md-6 mt-3 d-grid gap-2"><button type="submit" class="btn btn-dark">Guardar Beca</button></div></div>');
    $('#mnsj_row_beca').hide();
    ajaxSelect('#inst_beca',ruta+'ajax/institucion.php','Seleccione','read');
  
  });
  $(document).on('change','#tipo_beca',function(){
    $('#n_b').remove();
    if($('#tipo_beca').val()==1){
      ajaxSelect('#nom_beca',ruta+'ajax/beca.php','Seleccione','read-nombre',1);
    }else if($('#tipo_beca').val()==2){
      ajaxSelect('#nom_beca',ruta+'ajax/beca.php','Seleccione','read-nombre',2);
    }else{
      $('#nom_beca').html('<option value="0"></option>')
    }
  })
  //agregar campo beca externa
  $(document).on('change','#nom_beca',function(){
    $('#n_b').remove();
    if($('#nom_beca').val()=='otro'){
      $('#row_nombre').append('<div class="col-md-6" id="n_b"><input type="text" class="form-control mb-3" placeholder="Nombre Beca" id="nueva_beca" maxlength="80"></div>');
    }
  })
  $(document).on('change','#inst_beca',function(){
    $('#i_b').remove();
    if($('#inst_beca').val()=='otro'){
      $('#row_inst').append('<div class="col-md-6" id="i_b"><input type="text" class="form-control mb-3" placeholder="Institución" id="nueva_inst" maxlength="80"></div>');
    }
  })
  //Eliminar Beca
  $(document).on('click', '.borrar_beca', function(){
   $('#ingresar_beca').remove();
   $('#boton_beca').show();
  });

  function cargarBeca(usuario,id){
    op='read'
    $.ajax({
        async: false,
        type : 'POST',
        url : '../ajax/beca.php',
        data : {op,usuario},
        success : function(response) {
            let becas = JSON.parse(response);
            let template ='';
            let con=1
      becas.forEach(beca => { 
         
        template += `
        <div class="card mb-3 grado_card">
            <div class="card-body">
                <table class="table table-striped">
                <thead>
                <tr >
                <th class="col-md-4"><h5>BECA</h5></th>
                <th></th>
                </tr>
                </thead>
                    <tbody>
                      <tr>
                          <td>Tipo</td>
                          <td>${beca['tipo_beca']==1?'Beca Interna':'Beca Externa'}</td>
                      </tr>        
                        <tr>
                            <td>Nombre Beca</td>
                            <td>${cadenaMay(beca['beca'])}</td>
                        </tr>
                        <tr>
                            <td>Institución</td>
                            <td>${cadenaMay(beca['inst'])}</td>
                        </tr>
                        <tr>
                            <td>Fecha Inicio</td>
                            <td>${mostrarFecha(beca['fech_in'])}</td>
                        </tr> 
                        <tr>
                        <td>Fecha Término</td>
                        <td>${mostrarFecha(beca['fech_ter'])}</td>
                    </tr> 
                    </tbody>
                </table>    
            </div>
        </div>                   
        `
        con++
    });
    $(id).append(template);

        }
    })
}

$('#form_beca').submit(function(e){
    e.preventDefault();
    $('#tipo_beca').click(function(){limpiarSelect('#tipo_beca');})
    $('#nom_beca').click(function(){limpiarSelect('#nom_beca');})
    $('#inst_beca').click(function(){limpiarSelect('#inst_beca');})
    $('#nueva_beca').click(function(){limpiarInput('#nueva_beca');})
    $('#nueva_inst').click(function(){limpiarInput('#nueva_inst');})
    $('#fech_in_beca').click(function(){limpiarInput('#fech_in_beca');})
    $('#fech_ter_beca').click(function(){limpiarInput('#fech_ter_beca');})
    let tipo=$('#tipo_beca').val();
    let inst=$('#inst_beca').val()=='otro'?$('#nueva_inst').val():$('#inst_beca').val();
    let nombre=$('#nom_beca').val()=='otro'?$('#nueva_beca').val():$('#nom_beca').val();
    let fecha_in=$('#fech_in_beca').val();
    let fecha_ter=$('#fech_ter_beca').val();
    let usuario=$('#id_usuario').attr('name');
    if(validCampoVacio('#nueva_inst')||validCampoVacio('#nueva_beca')||fecha_in=='' ||fecha_ter=='' ||tipo=='0' || nombre=='0'|| inst=='0' ){
      validCampoVacio('#nueva_beca')  
        validSelect('#inst_beca');
        validSelect('#nom_beca');
        validCampoVacio('#fech_in_beca');
        validCampoVacio('#fech_ter_beca');
        validSelect('#tipo_beca');
        $('#mnsj_row_beca').show();
        $('#mnsj_beca').html('Rellene todos los campos')
    }else{
        nuevo_inst=$('#inst_beca').val()=='otro'?true:false;//agregar nuevo instituto
        nuevo_bec=$('#nom_beca').val()=='otro'?true:false;//agregar nuevo beca
        // en caso de ser "true" ingresar en tabla beca y/o instituto y devolver id para guardar grado academico
        if(nuevo_inst){
            op='insert';
            $.ajax({
                async: false,
                type : 'POST',
                url : '../ajax/institucion.php',
                data : {op,nombre:inst},
                success : function(response) {
                    let dato = JSON.parse(response);
                    $('#id_inst_beca').attr('name',dato);
                }
            })
        }
        if(nuevo_bec){
            op='insert';
            $.ajax({
                async: false,
                type : 'POST',
                url : '../ajax/beca.php',
                data : {op,nueva_beca:nombre,tipo},
                success : function(response) {
                    let dato = JSON.parse(response);
                    $('#id_nom_beca').attr('name',dato);
                }
            })
        }
        if($('#id_inst_beca').attr('name')!=0){
            inst=$('#id_inst_beca').attr('name')
        }
        if($('#id_nom_beca').attr('name')!=0){
            nombre=$('#id_nom_beca').attr('name')
        }
        console.log(inst+'+++++'+nombre)
        op='insert-update';
        becas={inst,nombre,tipo,fecha_in,fecha_ter,usuario,op};
        console.log(becas)
        $.post('../ajax/beca.php',becas,function(response){
            let dato = JSON.parse(response);
            console.log(dato);
                $('#mnsj_row_beca').show();
                $('#mnsj_beca').removeClass('alert-danger');
                $('#mnsj_beca').addClass('alert-success');
                $('#mnsj_beca').html(dato);
                setTimeout(function() {
                    $('#ingresar_beca').remove();
                    $('#boton_beca').show();
                    cargarBeca(usuario,'#beca_card')
            $("#mnsj_row_beca").fadeOut(1500);
        },2000);
        })
        
    }
})



