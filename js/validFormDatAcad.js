// validar imput factor de impacto
$(document).ready(function(){
  estado=false
  $(document).on('click','.no_tiene_art',function(){
    id_radio=$(this).attr("id")
    //estadoRadio(id_radio)
    i=id_radio.slice(10)  
    if(estado == false){
      
      $('#fact_imp_art'+i).prop('disabled',true)
      $('#fact_imp_art'+i).removeClass('is-invalid');
      $('#col_fact_imp_art'+i).remove();
      $(this).prop('checked',true)
      estado=true
    }else{
      $(this).prop('checked',false)
      estado=false
      $('#fact_imp_art'+i).prop('disabled',false)
    }
  })
  $(document).on('click','.no_tiene_rev',function(){
    id_radio=$(this).attr("id")
    i=id_radio.slice(10)  
    if(estado == false){
      $('#fact_imp_rev'+i).prop('disabled',true)
      $('#fact_imp_rev'+i).removeClass('is-invalid');
      $('#col_fact_imp_rev'+i).remove();
      $(this).prop('checked',true)
      estado=true
    }else{
      $(this).prop('checked',false)
      estado=false
      $('#fact_imp_rev'+i).prop('disabled',false)
    }
  })
  $('#btn_acad_alum').click(function(){
    //promedio
    validCampoVacio('#promedio','#val_prom','col_prom','su promedio de pregrado')
    //institución
    validSelect('#inst_unid','#val_inst','col_inst')
    //trabaja actualmente
    validRadio('#trabaja','#val_trab','v_trab','trab')
    //orientacion disciplinar
    validSelect('#orient','#val_orient','col_orient')
    //situacion ocupacional
    validCampoVacio('#sit_ocup','#val_sit_ocup','col_sit_ocup')
    // grado académico
    id_grado ="#ingresar_grado"
    //tipo grado
    validSelectDinamico('#grad_acad','#val_grad_acad','col_grad_acad',id_grado)
    //grado
    validSelectDinamico('#g_pre','#val_pre','col_g_pre',id_grado)
    validSelectDinamico('#g_post','#val_post','col_g_post',id_grado)
    //titulo grado
    validSelectDinamico('#s_pre','#val_pre_tit','col_s_pre',id_grado)
    validSelectDinamico('#s_post','#val_post_tit','col_s_pre',id_grado)
    //Institución
    validarCamposVacios('#inst_grado','#val_inst','col_inst_grado','una institución',id_grado)
    //validar publicaciones 
    id_pub="#ingresar_publicacion"
    validSelectDinamico('#pub','#val_publ','col_pub')
    //artículo
    validarInputSelDinamic('#tit_art',"#val_tit_art","col_tit_art",id_pub)
    validSelectDinamico("#anio_art","#val_anio_art","col_anio_art",id_pub)
    validarInputSelDinamic('#aut_art','#val_aut_art','col_aut_art',id_pub)
    validarInputSelDinamic("#nom_rev_art","#val_nom_rev","col_nom_rev_art",id_pub)
    validSelectDinamico('#ind_art','#val_ind_art','col_ind_art',id_pub)
    validSelectDinamico('#est_art','#val_est_art','col_est_art',id_pub)
    validarInputSelDinamic('#issn_art','#val_issn_art','col_issn_art',id_pub)
    validarCamposVaciosRadio('#fact_imp_art','#val_fact_imp_art','col_fact_imp_art','no_fac_art',id_pub)
    //revista
    
    validarInputSelDinamic('#tit_rev',"#val_tit_rev","col_tit_rev",id_pub)
    validSelectDinamico("#anio_rev","#val_anio_rev","col_anio_rev",id_pub)
    validarInputSelDinamic('#aut_rev','#val_aut_rev','col_aut_rev',id_pub)
    validarInputSelDinamic("#nom_rev_rev","#val_nom_rev","col_nom_rev",id_pub)
    validSelectDinamico('#ind_rev','#val_ind_rev','col_ind_rev',id_pub)
    validSelectDinamico('#est_rev','#val_est_rev','col_est_rev',id_pub)
    validarInputSelDinamic('#issn_rev','#val_issn_rev','col_issn_rev',id_pub)
    validarCamposVaciosRadio('#fact_imp_rev','#val_fact_imp_rev','col_fact_imp_rev','no_fac_rev',id_pub)
    //libro o capítulo
    validSelectDinamico('#tipo_libro','#val_tipo_libro','col_tipo_libro',id_pub)
    validSelectDinamico('#rol_libro','#val_rol_libro','col_rol_libro',id_pub)
    validarInputSelDinamic('#aut_libro','#val_aut_libro','col_aut_libro',id_pub)
    valRadioDinamic('#val_ref_ext','col_ref_ext','ref_ext',id_pub)
    valRadioDinamic('#val_traduc','col_traduc','traduc',id_pub)
    validarInputSelDinamic('#nom_libro','#val_nom_libro','col_nom_libro',id_pub)
    validSelectDinamico('#anio_libro','#val_anio_libro','col_anio_libro',id_pub)
    validarInputSelDinamic('#editorial','#val_editorial','col_editorial',id_pub)
    validarInputSelDinamic('#lug_lib','#val_lug_lib','col_lug_lib',id_pub)
    validSelectDinamico('#est_lib','#val_est_lib','col_est_lib',id_pub)
    //otra publicación
    validarInputSelDinamic('#tipo_pub','#val_tipo_pub','col_tipo_pub',id_pub)
    validarInputSelDinamic('#desc_pub','#val_desc_pub','col_desc_pub',id_pub)
    //validar congreso
    id_cong='#ingresar_congreso'
    validarInputSelDinamic('#nom_cong','#val_nom_cong','col_nom_con',id_cong)
    validarInputSelDinamic('#ciudad_cong','#val_ciudad_cong','col_ciudad_cong',id_cong)
    validarInputSelDinamic('#fech_in_cong','#val_fech_in_cong','col_fech_in_cong',id_cong)

    
  });


  //limpiar institucion/unidad
  $(document).on('change','#inst_unid',function(){
    limpiarSelect('#inst_unid','#col_inst')
  });
  //limpiar orientacion
  $(document).on('change','#orient',function(){
    limpiarSelect('#orient','#col_orient')
  });
  //limpiar trabaja actualmente
  $(document).on('click','.trabaja',function(){
    limpiarRadio('#val_trab')
    });
  //limpiar campos vacios
  $('#promedio').click(function(){
    limpiarInput('#promedio','#col_prom')
  })
  $('#sit_ocup').click(function(){
    limpiarInput('#sit_ocup','#col_sit_ocup')
  })
  
  //limpiar formularios dinamicos 
  //limpiar select dinamicos
  $(document).on('change','.form-select',function(){
    id=$(this).attr("id")
    limpiarSelectDinam(id,'#col_'+id)
  })
  //limpiar input dinamicos
  $(document).on('click','.form-control',function(){
     id=$(this).attr("id")
    limpiarInputDinamico(id,'#col_'+id)
  }) 
  //limpiar radio dinamicos
  $(document).on('click','.form-check-input',function(){
    nom=$(this).attr("name")
   limpiarRadioDinamic('#col_'+nom)
 })
  



    
  
  
   
 
 
 
 

 

  
     
    

    
    
      
    
   

    


    
    



      
      
      
    
  
  
  
    
  
      
      
  
  
      
    
    
  
    
});
    
    
    

     

   
        
         
   
  
      


      
      
        


      
    
    



    


