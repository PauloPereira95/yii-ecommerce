<?php

use common\models\Order;
use common\models\OrderAddresses;
use yii\bootstrap5\ActiveForm;

/** @var Order $order */
/** @var OrderAddresses $orderAddress */
/** @var array $cartItems */
/** @var int $productQuantity  */
/** @var float $totalPrice  */
?>
<script src="https://www.paypal.com/sdk/js?client-id=AdFaWjriPYLCZVL5uPhX0h_IIDMcsf98AZaGsTNHP4Wt5tKZN-VIgqsAK8G-kbpY3prFYRsiTcA2aSAy"></script>
<?php $form = ActiveForm::begin(
    [
        'action' => [''],
    ]); ?>
<div class="row">
    <div class="col">
        <div class="card mb-3">
            <div class="card-header">
               <h5>Account information</h5>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($order, 'firstname')->textInput(['autofocus' => true]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($order, 'lastname')->textInput(['autofocus' => true]) ?>
                    </div>

                </div>
                <?= $form->field($order, 'email')->textInput(['autofocus' => true]) ?>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                Address Information
            </div>
            <div class="card-body">
                <?= $form->field($orderAddress, 'address') ?>
                <?= $form->field($orderAddress, 'city') ?>
                <?= $form->field($orderAddress, 'state') ?>
                <?= $form->field($orderAddress, 'country') ?>
                <?= $form->field($orderAddress, 'zipcode') ?>
            </div>

        </div>

    </div>
    <div class="col">
        <div class="card">
            <div class="card-header">
                <h5>Order Summary</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td colspan="2"><?= $productQuantity ?> Products</td>
                    </tr>
                    <tr>
                        <td>Total Price</td>
                        <td class="text-right">
                            <?= Yii::$app->formatter->asCurrency($totalPrice); ?>
                        </td>
                    </tr>
                </table>
                <div id="paypal-button-container">

                </div>
<!--                <p class="d-flex justify-content-end mt-3">-->
<!--                    <button class="btn btn-secondary">Checkout</button>-->
<!--                </p>-->
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
<script>
    paypal.Buttons({
        createOrder : function (data , actions) {
            // The function sets  up the details of the transaction , including the amount and line item details.
            return actions.order.create({
              purchase_units : [{
                  amount : {
                      value : <?= $totalPrice?>
                  }
              }]
            });
        },
        onApprove : function (data,actions) {
            console.log(data,actions);
            //This function  captures teh funds from the transaction
            return actions.order.capture().then(function (details) {

               // This function shows a transaction success message to your browser
               alert('Transaction completed ' + details.payer.name.given_name);
            });
        }
    }).render('#paypal-button-container');
</script>
