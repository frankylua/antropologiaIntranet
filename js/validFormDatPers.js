
function validarCamposVacios(){

}

function leerDatPers(){
  let nombres=$('#nombres').val()
    let ap_pat=$('#ap_pat').val();
    let ap_mat=$('#ap_mat').val();
    let fech_nac=$('#fech_nac').val();
    let documento=$('#doc_ident').val();
    let nro_doc=$('#nro_doc').val();
    let pais_nac=$('#nac').val();
    let genero=$('#genero').val();
    let pais_res=$('#pais_res').val();
    let region=$('#region').val();
    let comuna=$('#comuna').val();
    let telefono=$('#telefono').val();
    let direccion=$('#direccion').val();
    let cont_em=$('#cont_em').val();
    let tel_em=$('#tel_em').val();
    //datos tabla login
    let correo=$('#correo').val();
    let pass=$('#pass').val();
    let pass2=$('#pass2').val();
    //pueblo
    let pertenece_pueblo=$('input:radio[name=pueblo]:checked').val();
    let pueblo=null;
    if(pertenece_pueblo == 'si'){
        pueblo=$('#select_pueb').val();
        // dato tabla pueblo
        if(pueblo == 'otro'){
            pueblo=$('#pueb').val();
        }
    }
    
     if(pass == pass2){
         usuario={nombres,ap_pat,ap_mat,fech_nac,documento,nro_doc,pais_nac,genero,pais_res,region,comuna,telefono,direccion,cont_em,tel_em,correo,pass,pueblo}
         return usuario;
      }else{
          let mensaje= 'Las contraseñas no coinciden';
          return mensaje;
      }

}
$(document).ready(function(){

  // validar datos personales 
    $('#btn_pers').click(function(){ //boton guardar
      var inv;
      // validar tipo ingreso
      validSelect('#tipo_in','#val_tipo_in','col_tipo_in')
      //validar nacionalidad
      validSelect('#nac','#val_nac','col_nac')
      //validar pueblo
      validSelect('#select_pueb','#val_pueb','col_pueb')
      if($('#pueb').val() == ''){
        $('#pueb').addClass('is-invalid')
      }else{
        $('#valid_p').remove();
        $('#pueb').removeClass('is-invalid')
        $('#pueb').addClass('is-valid')
      }
      //validar genero
      validSelect('#genero','#val_gen','valid_gen')
      //validar pais residencia
      validSelect('#pais_res','#val_pais','valid_pa')
      //validar rellenar campos
      //nombres
      validCampoVacio('#nombres','#val_nom','col_nom')
      //apellido paterno
      validCampoVacio('#ap_pat','#val_ap_pat','col_ap_pat')
      //apellido materno
      validCampoVacio('#ap_mat','#val_ap_mat','col_ap_mat')
      //región
      validCampoVacio('#region','#val_reg','col_reg')
      //comuna
      validCampoVacio('#comuna','#val_com','col_com')
      //telefono
      validCampoVacio('#telefono','#val_tel','col_tel')
      //direccion
      validCampoVacio('#direccion','#val_direc','col_direc')
      //contacto emergencia
      validCampoVacio('#cont_em','#val_cont','col_cont')
      //telefono emergencia
      validCampoVacio('#tel_em','#val_tel_cont','col_tel_cont')
      //validar correo electronico
      if($('#correo').val() == ''){
        
        $('#corr').remove();
        $('#val_correo').append('<div class="col text-danger" id="corr">Este campo es obligatorio</div>');
        $('#correo').addClass('is-invalid');
      }else {
        $('#corr').remove();
        if($("#correo").val().indexOf('@', 0) == -1 || $("#correo").val().indexOf('.', 0) == -1){
          $('#c_inv').remove();
          $('#val_correo').append('<div class="col text-danger" id="c_inv">Ingrese un correo válido</div>');
          $('#correo').addClass('is-invalid');
        }else{
          $('#c_inv').remove();
          $('#correo ').removeClass('is-invalid');
          $('#correo ').addClass('is-valid');
        }
      }
      //validar contraseñas
      if($('#pass').val() == ''){
        $('#v_pass').remove();
        $('#val_pass').append('<div class="col text-danger" id="v_pass">Ingrese una contraseña</div>');
        $('#pass').addClass('is-invalid');
      }else {
        $('#v_pass').remove();
        $('#pass ').removeClass('is-invalid');
        $('#pass ').addClass('is-valid');
      }
      if($('#pass2').val() == ''){
        inv=0;
        $('#v_pass2').remove();
        $('#inv_pass2').remove();
        $('#val_pass2').append('<div class="col text-danger" id="v_pass2">Repita la contraseña</div>');
        $('#pass2').addClass('is-invalid');
      }else {
        if($('#pass').val() == $('#pass2').val()){
          $('#v_pass2').remove();
          $('#inv_pass2').remove();
          $('#pass2 ').removeClass('is-invalid');
          $('#pass2 ').addClass('is-valid');
         }else{
           $('#v_pass2').remove();
           $('#inv_pass2').remove();
           $('#val_pass2').append('<div class="col text-danger" id="inv_pass2">Las contraseñas no coinciden</div>');
           $('#pass2').addClass('is-invalid');
          }
        }
        //funcion para validar rut
        var Fn = {
          // Valida el rut con su cadena completa "XXXXXXXX-X"
          validaRut : function (rutCompleto) {
            if (!/^[0-9]+[-|‐]{1}[0-9kK]{1}$/.test( rutCompleto ))
            return false;
            var tmp 	= rutCompleto.split('-');
            var digv	= tmp[1]; 
            var rut 	= tmp[0];
            if ( digv == 'K' ) digv = 'k' ;
            return (Fn.dv(rut) == digv );
          },
          dv : function(T){
            var M=0,S=1;
            for(;T;T=Math.floor(T/10))
            S=(S+T%10*(9-M++%6))%11;
            return S?S-1:'k';
          }
        }
        // validar rut 
      
        

      
        
         

         



      
      
      
      if($('#nro_doc').val() ==''){
        inv=0;
        $('#n_d').remove();
        $('#n_p').remove();
        $('#invalid_rut').remove();
        $('#nro_doc').removeClass('is-invalid');
        
        if($('#doc_ident').val() == 1){
          $('#val_nro_doc').append('<div class="col-md-6 text-danger" id="n_d">Este campo es obligatorio</div>');
          $('#nro_doc').addClass('is-invalid');
        }else{

          $('#val_nro_doc').append('<div class="col-md-6 text-danger" id="n_p">Este campo es obligatorio</div>');
          $('#nro_doc').addClass('is-invalid');
        }
        
      }else{
        $('#n_d').remove();
        $('#nu_d').remove();
        $('#n_p').remove();
        $('#nro_doc').removeClass('is-invalid');
        $('#nro_doc').addClass('is-valid');
        if($('#doc_ident').val() == 1){
          var rut = $('#nro_doc').val();
          if(Fn.validaRut(rut)){
             inv = 1;
            $('#nro_doc').removeClass('is-invalid');
            $('#nro_doc').addClass('is-valid');
          }else{
            alert('olaaaaa')
            inv=0;
            $('#val_nro_doc').append('<div class="col-md-6 text-danger" id="n_d">Ingrese un rut valido</div>');
            $('#nro_doc').addClass('is-invalid');
          }
          
          
        }
        
      }
      
      if($('#tipo_in').val() == '1'){
        
        location.href='ingrDatAcademProf.php';
      }
      if($('#tipo_in').val() == '2'){
        location.href='ingrDatAcadAlum.php';
      }
    })
      
                  
                  
        
      //limpiar documento identidad
      $(document).on('change','#doc_ident',function(){
        $('#nro_doc').removeClass('is-valid');
        $('#n_d').remove();
        $('#n_p').remove();
        $('#nro_doc').removeClass('is-invalid');
      });

      
      //limpiar tipo_ingreso
      $(document).on('change','#tipo_in',function(){
        limpiarSelect('#tipo_in','#val_tipo_in')
      })
      // limpiar nacionalidad
      $(document).on('change','#nac',function(){
        limpiarSelect('#nac','#valid_nac')
      })
      // limpiar genero
      $(document).on('change','#genero',function(){
        limpiarSelect('#genero','#valid_gen')
      })
      // limpiar pueblo
      $(document).on('change','#select_pueb',function(){
        limpiarSelect('#select_pueb','val_pueb')
      })
      // limpiar pais
      $(document).on('change','#pais_res',function(){
        limpiarSelect('#pais_res','#valid_pa')
      })
      //limpiar campos vacios 
      $('#nombres').click(function(){
        limpiarInput('#nombres','#val_nom')
      })
      $('#ap_pat').click(function(){
        limpiarInput('#ap_pat','#val_ap_pat')
      })
      $('#ap_mat').click(function(){
        limpiarInput('#ap_mat','#val_ap_mat')
      })
      $('#nro_doc').click(function(){
        $('#nro_doc').removeClass('is-invalid');
        $('#nro_doc').removeClass('is-valid');
        $('#val_nro_doc').remove();
        
      })
      $('#comuna').click(function(){
        limpiarInput('#comuna','#val_com')
      })
      $('#direccion').click(function(){
        limpiarInput('#direccion','#val_direc')
      })
      $('#cont_em').click(function(){
        limpiarInput('#cont_em','#val_cont')
      })
      $('#tel_em').click(function(){
        limpiarInput('#tel_em','#val_tel_cont')
      })
      $('#correo').click(function(){
        limpiarInput('#correo','#val_correo')
      })
      $('#pass').click(function(){
        limpiarInput('#pass','#val_pass')
      })
      $('#pass2').click(function(){
        limpiarInput('#pass2','#val_pass2')
      })
      $('#region').click(function(){
        limpiarInput('#region','#val_reg')
      })
      $('#telefono').click(function(){
        limpiarInput('#telefono','#val_tel')
      })

    })

       
  
        
        
 
      

  
      

     


      

        

      

    

    

      
     
     
     
     
        

     

     
        

     
      
      
        
      
        





        
        
        
        
        
          
          
         
    

        
    
          
    
  