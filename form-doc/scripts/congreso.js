//Agregar Congreso
$('#btn_congreso').click(function(){
    $('#boton_congreso').hide();
    $('#congreso').append('<div class="card mb-3" id="ingresar_congreso"><div class="card-body" id="cong_card"> </div></div>');
    $('#cong_card').append('<div class="row justify-content-between"><div class="col-auto mb-3""><h4>Congreso</h4></div><div class="col-auto"><button type="button" name="add" id="" class="btn btn-close btn-sm borrar_congreso"></button></div></div>');   
    $('#cong_card').append('<div class="row "> <div class="col-md-6 mb-3"><label for="nom_cong" class="form-label">Nombre</label><input type="text" class="form-control" id="nom_cong" name="0" autocomplete="off" ><ul class="list-group" id="list_cong"></ul></div><div class="col-md-6 mb-3"><label for="ciudad_cong" class="form-label">Ciudad</label><input type="text" class="form-control" id="ciudad_cong" maxlength="45"></div></div>');
    $('#cong_card').append('<div class="row "> <div class="col-md-6 mb-3"><label for="fech_in_cong" class="form-label">Fecha Inicio</label><input type="date" class="form-control" id="fech_in_cong" name="fecha_inicio" maxlength="60"><div class="row" id="val_fech_in_cong"></div></div><div class="col-md-6 mb-3"><label for="fech_ter_cong" class="form-label">Fecha Término</label><input type="date" class="form-control" id="fech_ter_cong" name="fecha_ter"> </div></div>');
    $('#cong_card').append('<div class="row mt-2 "><div class="col-12 my-3 "><select id="cong" name="cong" class="form-select cong"><option selected value="0">Participación</option><option value="1">Coordinación</option><option value="2">Expositor/a</option><option value="3">Presentación Póster</option></select></div>');
    $('#cong_card').append('<div class="col" id="id_cong"></div>')
    $('#cong_card').append(' <div class="row justify-content-center " id="mnsj_row_cong"><div class="col-lg-8 alert  text-center alert-danger" role="alert" id="mnsj_cong"></div></div>')
    $('#cong_card').append('<div class="row justify-content-center" id="guardar_cong" ><div class="col-md-6 mt-3 d-grid gap-2"><button type="submit" class="btn btn-dark">Guardar Congreso</button></div></div>');
    $('#guardar_cong').hide();
    $('#mnsj_row_cong').hide();
    anios('#anio_cong',1960)
    ajaxSelect('#pais_cong',ruta+'ajax/pais.php','Seleccione','pais');
    //buscador nombre congreso
    $('#nom_cong').keyup(function(){    
      $('#list_cong').hide();
      busqueda=$('#nom_cong').val();  
      if(busqueda!==''){
        $.ajax({
            url: '../ajax/congreso.php',
            type: 'POST',
            data: {op:'read_cong',busqueda},
            success: function(response){
            let nom_cong = JSON.parse(response);
            let template ='';
            //$('#prof_guia').attr('name','');
            nom_cong.forEach(list => {
                template += `
                <li class='list-group-item listCong' id='${list[0]}' name="${list[1]}"> ${cadenaMay(list[1])}</li>`});
                $('#list_cong').html(template);
                $('#list_cong').show();           
             }
         })
    
      }    
    })
    
})
$(document).on('change','.cong',function(){
    $('#part').remove();
      if($('#cong').val() == '1'){
        campo1='Nombre Mesa/Simposio';
        campo2='Comentarista';
        rol1='Coordinador/a';
        rol2='Coorganizador/a';
        coaut='Coorganizadores/as'
        tit_autor='Coordinador/a'
      }else if($('#cong').val() == '2'){
        campo1='Nombre Mesa/Simposio';
        campo2='Nombre Ponencia';
      }else if($('#cong').val() == '3'){
        campo1='Nombre Poster';
        campo2=''
        
      }
      if($('#cong').val() == '3'||$('#cong').val() == '2'){
        rol1='Autor/a';
        rol2='Coautor/a';
        coaut='Coautores/as'
        tit_autor='Autor/a'
      }
      if($('#cong').val() == '0'){
        $('#guardar_cong').hide();
      }else{
        $('#guardar_cong').show();
        $('#id_cong').append('<div class="row justify-content-between" id="part"><div class="col" id="coord_col"></div></div>');    
        $('#coord_col').append('<div class="row"><div class="col-md-6 mb-3"><label for="nom_mesa" class="form-label" >'+campo1+'</label><input type="text" class="form-control" id="nom_mesa" name="0" autocomplete="off" maxlength="45"><ul class="list-group" id="list_part"></ul></div><div class="col-md-6 mb-3" id="segundo_campo"><label for="coment_ponenc" class="form-label">'+campo2+'</label><input type="text" class="form-control" id="coment_ponenc" name="comentarista" maxlength="80"></div></div>');
        $('#coord_col').append('<div class="row" id="rol_c"><div class="col-md-6 "><label class="form-label" for="tipo_cong">Tipo</label><select class="form-select" id="tipo_cong"><option selected value="0">Seleccione</option><option value="1">Mesa</option><option value="2">Simposio</option></select></div><div class="col-md-6 mb-3 "><label for="rol_cong" class="form-label">Rol</label><select id="rol_cong" class="form-select coord"><option selected value="0">Rol</option><option value="1">'+rol1+'</option><option value="2">'+rol2+'</option></select></div></div>');
        $('#coord_col').append('<div class="row"><div class="col mb-3"><div class="card"><div class="card-body"><div class="row justify-content-between"><div class="col-auto mb-3"> <h6 class="card-title fs-5">'+coaut+'</h6></div></div> <div class="row"><div class="col-md-12 mb-3"><label for="coautores" class="form-label">(Ingresar nombres separados por una coma)</label><textarea class="form-control" id="coautores" rows="3" maxlength="300"></textarea></div> </div> </div> </div></div></div>');
      }
      // Agregar Coordinador Mesa
      
      if($('#cong').val() == '3'){
        $('#segundo_campo').remove()
      }
    
      $('#nom_mesa').keyup(function(){    
        $('#list_part').hide();
        busqueda=$('#nom_mesa').val();
        tipo_part=$('#cong').val();
        id_congreso=$('#nom_cong').attr('name')  
        if(busqueda!=='' && id_congreso
        !=0){
            console.log(tipo_part)
          $.ajax({
              url: '../ajax/congreso.php',
              type: 'POST',
              data: {op:'read_part',busqueda,tipo_part,cong:id_congreso},
              success: function(response){
              let nom_part = JSON.parse(response);
              console.log(nom_part)
              let template ='';
              //$('#prof_guia').attr('name','');
              nom_part.forEach(list => {
                  template += `
                  <li class='list-group-item listPart' id='${list[0]}' name="${list[1]}"> ${list[1]}</li>`});
                  $('#list_part').html(template);
                  $('#list_part').show();           
               }
           })
      
        }    
      })
    
    });
    //agregar campo organizador mesa
