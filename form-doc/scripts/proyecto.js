//Agregar Proyecto Investigacion
$('#btn_proyecto').click(function () {
    $('#boton_proyecto').hide();
    $('#proyecto').append('<div class="card mb-3" id="ingresar_proyecto"><div class="card-body" id="card_proy"> </div></div>');
    $('#card_proy').append('<div class="row justify-content-between"><div class="col-auto mb-3"><h4 class="card-title">Proyecto de Investigación</h4></div><div class="col-auto"><button type="button"  id="close_proyecto"  class="btn btn-close btn-sm borrar_proyecto"></button></div></div>');
    $('#card_proy').append('<div class="row"><div class="col-md-6 mb-3"><label for="folio_proy" class="form-label">Folio</label><input type="text" class="form-control" id="folio_proy" name="0" maxlength="10" autocomplete="off"><ul class="list-group" id="list_proy"></ul></div><div class="col-md-3 mb-3"><label for="anio_adj" class="form-label">Año Adjudicación</label><select id="anio_adj" class="form-select proyec"><select/></div><div class="col-md-3 mb-3"><label for="dur_proy" class="form-label">Duración</label><div class="row"><div class="col-6"><input type="number" class="form-control" id="dur_proy"></div><div class="col-6 align-self-end"><label class="form-label">Meses</label></div></div></div><div class="col-12 mb-3"><label for="titulo_proy" class="form-label">Título</label><textarea row="1" class="form-control" id="titulo_proy" maxlength="300"></textarea></div></div>');
    $('#card_proy').append('<div class="row " id="rol_pro"><div class="col-md-6 mb-3"><label for="rol_proy" class="form-label">Rol</label><select id="rol_proy" class="form-select" name="0"><option selected value="0">Rol</option><option value="1">IR</option><option value="2">CO-I</option></select></div><div class="col-md-6" id="nombre_rol"><label class="form-label" id="label_rol"></label></div></div>');
    $('#card_proy').append('<div class="row" id="row_inst_proy"><div class="col-md-6 mb-3" id="id_financ" name="0"><label for="financ" class="form-label">Fuente de Financiamiento</label><select class="form-select" id="financ"></select></div><div class="col-md-6" id="id_inst_proy" name="0"></div><div class="col-md-6" id="otro_financ"></div><div class="col-md-6" id="otro_inst_proy"></div></div>');
    $('#card_proy').append(' <div class="row justify-content-center " id="mnsj_row_proy"><div class="col-lg-8 alert  text-center alert-danger" role="alert" id="mnsj_proy"></div></div>')
    $('#card_proy').append('<div class="row justify-content-center"><div class="col-md-6 mt-3 d-grid gap-2"><button type="submit" class="btn btn-dark">Guardar Proyecto</button></div></div>');
    $('#mnsj_row_proy').hide();
    anios('#anio_adj', 1960)

    $.post('../ajax/financiamiento.php', {op:'read'}, function (response) {
        data = JSON.parse(response);
        templateSelect(data,'#financ')
        })
    $('#folio_proy').keyup(function () {
        $('#list_proy').hide();
        busqueda = $('#folio_proy').val();
        if (busqueda !== '') {
            $.ajax({
                url: '../ajax/proyecto.php',
                type: 'POST',
                data: { op: 'read_proy', busqueda },
                success: function (response) {
                    let proyecto = JSON.parse(response);
                    let template = '';
                    //$('#prof_guia').attr('name','');
                    proyecto.forEach(list => {
                        template += `
                <li class='list-group-item listProy' id='${list[0]}' name="${list[1]}"> ${list[1]}</li>`
                    });
                    $('#list_proy').html(template);
                    $('#list_proy').show();
                }
            })

        }
    })

});
$('body').on('click', '.listProy', function () {
    id = $(this).attr('id');
    folio = $(this).attr('name');
    $('#folio_proy').attr('name', id);
    $('#list_proy').hide();
    console.log($('#folio_proy').attr('name'))
    if ($('#folio_proy').attr('name') !== 0) {
        $.ajax({
            async: false,
            url: '../ajax/proyecto.php',
            type: 'POST',
            data: { op: 'carg_proy', id },
            success: function (response) {
                let proy = JSON.parse(response);
                let template = '';
                if (proy[0]['id_inv'] == null || proy[0]['id_coinv'] == null) {
                    $('#folio_proy').val(folio);
                    $('#folio_proy').prop('disabled', true);
                    $('#anio_adj').val(proy[0]['anio_adjud']);
                    $('#anio_adj').prop('disabled', true);
                    $('#dur_proy').val(proy[0]['duracion']);
                    $('#dur_proy').prop('disabled', true);
                    $('#titulo_proy').val(proy[0]['titulo']);
                    $('#titulo_proy').prop('disabled', true);
                    $('#financ').val(proy[0]['fuente_financiamiento']);
                    $('#financ').prop('disabled', true);
                    $('#nombre_rol').append('<input type="text" class="form-control" id="input_rol" >')
                    $('#id_inst_proy').append('<label for="instituto" class="form-label">Institución Coinvestigador</label><select id="inst_proy" class="form-select"></select>')

                    $.post('../ajax/institucion.php', {op:'read'}, function (response) {
                        data = JSON.parse(response);
                        templateSelect(data,'#inst_proy')
                     })

                    if (proy[0]['id_inv'] == null) {
                        $('#rol_proy').val(1);
                        $('#rol_proy').prop('disabled', true);
                        $('#rol_proy').attr('name', proy[0]['id_proyecto']);
                        $('#label_rol').append('Coinvestigador/a')
                        $.ajax({
                            async: false,
                            type: 'POST',
                            url: '../ajax/proyecto.php',
                            data: { op: 'read-usu', usu: proy[0]['id_coinv'] },
                            success: function (response) {
                                let autor = JSON.parse(response);
                                nombre = cadenaMay(autor[0]['nombres']) + ' ' + cadenaMay(autor[0]['ap_pat']) + ' ' + cadenaMay(autor[0]['ap_mat'])
                                autor = nombre
                                $('#input_rol').val(autor);
                            }
                        })
                        $('#input_rol').prop('disabled', true);
                        $('#inst_proy').val(proy[0]['inst_proy']);
                        $('#inst_proy').prop('disabled', true);
                    } else {
                        $('#rol_proy').val(2);
                        $('#rol_proy').prop('disabled', true);
                        $('#rol_proy').attr('name', proy[0]['id_proyecto']);
                        $('#label_rol').append('Investigador/a')
                        $.ajax({
                            async: false,
                            type: 'POST',
                            url: '../ajax/proyecto.php',
                            data: { op: 'read-usu', usu: proy[0]['id_inv'] },
                            success: function (response) {
                                let autor = JSON.parse(response);
                                console.log('autores....' + autor)
                                nombre = cadenaMay(autor[0]['nombres']) + ' ' + cadenaMay(autor[0]['ap_pat']) + ' ' + cadenaMay(autor[0]['ap_mat'])
                                autor = nombre
                                $('#input_rol').val(autor);
                            }
                        })
                        $('#input_rol').prop('disabled', true);

                    }
                } else {
                    $('#list_proy').show();
                    $('#list_proy').html("<li class='list-group-item text-danger'>Este proyecto ya se encuentra registrado<li>")

                }


            }
        })
    }

})
//agregar campo autor proyecto inv
$(document).on('change', '#rol_proy', function () {
    $('#input_rol').remove();
    $('#label_rol').html('')
    $('#id_inst_proy').html('')
    if ($('#rol_proy').val() == '2') {
        $('#label_rol').append('Investigador/a')
        $('#nombre_rol').append('<input type="text" class="form-control" id="input_rol" >')
        $('#id_inst_proy').append('<label for="inst_proy" class="form-label">Institución</label><select id="inst_proy" class="form-select"></select>')
        $.post('../ajax/institucion.php', {op:'read'}, function (response) {
                        data = JSON.parse(response);
                        templateSelect(data,'#inst_proy')
                     })

    }
    if ($('#rol_proy').val() == '1') {
        $('#label_rol').append('Coinvestigador/a')
        $('#nombre_rol').append('<input type="text" class="form-control" id="input_rol" >')
    }
});
$(document).on('change', '#financ', function () {
    $('#input_financ').remove()
    otroCampo('input_financ', '#otro_financ', '#financ', 'Fuente de Financiamiento', '45')
    $('#id_financ').attr('name', 0)
})
$(document).on('change', '#inst_proy', function () {
    $('#input_inst_proy').remove()
    otroCampo('input_inst_proy', '#otro_inst_proy', '#inst_proy', 'Institución', '80')
    $('#id_inst_proy').attr('name', 0)
})
//Eliminar Proyecto Investigación
$(document).on('click', '.borrar_proyecto', function () {
    $('#ingresar_proyecto').remove();
    $('#boton_proyecto').show();
});
function cargarProy(usuario, id) {
    op = 'read'
    $.ajax({
        async: false,
        type: 'POST',
        url: '../ajax/proyecto.php',
        data: { op, usuario },
        success: function (response) {
            let proyecto = JSON.parse(response);
            let tabla = '';
            let con = 1
            proyecto.forEach(proy => {
                rol = proy['id_inv'] == usuario ? 'Investigador/a Resposable' : proy['id_coinv'] == usuario ? 'Coinvestigador/a' : ''
                tabla += `
          <div class="card mb-3 grado_card">
          <div class="card-body table-responsive">
          <table class="table table-striped" id="part">
          <thead>
          <tr>
          <th class="col-md-4 titulo_acad"><h5>PROYECTO</h5></th>
          <th class="col-md-8 ps-0"><button type="button" class="btn btn-link ps-1 link-secondary" id="${proy['id_proyecto']}">Editar Proyecto Investigación</button></th>
          </tr>
          </thead>
          <tbody>
          <tr>
          <td>Folio</td>
          <td>${proy['folio']}</td>
          </tr>
          <tr>
          <td>Título</td>
          <td>${letraMay(proy['titulo'])}</td>
          </tr>
          <tr>
          <td>Fuente de Fianciamiento</td>
          <td>${cadenaMay(proy['financiamiento'])}</td>
          </tr>
          <tr>
          <td>Duración</td>
          <td>${proy['duracion']} Meses</td>
          </tr> 
          <tr>
          <td>Rol</td>
          <td>${rol}</td>
          </tr>
          
        `
                con++;
                if (proy['id_inv'] == usuario) {
                    op = 'read-usu'
                    usu = proy['id_coinv']
                    rol = 'Investigador/a Resposable'
                    $.ajax({
                        async: false,
                        type: 'POST',
                        url: '../ajax/proyecto.php',
                        data: { op, usu },
                        success: function (response) {
                            let autor = JSON.parse(response);
                            if (proy['id_coinv'] == null) {
                                autor = cadenaMay(proy['nom_coinv'])
                            } else {
                                nombre = cadenaMay(autor[0]['nombres']) + ' ' + cadenaMay(autor[0]['ap_pat']) + ' ' + cadenaMay(autor[0]['ap_mat'])
                                autor = nombre
                            }

                            tabla += `<tr>
                  <td>Coinvestigador/a</td>
                  <td>${autor}</td>
                  </tr>`
                        }


                    })



                }
                if (proy['id_coinv'] == usuario) {
                    op = 'read-usu'
                    usu = proy['id_inv']
                    rol = 'Coinvestigador/a'
                    $.ajax({
                        async: false,
                        type: 'POST',
                        url: '../ajax/proyecto.php',
                        data: { op, usu },
                        success: function (response) {
                            let autor = JSON.parse(response);
                            if (proy['id_inv'] == null) {
                                autor = cadenaMay(proy['nom_inv'])
                            } else {
                                nombre = cadenaMay(autor[0]['nombres']) + ' ' + cadenaMay(autor[0]['ap_pat']) + ' ' + cadenaMay(autor[0]['ap_mat'])
                                autor = nombre
                            }

                            tabla += `<tr>
                            <td>Investigador/a Responsable</td>
                            <td>${autor}</td>
                            </tr>`
                        }
                    })
                    //institucion coinvestigador
                    console.log(proy['inst_proy'])
                    $.ajax({
                        async: false,
                        type: 'POST',
                        url: '../ajax/proyecto.php',
                        data: { op: 'read-inst', id_inst: proy['inst_proy'] },
                        success: function (response) {
                            let inst = JSON.parse(response);
                            console.log(inst)
                            tabla += `<tr>
                        <td>Institución</td>
                        <td>${cadenaMay(inst[0]['inst'])}</td>
                        </tr>`

                        }
                    })
                }

                tabla += `</tbody>
                </table>    
                </div>
                </div>`
            });
            $(id).append(tabla);
        }
    })
}
$('#form_proyecto').submit(function (e) {
    e.preventDefault();

    $('#folio_proy').click(function () { limpiarInput('#folio_proy'); })
    $('#titulo_proy').click(function () { limpiarInput('#titulo_proy'); })
    $('#anio_adj').click(function () { limpiarSelect('#anio_adj'); })
    $('#dur_proy').click(function () { limpiarInput('#dur_proy'); })
    $('#rol_proy').click(function () { limpiarSelect('#rol_proy'); })
    $('#inst_proy').click(function () { limpiarSelect('#inst_proy'); })
    $('#input_rol').click(function () { limpiarInput('#input_rol'); })
    $('#financ').click(function () { limpiarSelect('#financ'); })
    let folio = $('#folio_proy').val();
    let anio = $('#anio_adj').val();
    let duracion = $('#dur_proy').val();
    let titulo = $('#titulo_proy').val();
    let rol = $('#rol_proy').val();
    let nom_inv = rol == 2 ? $('#input_rol').val() : '';
    let nom_coinv = rol == 1 ? $('#input_rol').val() : '';
    let inv = rol == 1 ? $('#id_usuario').attr('name') : '';
    let coinv = rol == 2 ? $('#id_usuario').attr('name') : '';
    let inst = document.getElementById("inst_proy") == null ? '' : $('#inst_proy').val();
    inst = $('#inst_proy').val() == 'otro' ? $('#input_inst_proy').val() : inst;
    let financ = $('#financ').val() == 'otro' ? $('#input_financ').val() : $('#financ').val();
    console.log(inst)

    //agragar nuevo congreso y participacion(si no existe congreso)
    function validarProy() {
        if (financ == 0 || folio == '' || anio == 0 || duracion == '' || titulo == '' || rol == 0 || $('#inst_proy').val() == 0 || validCampoVacio('#input_financ') || $('#input_rol').val() == '') {
            console.log('entra al if');
            validCampoVacio('#folio_proy');
            validCampoVacio('#titulo_proy');
            validSelect('#anio_adj');
            validCampoVacio('#dur_proy');
            validSelect('#rol_proy');
            validSelect('#inst_proy');
            validSelect('#financ');
            validCampoVacio('#input_rol');
            $('#mnsj_row_proy').show();
            $('#mnsj_proy').html('Rellene todos los campos requeridos')
            // if($('#nom_cong').attr('name')!=0){
            // //limpiarCamposCong();
            // }
            return false;
        } else {
            return true;
        }
    }

    if (validarProy()) {
        nuevo_inst = $('#inst_proy').val() == 'otro' ? true : false;//agregar nuevo instituto
        nuevo_financ = $('#financ').val() == 'otro' ? true : false;//agregar nuevo financiamiento 
        if (nuevo_financ) {
            op = 'insert';
            $.ajax({
                async: false,
                type: 'POST',
                url: '../ajax/financiamiento.php',
                data: { op, financ },
                success: function (response) {
                    let dato = JSON.parse(response);
                    $('#id_financ').attr('name', dato);
                }
            })
        }
        if (nuevo_inst) {
            op = 'insert';
            $.ajax({
                async: false,
                type: 'POST',
                url: '../ajax/institucion.php',
                data: { op, nombre: inst },
                success: function (response) {
                    let dato = JSON.parse(response);
                    $('#id_inst_proy').attr('name', dato);
                }
            })
        }
        //obtener id de nueva fuente financiamiento e institucion
        financ = $('#id_financ').attr('name') != 0 ? $('#id_financ').attr('name') : financ
        inst = $('#id_inst_proy').attr('name') != 0 ? $('#id_inst_proy').attr('name') : inst


        if ($('#folio_proy').attr('name') == 0) {
            op = 'insert-update';
            proyecto = { folio, anio, duracion, titulo, nom_inv, nom_coinv, inv, coinv, inst, financ, op };
            console.log(proyecto)
            $.post('../ajax/proyecto.php', proyecto, function (response) {
                let dato = JSON.parse(response);
                $('#mnsj_row_proy').show();
                $('#mnsj_proy').removeClass('alert-danger');
                $('#mnsj_proy').addClass('alert-success');
                $('#mnsj_proy').html(dato);
                setTimeout(function () {
                    $('#ingresar_proyecto').remove();
                    $('#boton_proyecto').show();
                    cargarProy($('#id_usuario').attr('name'), '#proyecto_card')
                    $("#mnsj_row_proy").fadeOut(1500);
                }, 3000);
            })
        } else {

            id_proy = $('#folio_proy').attr('name');
            if (rol == 1) {
                op = 'update-inv';
                $.post('../ajax/proyecto.php', { inv, op, id_proy }, function (response) {
                    let dato = JSON.parse(response);
                    $('#mnsj_row_proy').show();
                    $('#mnsj_proy').removeClass('alert-danger');
                    $('#mnsj_proy').addClass('alert-success');
                    $('#mnsj_proy').html(dato);
                    setTimeout(function () {
                        $('#ingresar_proyecto').remove();
                        $('#boton_proyecto').show();
                        cargarProy($('#id_usuario').attr('name'), '#proyecto_card')
                        $("#mnsj_row_proy").fadeOut(1500);
                    }, 3000);
                })

            } else {
                op = 'update-coinv';
                $.post('../ajax/proyecto.php', { coinv, op, id_proy, inst }, function (response) {
                    let dato = JSON.parse(response);
                    $('#mnsj_row_proy').show();
                    $('#mnsj_proy').removeClass('alert-danger');
                    $('#mnsj_proy').addClass('alert-success');
                    $('#mnsj_proy').html(dato);
                    setTimeout(function () {
                        $('#ingresar_proyecto').remove();
                        $('#boton_proyecto').show();
                        cargarProy($('#id_usuario').attr('name'), '#proyecto_card')
                        $("#mnsj_row_proy").fadeOut(1500);
                    }, 3000);
                })
            }



        }

        //     }
    }
})





