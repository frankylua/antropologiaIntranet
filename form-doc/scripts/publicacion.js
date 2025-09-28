  //Agregar Publicación
  $('#btn_publicacion').click(function(){
    $('#boton_publicacion').hide();
  $('#publicacion').append('<div class="card mb-3" id="ingresar_publicacion"><div class="card-body" id="pub_card"> </div></div>');
  $('#pub_card').append('<div class="row justify-content-between"><div class="col-auto mb-3"><h4>Publicación</h4></div><div class="col-auto"><button type="button" name="add" id="close_pub" class="btn btn-close btn-sm borrar_publicacion"></button></div></div>');   
  $('#pub_card').append('<div class="row "><div class="col-12 mb-3 "> <label for="pub" class="form-label">Tipo de Publicación</label><select id="pub" name="pub" class="form-select publ"><option selected value="0">Publicación</option><option value="1">Artículo</option><option value="2">Edición de Revista Temática</option><option value="3">Libro o capítulo de Libro</option><option value="4">Otra Publicación</option></select></div></div>');
  $('#pub_card').append('<div class="col" id="id_pub"></div>');
  $('#pub_card').append(' <div class="row justify-content-center " id="mnsj_row_pub"><div class="col-lg-8 alert  text-center alert-danger" role="alert" id="mnsj_pub"></div></div>')
  $('#pub_card').append('<div class="row justify-content-center" id="guardar_pub" ><div class="col-md-6 mt-3 d-grid gap-2"><button type="submit" class="btn btn-dark">Guardar Publicación</button></div></div>');
  $('#mnsj_row_pub').hide();
  $('#guardar_pub').hide();
  })
  //form articulo/revista
  function formArtRev(cont){
    $(cont).append('<div class="row justify-content-between" id="row_pub"><div class="col" id="pub_col"></div></div></div>');    
      $('#pub_col').append('<div class="row "> <div class="col-md-8 mb-3"><label for="titulo" class="form-label">Título</label><textarea rows="2" class="form-control " id="titulo" maxlength="300"></textarea></div><div class="col-md-4 mb-3"><label for="anio" class="form-label">Año</label><select  class="form-select anio" id="anio"><select/></div></div>');
      $('#pub_col').append('<div class="row"><div class="col mb-3"><div class="card"><div class="card-body"><div class="row justify-content-between"><div class="col-auto mb-3"> <h6 class="card-title fs-5">Otros/as Autores/as</h6></div></div> <div class="row"><div class="col-md-12 mb-3"><label for="autores" class="form-label">(Ingresar nombres separados por una coma)</label><textarea class="form-control " id="autores" rows="3" maxlength="300"></textarea></div> </div> </div> </div></div></div>');
      $('#pub_col').append('<div class="row"><div class="col-md-6 mb-3"> <label for="nombre" class="form-label">Nombre Revista</label><input type="text" class="form-control " id="nombre" maxlength="60"></div><div class="col-md-6 mb-3 "><label for="indizacion" class="form-label">Indización</label><select id="indizacion" class="form-select indiz"><option selected value="0">Tipo</option ><option value="2">Wos</option><option value="3">Scopus</option><option value="4">Erih Plus</option ><option value="5">Scielo</option><option value="6">Latindex</option><option value="1">No tiene</option></select></div></div>');
      $('#pub_col').append('<div class="row"><div class="col-md-6 mb-3 "><label for="estado" class="form-label">Estado</label><select id="estado" class="form-select est"><option selected value="0">Seleccione Estado</option><option value="1">Publicada</option><option value="2">En Prensa</option><option value="3">Aceptada</option><option value="4">Enviada</option></select></div><div class="col-md-6 mb-3"><label for="issn" class="form-label">Issn</label><input type="text" class="form-control " id="issn" maxlength="9"></div>');
      $('#pub_col').append('<div class="row"><label for="fact_imp" class="form-label">Factor de Impacto</label><div class="col-md-6 mb-3"><input type="number" step="any" class="form-control fact_imp" id="fact_imp" name="factor"></div><div class="col-md-6 mb-3 align-self-center"><div class="form-check"><input type="radio" class="form-check-input no_fac"  id="no_fac" name="no_fac"><label for="no_fac" class="form-check-label">No tiene Factor de Impacto</label></div></div>');
      $('#pub_col').append(' <div class="row justify-content-center " id="mnsj_row_pub"><div class="col-lg-8 alert  text-center alert-danger" role="alert" id="mnsj_pub"></div></div>')

      anios('#anio',1960);
  }
  //libro
  function formLibro(cont){
    $(cont).append('<div class="row justify-content-between" id="libro"><div class="col" id="libro_col"></div></div>');
    $('#libro_col').append('<div class="row"><div class="col-md-6 mb-3"><select id="tipo_libro" class="form-select select_acad"><option selected value="0">Tipo</option><option value="1">Libro</option><option value="2">Capítulo</option></select></div><div class="col-md-6 mb-3"><select id="rol_libro" class="form-select"><option selected value="0">Rol</option><option value="1">Autor/a</option><option value="2">Editor/a</option></select></div></div>');
    $('#libro_col').append('<div class="row"><div class="col-md-8 mb-3"><label for="nombre" class="form-label">Nombre Capítulo y/o Libro</label><input type="text" class="form-control" id="nombre" maxlength="60"></div><div class="col-md-4 mb-3"><label for="anio" class="form-label">Año</label><select  class="form-select anio" id="anio"><select/></div></div>');
    $('#libro_col').append('<div class="row"><div class="col mb-3"><div class="card"><div class="card-body"><div class="row justify-content-between"><div class="col-auto mb-3"><h6 class="card-title fs-5">Otros/as Autores/as</h6></div></div><div class="row"><div class="col-md-12 mb-3"><label for="autores" class="form-label">(Ingresar nombres separados por una coma)</label><textarea class="form-control" id="autores" rows="3" maxlength="300"></textarea><div class="row" id="val_aut_libro"></div></div></div></div></div></div></div>');
    $('#libro_col').append('<div class="row"><div class="col-md-6 mb-3"><label  class="form-label mb-2">Referato Externo</label><div class="row"><div class="col-auto"><div class="form-check"><input type="radio" class="form-check-input referato" name="ref_ext" id="si_ref_ext" value="1"><label for="si_ref_ext" class="form-check-label">Sí</label></div></div><div class="col-auto"><div class="form-check"><input type="radio" class="form-check-input referato" name="ref_ext" value="0" id="no_ref_ext"><label for="no_ref_ext" class="form-check-label">No</label></div></div></div></div><div class="col-md-6 mb-3"><label  class="form-label mb-2">Corresponde a traducción</label><div class="row"><div class="col-auto"><div class="form-check"><input type="radio" class="form-check-input traduccion" name="traduc" value="1" id="si_traduc"><label for="si_traduc" class="form-check-label">Sí</label></div></div><div class="col-auto"><div class="form-check"><input type="radio" class="form-check-input traduccion" name="traduc" value="0" id="no_traduc"><label for="no_traduc" class="form-check-label">No</label></div></div></div></div>')
    $('#libro_col').append('<div class="row"><div class="col-md-6 mb-3"><label for="lugar" class="form-label">Lugar</label><input type="text" class="form-control" id="lugar" maxlength="45"><div class="row" id="val_lug_lib"></div></div><div class="col-md-6 mb-3"><label for="estado" class="form-label">Estado</label><select id="estado" class="form-select"><option selected value="0">Estado</option><option value="1">Publicada</option><option value="2">En Prensa</option><option value="3">Aceptada</option><option value="4">Enviada</option></select></div></div>');
    $('#libro_col').append('<div class="row"><div class="col-md-6 mb-3"><label for="editorial" class="form-label">Editorial(es)</label><input type="text" class="form-control" id="editorial" placeholder="Editorial" name="0"><ul class="list-group" id="list_editorial"></ul></div></div><div class="row"><div class="col-md-6 " id="agr_editorial"></div></div>');
    $('#libro_col').append('<div class="row"><div class="col-md-3 mb-3"><button type="button" class="btn btn-dark btn_editorial" id="btn_editorial">Agregar Editorial</button></div></div>');
    $('#libro_col').append(' <div class="row justify-content-center " id="mnsj_row_pub"><div class="col-lg-8 alert  text-center alert-danger" role="alert" id="mnsj_pub"></div></div>')

    anios('#anio',1950) 
    $('#editorial').keyup(function(){    
      $('#list_editorial').hide();
      busqueda=$('#editorial').val(); 
      if(busqueda!==''){
        $.ajax({
            url: '../ajax/publicacion.php',
            type: 'POST',
            data: {op:'read_edit',busqueda},
            success: function(response){
            let editoriales = JSON.parse(response);
            let template ='';
            console.log(editoriales)
            //$('#prof_guia').attr('name','');
            editoriales.forEach(list => {
                template += `
                <li class='list-group-item listEdit' id='${list[0]}' name="${list[1]}"> ${list[1]}</li>`});
                $('#list_editorial').html(template);
                $('#list_editorial').show();           
             }
         })
    
      }    
    })
  }
  //form otra publicacion
  function formOtraPub(cont){
    $(cont).append('<div class="row justify-content-between" id="otra_pub"><div class="col" id="otra_col"></div></div>');
    $('#otra_col').append('<div class="row"><div class="col-md-6 mb-3"><label for="tipo_otra_pub" class="form-label">Tipo</label><input type="text" class="form-control" id="tipo_otra_pub" maxlength="50"></div>');
    $('#otra_col').append('<div class="row"><div class="col mb-3"><div class="row"><div class="col-md-12 mb-3"><label for="desc_pub" class="form-label">Descripción</label><textarea class="form-control" id="desc_pub" rows="3" maxlength="300"></textarea><div class="row" id="val_desc_pub"</div></div></div></div>');
    $('#otra_col').append(' <div class="row justify-content-center " id="mnsj_row_pub"><div class="col-lg-8 alert  text-center alert-danger" role="alert" id="mnsj_pub"></div></div>')


  }
  let x=0
  let arrayx=[]

    function agrEdit(){
        x++ 
        $('#agr_editorial').append('<div class="row" id="ed'+x+'"><div class="col mb-3"><input type="text" class="form-control" id="edit'+x+'" placeholder=" Editorial" name="0" maxlength="60"><ul class="list-group" id="list_editorial'+x+'"></ul></div><div class="col-auto mb-3"><button type="button" class="btn btn-dark elim_edit" id="'+x+'">x</button></div></div>');
        arrayx.push(x)
        $('#ed'+x).keyup(function(){    
          $('#list_editorial'+x).hide();
          busqueda=$('#edit'+x).val(); 
          if(busqueda!==''){
            $.ajax({
                url: '../ajax/publicacion.php',
                type: 'POST',
                data: {op:'read_edit',busqueda},
                success: function(response){
                let editoriales = JSON.parse(response);
                let template ='';
                console.log(editoriales)
                //$('#prof_guia').attr('name','');
                editoriales.forEach(list => {
                    template += `
                    <li class='list-group-item listEditNew' id='${list[0]}' name="${list[1]}"> ${list[1]}</li>`});
                    $('#list_editorial'+x).html(template);
                    $('#list_editorial'+x).show();           
                 }
             })
        
          }    
        })
      $('body').on('click','.listEditNew',function(){
        id=$(this).attr('id');
        nom_edit=cadenaMay($(this).attr('name'));
        $('#edit'+x).val(nom_edit);
        $('#edit'+x).attr('name',id);
        $('#list_editorial'+x).hide();   
    })
    }
  
  //Select Publicacion
  $(document).on('change','.publ',function(){  
    $('#row_pub').remove();
    $('#libro').remove();
    $('#otra_pub').remove();
  
    if($('#pub').val() == '0'){
      $('#guardar_pub').hide();
    }else{
      $('#guardar_pub').show();
    }
    //agregar articulo o revista
    if($('#pub').val() == '1' || $('#pub').val()== '2'){
      formArtRev('#id_pub')
    }
    //Agregar Libro
    if($('#pub').val() == '3'){
         formLibro('#id_pub')           
    }
    //Agregar Editorial
    $('#btn_editorial').click(function(){       
        agrEdit();
    })
    //Eliminar Editorial
    $(document).on('click', '.elim_edit', function(){
        var button_id = $(this).attr("id"); 
        arrayx = arrayx.filter(exis => exis != button_id)
        $('#ed'+button_id).remove();
    })
    //Agregar Otra Publicacion
    if($('#pub').val() =='4'){
        formOtraPub('#id_pub')
    }
  })
  //borrar formulario Publicacion
  $(document).on('click', '.borrar_publicacion', function(){
    $('#ingresar_publicacion').remove();
    $('#boton_publicacion').show();
  })
  function cargarPub(usuario,id){
    //cargar articulos y revistas
    $.ajax({
        async: false,
        type : 'POST',
        url : '../ajax/publicacion.php',
        data : {op:'read_articulos',usuario},
        success : function(response) {
            let publis = JSON.parse(response);
            let template ='';
      publis.forEach(pub => {
        tipo=pub['tipo']==1?'Artículo':'Edición de Revista Temática'
        fact_imp=pub['factor_impacto']==0?'No tiene factor de Impacto':pub['factor_impacto']
        template += `
        <div class="card mb-3 card_pub">
            <div class="card-body">
                <table class="table table-striped">
                <thead>
                <tr>
                <th class="col-md-4 titulo_acad"><h5>PUBLICACIÓN</h5></th>
                <th class="row justify-content-end ps-0 botones"><button type="button" class="col-auto btn btn-link link-success ps-1 editarPub" id="${pub['id_publicacion']}" name="${pub['tipo']}">Editar</button><button type="button" class="col-auto btn btn-link link-danger ps-1 eliminarPub" id="${pub['id_publicacion']}">Eliminar</button></th>
                </tr>
                </thead>
                    <tbody>
                        <tr>
                            <td>Tipo de Publicación</td>
                            <td>${tipo}</td>
                        </tr>
                        <tr>
                            <td>Titulo</td>
                            <td>${letraMay(pub[5])}</td>
                        </tr> 
                        <tr>
                            <td>Año</td>
                            <td>${pub[2]}</td>
                        </tr>
                        <tr>
                            <td>Nombre Revista</td>
                            <td>${letraMay(pub[0])}</td>
                        </tr>
                        <tr>
                            <td>Indizacion</td>
                            <td>${letraMay(pub[7])}</td>
                        </tr>
                        <tr>
                            <td>Estado</td>
                            <td>${letraMay(pub[3])}</td>
                        </tr> 
                        <tr>
                            <td>Issn</td>
                            <td>${pub[6]}</td>
                        </tr> 
                        <tr>
                            <td>Factor de Impacto</td>
                            <td>${fact_imp}</td>
                        </tr>           
                                      
        `
        
        if( pub['otros_autores']!=null){
                
            template+=`<tr>
            <td>Otros/as Autores/as</td>
            <td>${mostrarAutores(pub['otros_autores'])}</td>
            </tr> 
            `
        }
        template+=` </tbody>
                </table>    
            </div>
        </div>`
    });
    
    
 
    $(id).append(template);

        }
    })    
    //cargar Libros
    $.ajax({
        async: false,
        type : 'POST',
        url : '../ajax/publicacion.php',
        data : {op:'read_libros',usuario},
        success : function(response) {
            let libros = JSON.parse(response);
            let template ='';
      libros.forEach(libro => {
        tipo=libro['tipo']==1?'Libro':'Capítulo de Libro'
        tipo_pub=libro['tipo']==1?3:4
        rol=libro['rol']==1?'Autor/a':'Editor/a'
        ref_ext=libro['ref_ext']==0?'No tiene':'Si tiene'
        traduccion=libro['traduccion']==0?'No correspone':'Si corresponde'
        template += `
        <div class="card mb-3 card_pub">
            <div class="card-body">
                <table class="table table-striped">
                <thead>
                <tr>
                <th class="col-md-4 titulo_acad"><h5>PUBLICACIÓN</h5></th>
                <th class="row justify-content-end ps-0 botones"><button type="button" class="col-auto btn btn-link link-success ps-1 editarPub" id="${libro['id_publicacion']}" name="${tipo_pub}">Editar</button><button type="button" class="col-auto btn btn-link link-danger ps-1 eliminarPub" id="${libro['id_publicacion']}">Eliminar</button></th>
                </tr>
                </thead>
                    <tbody>
                        <tr>
                            <td>Tipo de Publicación</td>
                            <td>${tipo}</td>
                        </tr>
                        <tr>
                            <td>Nombre Capítulo y/o Libro</td>
                            <td>${cadenaMay(libro['nom_pub'])}</td>
                        </tr> 
                        <tr>
                            <td>Rol</td>
                            <td>${rol}</td>
                        </tr>
                       
                        <tr>
                            <td>Año</td>
                            <td>${libro['anio']}</td>
                        </tr>
                        <tr>
                            <td>Estado</td>
                            <td>${letraMay(libro['nom_est'])}</td>
                        </tr>
                        <tr>
                            <td>Referato Externo</td>
                            <td>${ref_ext}</td>
                        </tr> 
                        <tr>
                            <td>Traducción</td>
                            <td>${traduccion}</td>
                        </tr> 
                        <tr>
                            <td>Lugar</td>
                            <td>${cadenaMay(libro['lugar'])}</td>
                        </tr>                           
        `
        
        if( libro['otros_autores']!=null){
                
            template+=`<tr>
            <td>Otros/as Autores/as</td>
            <td>${mostrarAutores(libro['otros_autores'])}</td>
            </tr> 
            `
        }
        //cargar editoriales
        $.ajax({
            async:false,
            type : 'POST',
            url : '../ajax/publicacion.php',
            data : {op:'read_edit_libro',libro:libro['id_libro']},
            success : function(response) {
                let editoriales = JSON.parse(response);
                let cadenaEditorial=''
                let coma=''
                editoriales.forEach(ed => {
                    cadenaEditorial+=coma+cadenaMay(ed['nombre'])
                    coma=', ' 
                })
                template+=`<tr>
                    <td>Editorial/es</td>
                    <td>${cadenaEditorial}</td>
                    </tr>`

            }

            })
        template+=` </tbody>
                </table>    
            </div>
        </div>`
    });
    
    
 
    $(id).append(template);

        }
    })
    //cargar otra publicacion
    $.ajax({
        async: false,
        type : 'POST',
        url : '../ajax/publicacion.php',
        data : {op:'read_otro_art',usuario},
        success : function(response) {
            let publis = JSON.parse(response);
            let template ='';
      publis.forEach(pub => {
        template += `
        <div class="card mb-3 card_pub">
            <div class="card-body">
                <table class="table table-striped">
                <thead>
                <tr>
                <th class="col-md-4 titulo_acad"><h5>PUBLICACIÓN</h5></th>
                <th class="row justify-content-end ps-0 botones"><button type="button" class="col-auto btn btn-link link-success ps-1 editarPub" id="${pub['id_otra_pub']}" name="5">Editar</button><button type="button" class="col-auto btn btn-link link-danger ps-1 eliminarOtraPub" id="${pub['id_otra_pub']}">Eliminar</button></th>
                </tr>
                </thead>
                    <tbody>
                        <tr>
                            <td>Tipo de Publicación</td>
                            <td>${cadenaMay(pub['tipo'])}</td>
                        </tr>
                        <tr>
                            <td>Descripcion</td>
                            <td>${cadenaMay(pub['descripcion'])}</td>
                        </tr> 
                        <tr>
                                   
                    </tbody>
                </table>    
            </div>
        </div>                   
        `
    });
    
    $(id).append(template);
    
 

        }
    })
}
//editar publicacion
//buscar la manera de enconta¡rar el tipo de publicacion
$("body").on("click", ".editarPub", function () {
    id_pub = $(this).attr("id");
    tipo_pub = $(this).attr("name");// tipo de publicacion
    $('#edit_academicos').attr('name',id_pub)
    //if articulo   
    editAcadDoc()
    $('#campos_publicacion').append('<h3 class="mb-5 text-center" id="tipo_pub">EDITAR PUBLICACIÓN</h3>')
    if(tipo_pub==1||tipo_pub==2){
        formArtRev('#campos_publicacion')
        $.ajax({
            async: false,
            url: "../ajax/publicacion.php",
            type: "POST",
            data: { op: "read_art_rev_id", id_pub  },
            success: function (response) {
            let artRev = JSON.parse(response);
            autores=artRev[0]['otros_autores']==null?'':mostrarAutores(artRev[0]['otros_autores'])
            $('#tipo_pub').attr('name',tipo_pub)//agrego el tipo de publicacion para validar al editar
            $('#titulo').val(cadenaMay(artRev[0]['titulo']))
            $('#titulo').attr('name',cadenaMay(artRev[0]['titulo']))
            $('#anio').val(artRev[0]['anio'])
            $('#anio').attr('name',artRev[0]['anio'])
            $('#autores').val(autores)
            $('#nombre').val(cadenaMay(artRev[0]['nombre']))
            $('#nombre').attr('name',cadenaMay(artRev[0]['nombre']))
            $('#indizacion').val(artRev[0]['indizacion'])
            $('#indizacion').attr('name',artRev[0]['indizacion'])
            $('#estado').val(artRev[0]['estado'])
            $('#estado').attr('name',artRev[0]['estado'])
            $('#issn').val(artRev[0]['issn'])
            $('#issn').attr('name',artRev[0]['issn'])
            if(artRev[0]['factor_impacto']==0){
                $('#no_fac').prop('checked',true)
                $('#fact_imp').prop('disabled',true)   
            }else{
                $('#fact_imp').val(artRev[0]['factor_impacto'])
            }

      },
    });
    
    }
    if(tipo_pub==3||tipo_pub==4){
        formLibro('#campos_publicacion')
       x=1
       arrayx=[]
       //peticion articulo-revista
        $('#btn_editorial').click(function(){       
            agrEdit(x,arrayx);
        })
        //Eliminar Editorial
        $(document).on('click', '.elim_edit', function(){
            var button_id = $(this).attr("id"); 
            arrayx = arrayx.filter(exis => exis != button_id)
            $('#ed'+button_id).remove();
        }) 
        $.ajax({
            async: false,
            url: "../ajax/publicacion.php",
            type: "POST",
            data: { op: "read_libro_id", id_pub  },
            success: function (response) {
            let libro = JSON.parse(response);
            autores=libro[0]['otros_autores']==null?'':mostrarAutores(libro[0]['otros_autores'])
            $('#tipo_pub').attr('name',tipo_pub)//agrego el tipo de publicacion para validar al editar
            $('#tipo_libro').val(libro[0]['tipo'])
            $('#rol_libro').val(libro[0]['rol'])
            $('#nombre').val(libro[0]['nom_pub'])
            $('#anio').val(libro[0]['anio'])
            $('#autores').val(autores)
            $('#estado').val(libro[0]['estado'])
            //check referato externo
            libro[0]['ref_ext']==0?$('#no_ref_ext').prop('checked',true):$('#si_ref_ext').prop('checked',true)
            //check correspode a traduccion
            libro[0]['traduc']==0?$('#no_traduc').prop('checked',true):$('#si_traduc').prop('checked',true)
            $('#lugar').val(libro[0]['lugar'])
            $('#estado').val(libro[0]['estado'])
            //mostrar/cargar editoriales
                libro.forEach(lib=>{          
                    if(libro.indexOf(lib) === 0){
                        console.log('el indicee')
                        $('#editorial').val(lib['nom_ed'])
                        console.log(lib['nom_ed'])
                }else{
                    
                    $('#agr_editorial').append('<div class="row" id="ed'+lib['id_editorial&G']+'"><div class="col mb-3"><input type="text" class="form-control" id="edit'+'" value="'+lib['nom_ed']+'" name="0" maxlength="60"><ul class="list-group" id="list_editorial'+'"></ul></div><div class="col-auto mb-3"><button type="button" class="btn btn-dark elim_edit" id="'+lib['id_editorial']+'">x</button></div></div>');
                }

            });

      },
    });
    }
    if(tipo_pub==5){
        formOtraPub('#campos_publicacion')
        //peticion otra publicacion
        $.ajax({
            async: false,
            url: "../ajax/publicacion.php",
            type: "POST",
            data: { op: "read_otro_art_id", id_pub  },
            success: function (response) {
            let otraPub = JSON.parse(response);
            $('#tipo_pub').attr('name',tipo_pub)
            $('#tipo_otra_pub').val(otraPub[0]['tipo'])//agrego el tipo de publicacion para validar al editar
            $('#desc_pub').val(otraPub[0]['descripcion'])
            

      },
    });
        
    }
    btn_editar_acad('#campos_publicacion',$('#info_doc').attr('name'))
   

    //formGrado('#campos_grado')
    
    //if libro
    //if otra publicacion
    // $.ajax({
    //   async: false,
    //   url: "../ajax/grado.php",
    //   type: "POST",
    //   data: { op: "read_grado_id", id_grado },
    //   success: function (response) {
    //     let grado = JSON.parse(response);
    //     console.log('editar usu')
    //     console.log(grado)
    //     tipo=grado[0]['tipo_grado']
    //     console.log(tipo)
    //     $('#grad_acad').val(tipo==1||tipo==2?1:2)
    //     $('#inst_grado').val(grado[0]['inst_grado'])
    //     $('#fech_grado').val(grado[0]['fech_graduacion'])
    //     $('#grado').html('<option selected value="0">Seleccione</option')
    //     tipo_tit=tipo==1?'lic':tipo==2?'un':tipo==3?'mag':'doc'
    //     ajaxSelect('#s_tit',ruta+'ajax/titulo.php','Seleccione','read',tipo_tit)
    //     $('#s_tit').val(grado[0]['tit_grado'])
    //     if(tipo==1||tipo==2){
    //         $('#grado').append('<option value="1">Licenciatura</option><option selected value="2">Título Universitario</option>')
    //         if(tipo==1){
    //             $('#grado option[value="1"]').attr("selected",true)
    //         }else{
    //             $('#grado option[value="2"]').attr("selected",true)
    //         }

            
    //     }else{
    //         $('#grado').html('<option value="3">Magister</option><option selected value="4">Doctorado</option>')
    //         tipo==3?$('#grado option[value="3"]').attr("selected",true):tipo==4?$('#grado option[value="4"]').attr("selected",true):''
    //     }
    //   },
    // });
});
$('body').on('click','.listEdit',function(){
    id=$(this).attr('id');
    nom_edit=cadenaMay($(this).attr('name'));
    $('#editorial').val(nom_edit);
    $('#editorial').attr('name',id);
    $('#list_editorial').hide();   
})
$("body").on("click", ".eliminarPub", function () {
    id_pub = $(this).attr("id");
    usu=$('#info_doc').attr('name')
    $.ajax({
        url: "../ajax/publicacion.php",
        type: "POST",
        data: { id_postdoc, op: "delete" },
        success: function (response) {
            if($('lista_doc')=='true'){
                cargarFichaDoc(usu)
            }else{
                cargarFichaEst(usu) 
            }           
            let mensaje = JSON.parse(response);
            $("html, body").animate({ scrollTop: $('#ant_acad_doc').offset().top}, 100);
            $("#mnsj_row_acad_doc").show();
            $("#mnsj_acad_doc").addClass("alert-success");
            $("#mnsj_acad_doc").html(mensaje);
            setTimeout(function () {
                $("#mnsj_row_acad_doc").fadeOut(1500);
            }, 3000);
        },
      });
  
});
$("body").on("click", ".eliminarOtraPub", function () {
    id_pub = $(this).attr("id");
    usu=$('#info_doc').attr('name')
    $.ajax({
        url: "../ajax/publicacion.php",
        type: "POST",
        data: { id_pub, op: "delete_otra_pub" },
        success: function (response) {
            cargarFichaDoc(usu);
            let mensaje = JSON.parse(response);
            $("html, body").animate({ scrollTop: $('#ant_acad_doc').offset().top}, 100);
          $("#mnsj_row_acad_doc").show();
          $("#mnsj_acad_doc").addClass("alert-success");
          $("#mnsj_acad_doc").html(mensaje);
          setTimeout(function () {
            $("#mnsj_row_acad_doc").fadeOut(1500);
          }, 3000);
        },
      });
  
});
$('#form_publicacion').submit(function(e){
    e.preventDefault();
    //console.log('esta es la variableee.....'+arrayx)
    $('#anio').click(function(){limpiarSelect('#anio');})
    $('#autores').click(function(){limpiarInput('#autores');})
    $('#nombre').click(function(){limpiarInput('#nombre');})
    $('#estado').click(function(){limpiarSelect('#estado');})
    $('#titulo').click(function(){limpiarInput('#titulo');})
    $('#indizacion').click(function(){limpiarSelect('#indizacion');})
    $('#issn').click(function(){limpiarInput('#issn');})
    $('#tipo_libro').click(function(){limpiarSelect('#tipo_libro');})
    $('#rol_libro').click(function(){limpiarSelect('#rol_libro');})
    $('#lugar').click(function(){limpiarInput('#lugar');})
    $('#fact_imp').click(function(){limpiarInput('#fact_imp');})
    $('#tipo_pub').click(function(){limpiarSelect('#tipo_pub');})
    $('#desc_pub').click(function(){limpiarSelect('#desc_pub');})
    $('#editorial').click(function(){limpiarSelect('#editorial');})
    arrayx.forEach(x => {
        $('#edit'+x).click(function(){limpiarSelect('#edit'+x);})
        
    });
    

    let publicacion
    let op='insert'
    let usuario=$('#id_usuario').attr('name');
    let tipo=$('#pub').val();
    let ingrPub
    if(tipo==1||tipo==2||tipo==3){
        anio=$('#anio').val();
        autores=guardarAutores($('#autores').val());
        nombre=guardar($('#nombre').val());
        estado=$('#estado').val();
        publicacion={op,usuario,tipo,anio,autores,nombre,estado};   
        if(anio==0||nombre==''||estado==0){
            validSelect('#anio')
            validCampoVacio('#nombre')
            validSelect('#estado')
            ingrPub=false;
        }else{
            ingrPub=true;
        }

    }
    
    if(tipo == '1' || tipo == '2'){
        titulo=guardar($('#titulo').val());   
        indizacion=$('#indizacion').val();        
        issn=$('#issn').val();
        fac_imp=$("#no_fac").is(':checked')?0:$('#fact_imp').val();
        valFactor=!$("#no_fac").is(':checked')&&$('#fact_imp').val()==''?true:false
        
        if(titulo==''||indizacion==0||issn==0|| valFactor || !ingrPub){
            if(!$("#no_fac").is(':checked')){
                validCampoVacio('#fact_imp')
            }
            validCampoVacio('#titulo')
            validSelect('#indizacion')
            validCampoVacio('#issn')
            $('#mnsj_row_pub').show();
            $('#mnsj_pub').html('Rellene todos los campos articulo');
            ingrPub=false;
        }else{
            ingrPub=true;
        }
        Object.assign(publicacion,{titulo,indizacion,issn,fac_imp})
        console.log(publicacion)
    }else if(tipo==3){
        tipo_lib=$('#tipo_libro').val();
        rol_libro=$('#rol_libro').val();
        ref_ext=$('input:radio[name=ref_ext]:checked').val();
        traduccion=$('input:radio[name=traduc]:checked').val();
        editorial=$('#editorial').attr('name')==0?[$('#editorial').val()]:[$('#editorial').attr('name')];      
        arrayx.forEach(x => 
            editorial.push($('#edit'+x).attr('name')==0?$('#edit'+x).val():$('#edit'+x).attr('name'))
            )
        $('.btn_editorial').click(function(){  
        })
        lugar=guardar($('#lugar').val());
        if(tipo_libro==0||rol_libro==0||ref_ext==null||traduccion==null || editorial==''||lugar=='' || !ingrPub){
            validSelect('#tipo_libro')
            validSelect('#rol_libro')
            validCampoVacio('#editorial')
            validCampoVacio('#lugar')
            ingrPub=false;
            $('#mnsj_row_pub').show();
            $('#mnsj_pub').html('Rellene todos los campos');
        }else{
            if(arrayx.length > 1){
                let contEdit=0
                arrayx.forEach(x => {
                    if($('#edit'+x).val()==''){
                        validCampoVacio('#edit'+x)
                        ingrPub=false
                        contEdit++
                    }
                });
                if(contEdit==0){
                    ingrPub=true
                }


            }
        }
        Object.assign(publicacion,{tipo_lib,rol_libro,ref_ext,traduccion,editorial,lugar})       
    }else{
        tipo_pub=$('#tipo_otra_pub').val();
        desc_pub=$('#desc_pub').val();
        publicacion={op,tipo,tipo_pub,desc_pub,usuario};
        ingrPub=true
    }
    //cargarPub(usuario)
    if(ingrPub){
        $.post('../ajax/publicacion.php',publicacion,function(response){
            let dato = JSON.parse(response);
                $('#mnsj_row_pub').show();
                $('#mnsj_pub').removeClass('alert-danger');
                $('#mnsj_pub').addClass('alert-success');
                $('#mnsj_pub').html(dato);
                setTimeout(function() {
                    $('#ingresar_publicacion').remove();
                    $('#boton_publicacion').show();
                    cargarPub(usuario,'#publicacion_card');
            $("#mnsj_row_pub").fadeOut(1500);
        },3000);
        })
     }
   
    

})
//falta validar los campos vacios (enviar datos default)
$('#form_edit_publicacion').submit(function(e){
    e.preventDefault();
    let id_pub=$('#edit_academicos').attr('name');
    let tipo=$('#tipo_pub').attr('name'); //tipo de publicacion
    tipo=tipo==4?3:tipo==5?4:tipo
    let op='update'
    let publicacion={id_pub,tipo,op}
    if(tipo==1||tipo==2||tipo==3){
        let anio=$('#anio').val();
        anio=anio==''?$('#anio').attr('name'):anio;
        let autores=guardarAutores($('#autores').val());
        let nombre=guardar($('#nombre').val());
        nombre=nombre==''?$('#nombre').attr('name'):nombre;
        let estado=$('#estado').val();
        estado=estado==0?$('#estado').attr('name'):estado;
        Object.assign(publicacion,{anio,autores,nombre,estado}); 
        if(tipo==1||tipo==2){
            let titulo=guardar($('#titulo').val())
            titulo=titulo==''?$('#titulo').attr('name'):titulo
            let indizacion=$('#indizacion').val()
            indizacion=indizacion==0?$('#indizacion').attr('name'):indizacion;            
            let issn=$('#issn').val()
            issn=issn==''?$('#issn').attr('name'):issn
            let fac_imp=$("#no_fac").is(':checked')?0:$('#fact_imp').val();
            Object.assign(publicacion,{titulo,indizacion,issn,fac_imp})
        }else{
            tipo_lib=$('#tipo_libro').val();
            rol_libro=$('#rol_libro').val();
            lugar=$('#lugar').val();
            ref_ext=$('input:radio[name=ref_ext]:checked').val();
            traduccion=$('input:radio[name=traduc]:checked').val();
            Object.assign(publicacion,{tipo_lib,rol_libro,ref_ext,traduccion,lugar})
    
        }
    
    }else{
        tipo_pub=$('#tipo_otra_pub').val()
        desc_pub=$('#desc_pub').val()
        publicacion={op,tipo_pub,tipo,desc_pub,id_pub}

    }
    ///console.log(publicacion)
    $.post('../ajax/publicacion.php',publicacion,function(response){
        let dato = JSON.parse(response);
        console.log(dato);
            $('#mnsj_row_pub').show();
            $('#mnsj_pub').removeClass('alert-danger');
            $('#mnsj_pub').addClass('alert-success');
            $('#mnsj_pub').html(dato);
            setTimeout(function() {
                reiniciarInfoDoc()
                $('#campos_publicacion').html('')
        $("#mnsj_row_pub").fadeOut(1500);
    },3000);
    })    
})
//validar factor de impacto
estado=$("#no_fac").is(':checked')
$(document).on('click',"#no_fac",function(){
        limpiarInput('#fact_imp') 
        if(estado){
            $(this).prop('checked',true)
            $('#fact_imp').prop('disabled',true)
            $('#fact_imp').val('')
            estado=false
        }else{
            $(this).prop('checked',false) 
            $('#fact_imp').prop('disabled',false)
            estado=true
        }
})