$(document).on('change','.coord',function(){
    $('#aut').remove();
    if($('#rol_cong').val() == '2'){
    $('#rol_c').append('<div class="col-md-6 mb-3" id="aut"> <label for="autor" class="form-label">'+tit_autor+'</label><input type="text" class="form-control" id="autor" name="0" maxlength="60"></div>');
    }
});    
    //Eliminar Congreso
$(document).on('click', '.borrar_congreso', function(){
    $('#ingresar_congreso').remove();
    
    $('#boton_congreso').show();
});
function cargarCong(usuario,id){
    op='read'
    $.ajax({
        async: false,
        type : 'POST',
        url : '../ajax/congreso.php',
        data : {op,usuario},
        success : function(response) {
            let congrs = JSON.parse(response);
            let tabla_cong ='';
            let tabla_part ='';
            let id_cong
            let cont_cong=0//contador congresos
            let i=0//contador participaciones
            let c=0
            let con=1
        congrs.forEach(cong => { 
            i=cong['id_congreso']==id_cong?i:1
            tipo_part=cong['tipo_part']==1?'Coordinación':cong['tipo_part']==2?'Expositor/a':'Presentación Poster';
            tipo_cong=cong['tipo_cong']==1?'Mesa':'Simposio';
            nombre=cong['tipo_part']==1||cong['tipo_part']==2?'Nombre Mesa/Simposio':'Nombre Poster'
            comen_pon=cong['tipo_part']==1?cong['coment_ponenc']:cong['tipo_part']==2?cong['coment_ponenc']:'';
            tit_comen_pon=cong['tipo_part']==1?'Comentarista':cong['tipo_part']==2?'Nombre Ponencia':'';
            if(cong['tipo_part']==1){
                tit_autor='Coordinador/a'
                tit_coautor='Coorganizador/a'
                tit_otros='Otros Organizadores'
                rol=cong['id_coaut']==usuario?'Coorganizador/a':'Coordinador/a'
            }else{
                tit_autor='Autor/a'
                tit_coautor='Coautor/a'
                tit_otros='Otros Coautores'
                rol=cong['id_coaut']==usuario?'Coautor/a':'Autor/a'
            }
                i++;
                i=cong['id_congreso']==id_cong?i:1
                tabla_part=`<td><h5>PARTICIPACIÓN</h5></td>
                
                <td></td>
                </tr> 
                <tr>
                <td>Tipo de Participación</td>
                <td>${tipo_part}</td>
                </tr>
                <tr>
                <td>Tipo</td>
                <td>${tipo_cong}</td>
                </tr>
                
                <tr>
                <td>${nombre}</td>
                <td>${cadenaMay(cong['nombre_mesa'])}</td>
                </tr>
                <tr>
                <td>Rol</td>
                
                <td>${rol}</td>
                </tr>
                `
                           
            if(cong['id_coaut']==usuario){
                op='read-usu'
                usu=cong['id_aut']
                $.ajax({
                    async:false,
                    type : 'POST',
                    url : '../ajax/congreso.php',
                    data : {op,usu},
                    success : function(response) {
                        let autor = JSON.parse(response);
                        console.log(autor)
                        if(cong['id_aut']==null){
                            autor=cadenaMay(cong['nom_aut'])
                        }else{
                            nombre=cadenaMay(autor[0]['nombres'])+' '+cadenaMay(autor[0]['ap_pat'])+' '+cadenaMay(autor[0]['ap_mat'])
                            autor=nombre
                        }
                        
                        tabla_part+=`<tr>
                        <td>${tit_autor}</td>
                        <td>${autor}</td>
                        </tr>`
                    }

                    })               
            }
            if( cong['otros_org']!=null){
                
                tabla_part+=`<tr>
                <td>${tit_otros}</td>
                <td>${mostrarAutores(cong['otros_org'])}</td>
                </tr>`
            }
            if(cong['tipo_part']==1||cong['tipo_part']==2){
                tabla_part+=`<tr>
                <td>${tit_comen_pon}</td>
                <td>${cadenaMay(comen_pon)}</td>
                </tr>`
            }
                     
                if(cong['id_congreso']!=id_cong){
                    id_cong=cong['id_congreso']
                    if(c<cont_cong){
                        tabla_cong+=` </tbody>
                        </table>    
                        </div>
                        </div>`
                        c++;                           
                        i=1;
                    }
                    cont_cong ++;
                    
                    tabla_cong += `
                    <div class="card mb-3 grado_card">
                    <div class="card-body">
                    <table class="table table-striped" id="part${cong['id_congreso']}">
                    <thead>
                    <tr >
                    <th class="col-md-4 titulo_acad"><H5>CONGRESO</H5></th>
                    <th class="p-0"><button type="button" class="btn btn-link ps-1 link-secondary" id="${cong['id_congreso']}">Editar Congreso</button></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                    <td>Nombre</td>
                    <td>${cadenaMay(cong['nombre'])}</td>
                    </tr>
                    <tr>
                    <td>Ciudad</td>
                    <td>${cadenaMay(cong['ciudad'])}</td>
                    </tr>
                    <tr>
                    <td>Fecha de Inicio</td>
                    <td>${mostrarFecha(cong['fecha_inicio'])}</td>
                    </tr>
                    <tr>
                    <td>Fecha de Término</td>
                    <td>${mostrarFecha(cong['fecha_termino'])}</td>
                    </tr> 
                    <tr>
                    `
                    tabla_cong+=tabla_part; 
                    con++;
                    
                }else{ 
                    tabla_cong+=tabla_part
                                 
                    
                }                    
        });       
            $(id).append(tabla_cong);
        }
    })
}

