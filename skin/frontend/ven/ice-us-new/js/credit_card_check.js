jQuery(function($){

  function getCreditCardType(accountNumber){

    //start without knowing the credit card type
    var result = "unknown";

    //first check for MasterCard
    if (/^5[1-5]/.test(accountNumber)){
      result = "mastercard";
     //then check for Visa
    }else if (/^4/.test(accountNumber)){
      result = "visa";
    //then check for AmEx
    }  else if (/^3[47]/.test(accountNumber)){
      result = "american-express";
    // then check for discover
    }else if(/^6011/.test(accountNumber)){
      result = 'discover';
    }

    return result;
  }

  $('#checkout-payment-method-load').on('keyup', '#authorizenet_cc_number', function(e){
    var number = $(e.target).val();
    if(number.length >= 4){
      var cardType = getCreditCardType(number);
      if(cardType != 'unknown'){
        $('.credit-card-type:not(.card-type-'+cardType+')').fadeTo(200, 0.3);
        var cardCode = $('.credit-card-type.card-type-'+cardType).data('card-code');
        jQuery('#authorizenet_cc_type').val(cardCode);
      }
    }else{
      $('.credit-card-type').fadeTo(200, 1);
      jQuery('#authorizenet_cc_type').val("");

    }
  });

});
