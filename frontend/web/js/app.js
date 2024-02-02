$(function() {
    const $cartQuantity = $('#cart-quantity');
    // button to add te product
   const  $addToCart = $('.btn-add-to-cart');
   // listen the event click on teh button
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
});