function limpiarCamposCong(){
        limpiarInput('#nom_cong');
        limpiarInput('#ciudad_cong');
        limpiarInput('#fech_in_cong');
        limpiarInput('#fech_ter_cong');
}
$('body').on('click','.listCong',function(){
    id=$(this).attr('id');
    nom_cong=$(this).attr('name');
    $('#nom_cong').val(cadenaMay(nom_cong));
    $('#nom_cong').prop('disabled',true);
    $('#nom_cong').attr('name',id);
    $('#list_cong').hide();
    if($('#nom_cong').attr('name')!==0){
        $.ajax({
            url: '../ajax/congreso.php',
            type: 'POST',
            data: {op:'carg_cong',id},
            success: function(response){
            let congreso = JSON.parse(response);
            let template ='';
            //$('#prof_guia').attr('name','');
            $('#ciudad_cong').val(cadenaMay(congreso[0][2]));
            $('#ciudad_cong').prop('disabled',true);
            $('#fech_in_cong').val(congreso[0][3]);
            $('#fech_in_cong').prop('disabled',true);
            $('#fech_ter_cong').val(congreso[0][4]);
            $('#fech_ter_cong').prop('disabled',true);
            $('#anio_cong').val(congreso[0][5]);
            $('#anio_cong').prop('disabled',true);
            limpiarCamposCong()
            }
            })
    }

})
$('body').on('click','.listPart',function(){
    id=$(this).attr('id');
    nom_part=$(this).attr('name');
    $('#nom_mesa').val(cadenaMay(nom_part));
    $('#nom_mesa').prop('disabled',true);
    $('#nom_mesa').attr('name',id);
    $('#list_part').hide();
    console.log($('#nom_mesa').attr('name'))
    if($('#nom_mesa').attr('name')!==0){
        $.ajax({
            url: '../ajax/congreso.php',
            type: 'POST',
            data: {op:'carg_part',id},
            success: function(response){
            let part = JSON.parse(response);
            let template ='';
            //$('#prof_guia').attr('name','');
            if(part[0]['tipo_part']==1||part[0]['tipo_part']==2){
                $('#coment_ponenc').val(cadenaMay(part[0]['coment_ponenc']));
            }
            $('#coment_ponenc').prop('disabled',true);
            $('#tipo_cong').val(part[0]['tipo_cong']);
            $('#tipo_cong').prop('disabled',true);
            $('#coautores').val(part[0]['otros_org']==null?'':mostrarAutores(part[0]['otros_org']));
            $('#coautores').prop('disabled',true);
            part.forEach(p => {
                if(p['id_aut']==null){
                    $('#rol_cong').val(1);
                    $('#rol_cong').prop('disabled',true);
                    $('#rol_cong').attr('name',p['id_participacion']);
                }else{
                    $('#rol_cong').val(2);
                    $('#rol_cong').prop('disabled',true);
                    $('#rol_cong').attr('name',p['id_participacion']);
                }

            })
        }
    })
}

})    
$('#form_congreso').submit(function(e){
    e.preventDefault();
    
    $('#nom_cong').click(function(){limpiarInput('#nom_cong');})
    $('#ciudad_cong').click(function(){limpiarInput('#ciudad_cong');})
    $('#fech_in_cong').click(function(){limpiarInput('#fech_in_cong');})
    $('#fech_ter_cong').click(function(){limpiarInput('#fech_ter_cong');})
    $('#rol_cong').click(function(){limpiarSelect('#rol_cong');})
    $('#tipo_cong').click(function(){limpiarSelect('#tipo_cong');})
    $('#autor').click(function(){limpiarInput('#autor');})
    $('#nom_mesa').click(function(){limpiarInput('#nom_mesa');})
    $('#coment_ponenc').click(function(){limpiarInput('#coment_ponenc');})
    let nombre=guardar($('#nom_cong').val());
    let ciudad=guardar($('#ciudad_cong').val());
    let fech_in=$('#fech_in_cong').val();
    let fech_ter=$('#fech_ter_cong').val();
    let tipo_part=$('#cong').val();
    let rol=$('#rol_cong').val();
    let autor=rol==1?$('#id_usuario').attr('name'):'';
    let coautor=rol==2?$('#id_usuario').attr('name'):'';
    let tipo_cong=$('#tipo_cong').val();
    let nom_autor=document.getElementById("autor")==null?'':guardar($('#autor').val());
    let coautores=guardarAutores($('#coautores').val());
    let nom_mesa=guardar($('#nom_mesa').val());
    let comen_pon=tipo_part==3?'':guardar($('#coment_ponenc').val());
    
    
    //agragar nuevo congreso y participacion(si no existe congreso)
    function validarCong(){
        
        if(nombre=='' || ciudad==''|| fech_in=='' || fech_ter==''||rol=='0'||$('#autor').val()==''||$('#coment_ponenc').val()==''||nom_mesa==''||tipo_cong=='0'){
            console.log('entra al if');
            validCampoVacio('#nom_cong');
            validCampoVacio('#ciudad_cong');
            validCampoVacio('#fech_in_cong');
            validCampoVacio('#fech_ter_cong');
            validCampoVacio('#autor');
            validCampoVacio('#nom_mesa');
            validCampoVacio('#coment_ponenc');
            validSelect('#rol_cong');
            validSelect('#anio_cong');
            validSelect('#tipo_cong');
            $('#mnsj_row_cong').show();
            $('#mnsj_cong').html('Rellene todos los campos requeridos')
            if($('#nom_cong').attr('name')!=0){
            limpiarCamposCong();
            }
            return false;
        }else{
            return true;
        }
    }
    if(validarCong()){
    if($('#nom_cong').attr('name')==0){
            op='insert-update';
            congreso={nombre,ciudad,fech_in,fech_ter,tipo_part,rol,tipo_cong,nom_autor,coautor,nom_mesa,comen_pon,autor,coautores,op};
            console.log(congreso)     
            $.post('../ajax/congreso.php',congreso,function(response){
                let dato = JSON.parse(response);
                console.log(dato);
                    $('#mnsj_row_cong').show();
                    $('#mnsj_cong').removeClass('alert-danger');
                    $('#mnsj_cong').addClass('alert-success');
                    $('#mnsj_cong').html(dato);
                    setTimeout(function() {
                        $('#ingresar_congreso').remove();
                        $('#boton_congreso').show();
                        
                $("#mnsj_row_cong").fadeOut(1500);
            },3000);
            })
        }else{
            if($('#nom_mesa').attr('name')==0){
                cong=$('#nom_cong').attr('name');
                op='insert-part';
                congreso={tipo_part,rol,tipo_cong,nom_autor,coautor,nom_mesa,comen_pon,autor,coautores,op,cong};
                $.post('../ajax/congreso.php',congreso,function(response){
                    let dato = JSON.parse(response);
                    $('#mnsj_row_cong').show();
                    $('#mnsj_cong').removeClass('alert-danger');
                    $('#mnsj_cong').addClass('alert-success');
                    $('#mnsj_cong').html(dato);
                    setTimeout(function() {
                        $('#ingresar_congreso').remove();
                        $('#boton_congreso').show();
                        cargarCong($('#id_usuario').attr('name'),'#congreso_card')
                        $("#mnsj_row_cong").fadeOut(1500);
                    },3000);
                })
            }else{
                op='update-rol';
                part=$('#rol_cong').attr('name');
                $.post('../ajax/congreso.php',{autor,op,part,rol,coautor},function(response){
                    let dato = JSON.parse(response);
                    $('#mnsj_row_cong').show();
                    $('#mnsj_cong').removeClass('alert-danger');
                    $('#mnsj_cong').addClass('alert-success');
                    $('#mnsj_cong').html(dato);
                    setTimeout(function() {
                        $('#ingresar_congreso').remove();
                        $('#boton_congreso').show();
                        cargarCong($('#id_usuario').attr('name'),'#congreso_card')
                        $("#mnsj_row_cong").fadeOut(1500);
                    },3000);
                })
                
                

            }

        }
    }
}) 
//cargarCong($('#id_usuario').attr('name'))



