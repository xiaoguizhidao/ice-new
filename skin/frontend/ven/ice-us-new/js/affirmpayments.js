document.observe('checkout:billing:saved', function(){
    var request = new Ajax.Request(
        '/ice/ajax/affirmpaymentsenabled/',
        {
            method:'get',

            onSuccess: function(transport){
                var jsonResp = transport.responseText.evalJSON();
                // hide affirm payments if not available
                if(typeof jsonResp.affirmAvailable != 'undefined' && !jsonResp.affirmAvailable){
                    $('p_method_affirm').up().hide();
                }else{
                    $('p_method_affirm').up().show();
                }
            }
        }
    );
});