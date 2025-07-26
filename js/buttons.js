$(document).ready(function(){
    $('#btn_rec_volver').click(function(){
        location.href='index.php'
    })
    $('#btn_acad_alum').click(function(){
        location.href=ruta+'estudiante/ingr.dat.prog.php'
    })
    $('#btn_acad_prof').click(function(){
        location.href=ruta+'docente/ingr.dat.prog.php'
    })
    $('.login').click(function(){
        location.href='index.php'
    })
    $('.inicio_admin').click(function(){
        location.href=ruta+'admin/inicio.php'
    })
    $('.prog_prof_volver').click(function(){
        location.href=ruta+'docente/ingr.dat.acad.php'
    })
    $('.prog_alum_volver').click(function(){
        location.href=ruta+'estudiante/ingr.dat.acad.php'
    })
    $('#btn_perfil').click(function(){
        location.href='perfil.php'
    })
    $('.btn_edit_admin').click(function(){
        location.href='editar.php'
    })
    $('#editar_pass').click(function(){
        location.href=ruta+'ed.pass.php'
    })
    $('#btn_acad_prof').click(function(){
        location.href=ruta+'docente/ingr.dat.prog.php'
    })
    $('.inicio').click(function(){
        location.href=ruta+'index.php'
    })
    $('#form_ingreso').submit(function(){
        location.href=ruta+'form-doc/ingr.dat.pers.php'
        
    })
    $('#form_tipo').submit(function(){
        location.href=ruta+'form-doc/ingr.dat.pers.php'
        
    })
    
})
    
    





   
