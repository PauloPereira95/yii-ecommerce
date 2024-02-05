$(function() {
    const $cartQuantity = $('#cart-quantity');
    // button to add te product
   const  $addToCart = $('.btn-add-to-cart');
   // listen the event click on teh button
    const $itemQuantities = $('.item-quantity');
   $addToCart.click(event => {
       event.preventDefault();
       const $this = $(event.target);
       // selects the data-key of the closest parent
       const id = $this.closest('.product-item').data('key');
       console.log(id);
       $.ajax({
           method : 'post',
           url : $this.attr('href'),
           data:{id},
           success : function (){
               console.log(arguments);
               $cartQuantity.text(parseInt($cartQuantity.text() || 0)+1);
           }
       });
   })

    // list de event change on input
    $itemQuantities.change(event => {
    const $this = $(event.target);
    const $tr = $this.closest('tr');
    const id = $tr.data('id');
    $.ajax({
        method : 'post',
        url : $tr.data('url'),
        // $this.val() is the value of selected input
        data : {id, quantity :  $this.val()},
        // if success update te quantity field on cart/index
        success : function (totalQuantity) {
            $cartQuantity.text(totalQuantity);
        }

    })
    });
});

