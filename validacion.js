(function(){

    var form = document.getElementsByName("form")[0],
        elementos = form.elements,
        boton = document.getElementById("btn");

    var validNom = function(){
        if(form.nombres.value == 0){
            alert("Completa el campo nombre");
        }
    }; 
    var valid = function(){
        validNom();
    };

    form.addEventListener("submit", valid);

}())