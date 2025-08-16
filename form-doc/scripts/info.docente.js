function cargarFichaDoc() {
    $.ajax({
        async: false,
        url: "../ajax/docente.php",
        type: "POST",
        data: { op: "read_prof_perfil" },
        success: function (response) {
            console.log(response)
            let usu = JSON.parse(response);
            let template;
            nombre =
                cadenaMay(usu[0]["nombres"]) +
                " " +
                letraMay(usu[0]["ap_pat"]) +
                " " +
                letraMay(usu[0]["ap_mat"]);
            cat_acad =
                usu[0]["cat_academica"] == 1
                    ? "Profesor(a) instructor(a)"
                    : usu[0]["cat_academica"] == 2
                        ? "Profesor(a) asistente"
                        : usu[0]["cat_academica"] == 3
                            ? "Profesor(a) asociado(a)"
                            : usu[0]["cat_academica"] == 4
                                ? "Profesor(a) titular"
                                : "Sin Jerarquía";
            $("#info_doc").attr("name", usu[0]["id_usuario"]);
            $("#id_usuario").attr("name", usu[0]["id_usuario"]);
            template += `
        <tr>
        <td class="col-4" ><h4 class=" mt-3">ANTECEDENTES PERSONALES</h4></td>
        <td class="col-8 ps-0"><h4 class=" mt-3"><button type="button" class="btn btn-dark btn-sm  ps-1 editarPersDoc" id="${usu[0]["id_usuario"]
                }">Editar Datos Personales</button></h4></td>
        </tr>
        <tr>
        <td class="col-4" >Nombre</td>
        <td class="col-8">${nombre}</td>
        </tr>
        <tr>
        <td class="col-4" >País Nacionalidad</td>
        <td class="col-8">${letraMay(usu[0]["p_nac"])}</td>
        </tr>
        <td class="col-4" >${usu[0]["tipo_doc"] == 1 ? "Rut" : "Pasaporte"}</td>
        <td class="col-8">${usu[0]["nro_doc"]}</td>
        </tr>
        <tr>
        <td class="col-4" >fecha de Nacimiento</td>
        <td class="col-8">${mostrarFecha(usu[0]["fecha_nac"])}</td>
        </tr>
        <tr>
        <td class="col-4" >Género</td>
        <td class="col-8">${usu[0]["genero"] == 1
                    ? "Femenino"
                    : usu[0]["genero"] == 2
                        ? "Masculino"
                        : usu[0]["genero"] == 3
                            ? "No Binario"
                            : "Otro"
                }</td>
        </tr>
        <tr>
        <td class="col-4" >Pueblo Indígena</td>
        <td class="col-8">${letraMay(usu[0]["pueblo"])}</td>
        </tr>
        <tr>
        <td class="col-4"><h5 class="mt-3">DATOS DE CONTACTO</h5></td>
        <td class="col-8"></td>
        </tr>
        <tr>
        <td class="col-4">Correo Electrónico</td>
        <td class="col-8">${usu[0]["correo"]}</td>
        </tr>
        <tr>
        <td class="col-4">Dirección</td>
        <td class="col-8">${cadenaMay(usu[0]["direccion"])}</td>
        </tr>
        <tr>
        <td class="col-4">Comuna</td>
        <td class="col-8">${cadenaMay(usu[0]["comuna"])}</td>
        </tr>
        <tr>
        <td class="col-4">Región</td>
        <td class="col-8">${cadenaMay(usu[0]["region"])}</td>
        </tr>
        <tr>
        <td class="col-4" >País Residencia</td>
        <td class="col-8">${letraMay(usu[0]["p_resi"])}</td>
        </tr>
        <tr>
        <td class="col-4">Teléfono</td>
        <td class="col-8">${usu[0]["telefono"]}</td>
        </tr>
        <tr>
        <td class="col-4">Contacto de Emergencia</td>
        <td class="col-8">${cadenaMay(usu[0]["cont_em"])}</td>
        </tr>
        <tr>
        <td class="col-4">Teléfono Contacto Emergencia</td>
        <td class="col-8">${usu[0]["tel_em"]}</td>
        </tr>
        <tr>
        <td class="col-4"><h4 class="mt-3">ANTECEDENTES PROGRAMA</h4></td>
        <td class="col-8 ps-0"><h4 class="mt-3"><button type="button" class="btn btn-dark ps-1 btn-sm   editarProgDoc text-center" id="${usu[0]["id_usuario"]
                }">Editar Datos Programa</button></h4></td>
        </tr>
        <tr>
        <td class="col-4">Institución/Unidad Académica</td>
        <td class="col-8">${cadenaMay(usu[0]["inst"])}</td>
        </tr>
        <tr>
        <td class="col-4">Vínculo con el Programa</td>
        <td class="col-8">${usu["vinculo"] == 1
                    ? "Claustro"
                    : usu["vinculo"] == 2
                        ? "Visitante"
                        : "Colaborador/a"
                }</td>
        </tr>
        <tr>
        <td class="col-4">Categoría Académica</td>
        <td class="col-8">${cat_acad}</td>
        </tr>
        <tr>
        <td class="col-4">Año de Ingreso</td>
        <td class="col-8">${usu[0]["anio_ingreso"]}</td>
        </tr>
     
        `;
            template += `<tr>
        <td class="col-4">Linea(s) de Investigación</td>
        <td class="col-8">
        <ul class="list-group list-group-flush">
        `
            usu.forEach(usuario => {
                template += `
          <li class="list-group-item">${usuario['linea']}</li>
        `
            });
            template += `</ul></td></tr>`
            $("#datos_fichadoc").html(template);
        },
    });
    $("#ant_acad_doc").html('')
    $("#ant_acad_doc").append('<div class="row m-2"><div class="col-md-4 m-0 p-2 ps-0 ms-0 mt-2 "><h4 class="text-left m-0 p-0">ANTECEDENTES ACADÉMICOS</h4></div><div class="col-md-8 m-0 p-2"><h4><button type="button" class="btn btn-dark btn-sm text-center " id="agrAcadDoc">Agregar Datos Académicos</button></h4></div></div>'
    );
    $("#ficha_grado").html('')
    $('#ficha_postdoc').html('')
    $('#ficha_publicacion').html('')
    $('#ficha_congreso').html('')
    $('#ficha_proyecto').html('')
    $('#ficha_pasantia').html('')
    $('#ficha_tesis').html('')
    cargarGrado($("#info_doc").attr("name"), "#ficha_grado");
    cargarPostdoc($("#info_doc").attr("name"), "#ficha_postdoc");
    cargarPub($("#info_doc").attr("name"), "#ficha_publicacion");
    cargarCong($("#info_doc").attr("name"), "#ficha_congreso");
    cargarProy($("#info_doc").attr("name"), "#ficha_proyecto");
    cargarPasantia($("#info_doc").attr("name"), "#ficha_pasantia");
    cargarTesis($("#info_doc").attr("name"), "#ficha_tesis");
    $('.titulo_acad').html('')
}
$("body").on("click", ".verFicha", function () {
    fichaDoc();
    cargarFichaDoc();
    //reinicio los campos para editar antecedentes academicos
    $('#campos_grado').html('')
    $('#campos_postdoc').html('')
    $('#campos_publicacion').html('')
    $('#campos_congreso').html('')
    $('#campos_pasantia').html('')
    $('#campos_tesis').html('')
});
function init() {
    fichaDoc()
    $('#login').attr('name',)
    cargarFichaDoc()
    $('#vol-infodoc').hide()
    $("#box-pass").hide();
    $("#btn_cambiar_pass").hide();
    $("#mnsj_row_per").hide();
    $("#mnsj_row_per_doc").hide();
    $("#mnsj_row_prog_doc").hide();
    $("#mnsj_row_acad_doc").hide();
    $('.loadPage').fadeOut();
    

}
init()
