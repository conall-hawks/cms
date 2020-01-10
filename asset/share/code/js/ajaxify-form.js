function ajaxifyForm(form){

    /* Construct parameters string from a POSTed <ajaxForm>. */
    if(typeof form === "object"){
        ajaxForm = new FormData(form);
        for(var i = 0; i < form.length; i++){

            /* Skip non-<input> elements. */
            if(typeof form[i].matches !== "function" || !form[i].matches("input")) continue;

            /* Input required validation. */
            if(form[i].required && !form[i].value){
                form[i].style.transition = "";
                form[i].style.boxShadow  = "var(--box-shadow-dark), 0 0 2px red inset";
                form[i].style.color      = "red";
                setTimeout(function(){
                    try{focusInput(form[i])}catch(error){console.log(error)}
                    form[i].style.transition = "1s box-shadow, 1s color";
                    form[i].style.boxShadow  = "";
                    form[i].style.color      = "";
                }, 10);
                return false;
            }

            /* Input pattern validation. */
            if(form[i].pattern && !RegExp(form[i].pattern).test(form[i].value)){
                form[i].style.transition = "";
                form[i].style.boxShadow  = "var(--box-shadow-dark), 0 0 2px red inset";
                form[i].style.color      = "red";
                setTimeout(function(){
                    try{focusInput(form[i])}catch(error){console.log(error)}
                    form[i].style.transition = "1s box-shadow, 1s color";
                    form[i].style.boxShadow  = "";
                    form[i].style.color      = "";
                }, 10);
                return false;
            }

            /* Append file upload. */
            if(form[i].type === "file"){
                if(form[i].multiple){
                    for(var j = 0; j < form[i].files.length; j++){
                        ajaxForm.append(form[i].name + "_" + j, form[i].files[j]);
                    }
                }else{
                    ajaxForm.append(form[i].name, form[i].files[0]);
                }
            }

            /* Append parameter. */
            else{
                ajaxForm.append(form[i].name, form[i].value);
            }
        }
        return ajaxForm;
    }
    return false;
}
