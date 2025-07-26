<div class="row ">
    <div class="col-md-6 mb-3">
    <label for="nombres" class="form-label" >Nombres</label>
    <input type="text" class="form-control" id="nombres" name="nombres" maxlength="60">
    </div>
    <div class="col-md-6 mb-3">
    <label for="ap_pat" class="form-label" >Apellido Paterno</label>
    <input type="text" class="form-control" id="ap_pat" name="ap_pat" maxlength="45">
    <div class="row" id="val_ap_pat"></div>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
    <label for="ap_mat" class="form-label" >Apellido Materno</label>
    <input type="text" class="form-control" id="ap_mat" name="ap_mat" maxlength="45">
    <div class="row" id="val_ap_mat"></div>
    </div>
    <div class="col-md-6 mb-3">
    <label for="fech_nac" class="form-label" >Fecha de Nacimiento</label>
    <input type="date" class="form-control" id="fech_nac" name="fech_nac">
    <div class="row" id="val_fech_nac"></div>
    </div>
</div>
<div class="row ">
    <div class="col-md-6 mb-3">
    <label for="documento" class="form-label">Documento Identidad</label>
    <select id="documento" class="form-select" name="documento">
        <option selected value="1">Rut</option>
        <option value="2">Pasaporte</option>
    </select>
    </div>
    <div class="col-md-6 mb-3">
    <label for="nro_doc" class="form-label">Rut/Pasaporte</label>
    <input type="text" class="form-control" id="nro_doc" name="nro_doc" maxlength="45" placeholder="XXXXXXXX-X">
    
    </div>
</div>
<div class="row ">
    <div class="col-md-6 mb-3">
    <label for="pais_nac" class="form-label" >País de nacionalidad</label>
    <select id="pais_nac" class="form-select " name="pais_nac">
        
    </select>
    <div class="row" id="val_nac"></div>
</div>
<div class="col-md-6 mb-3">
<label for="genero" class="form-label" >Género</label>
<select id="genero" class="form-select pais" name="genero">
    <option selected value="0">Seleccione</option>
    <option value="1">Femenino</option>
    <option value="2">Masculino</option>
    <option value="3">No Binario</option>
    <option value="4">Otro</option>
    </select>
    <div class="row" id="val_gen"></div>
</div>
</div>
<div class="row " id="row_pueb">
<div class="col-md-6 mb-3">
<label  class="form-label mb-2">Pertenece a Pueblo Indígena</label>
<div class="row">
    <div class="col-auto">
    <div class="form-check   ms-2">
        <input type="radio" class="form-check-input pueblo" name="pueblo" id="si" value="si">
        <label for="si" class="form-check-label">Sí</label>
    </div>
    </div>
    <div class="col-auto">
    <div class="form-check  ms-2">
        <input type="radio" class="form-check-input pueblo" name="pueblo" id="no" value="no" checked>
        <input type="hidden" class="form-check-input pueblo" name="no_pert" value="0" checked>

        <label for="no" class="form-check-label">No</label>
    </div>
    </div>
</div>
</div>
</div>
<div class="row ">
<div class="col-md-6 mb-3">
    <label for="pais_res" class="form-label">País de Residencia</label>
    <select name="pais_res" id="pais_res" class="form-select">
        
    </select>
    <div class="row" id="val_pais"></div>
</div>
<div class="col-md-6 mb-3">
<label for="region" class="form-label">Región</label>
<input type="text" class="form-control" id="region" name="region" maxlength="45">
<div class="row" id="val_reg"></div>
</div>
</div>
<div class="row">
<div class="col-md-6 mb-3">
<label for="comuna" class="form-label">Comuna</label>
<input type="text" class="form-control" id="comuna" name="comuna" maxlength="45">
<div class="row" id="val_com"></div>
</div>
<div class="col-md-6 mb-3">
<label for="telefono" class="form-label">Teléfono</label>
<input type="text" class="form-control" id="telefono" name="telefono" maxlength="15">
<div class="row" id="val_tel"></div>
</div>
</div>
<div class="row">
<div class="col-md-12 mb-3">
<label for="direccion" class="form-label">Dirección</label>
<input type="text" class="form-control" id="direccion" name="direccion">
<div class="row" id="val_direc"></div>
</div>
</div>
<div class="row">
<div class="col ">
<div class="card mb-3">
    <div class="card-body">
    <div class="row ">
        <h4 class="titulo-card">Contacto de Emergencia</h4>
        <div class="col-md-6 mb-3">
        <label for="cont_em" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="cont_em" name="cont_em">
        <div class="row" id="val_cont"></div>
        </div>
        <div class="col-md-6 mb-3">
        <label for="tel_em" class="form-label">Teléfono</label>
        <input type="text" class="form-control" id="tel_em" name="tel_em">
        <div class="row" id="val_tel_cont"></div>
        </div>
    </div>
    </div>
</div>
</div>
</div>


<div class="row" id="row_correo">

<div class="col-md-6 mb-3">
<label for="correo" class="form-label">Correo Electrónico</label>
<input type="email" class="form-control" id="correo" name="correo">
</div> 
<div class="col-md-6 mb-3 mt-2">
    <input type="button"  class="btn btn-outline-dark mt-4" value="Cambiar Constraseña" id="btn_cambiar_pass">
</div>
</div>
<div class="row" id="box-pass">
<div class="col-md-6 mb-3">
<label for="pass" class="form-label">Contraseña</label>
<input type="password" class="form-control" id="pass" name="pass">
<div class="row" id="val_pass"></div>
</div>
<div class="col-md-6 mb-3">
<label for="pass2" class="form-label">Repetir Contraseña</label>
<input type="password" class="form-control" id="pass2" name="pass2">
<div class="row" id="val_pass2"></div>
</div>
</div>