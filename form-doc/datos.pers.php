<?php
    ob_start();
    if (strlen(session_id()) < 1){
        session_start();//Validamos si existe o no la sesión
    }
 
   require('header.php');
   ?>
   <span class="loadPage" id="loadPageEst">
      <img src="../img/loadPage.gif" alt="" width="20%" height="20%">
  </span>
   
<div class="container-md mt-5">

    <div class="row">

        <div class="col" id="info_pers">
            
          <h3 class="mb-5 text-center" id="text-tit">INFORMACIÓN PERSONAL</h3>
          <form class="row g-3 " id="form_usuario" name="form_usuario" method='post' >
          
            <?php
            require('agr.form.dat.pers.php');
            ?>

       <div class="row justify-content-center " id="mnsj_row_per">
        <div class="col-lg-8 alert  mt-3 text-center alert-danger" role="alert" id="mnsj_per">
        </div>
      </div>

         <div class="row mt-5 justify-content-between ">
           <div class="col-md-4 mb-3">
             <button type="button" class="col-12 btn btn-dark col-6" id="volver_index">Volver</button>
           </div>
           <div class="col-md-4 mb-3" id="button_datos_pers">
             <button type="button" id="btn_pers" name="btn_pers" class="col-12 btn btn-dark col-6">Siguiente</button>
           </div>
         </div>
       
      </div>
    </div>
</div>
</div>
<?php

ob_end_flush();
?>




                    
                    

                  
                  
                  
                  
                  
                    
                      
                      

                          
                        

                      

                    
                            
                    
                    
                            
                    
                    
                          
                    

                    
                    
                   

                

                     
                     
                    


                    

                    


  
                


    









































































































































































































    
                        
                

                        
               
                
                
                

                
                

               


  



                
  



  
  
  




                    
                      

                  

                    
                    
                    
                   
                  
                  
                



                    
                  
                
               
                  
                  
                  

                
                          