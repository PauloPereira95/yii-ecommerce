<?php

use common\models\Order;
use common\models\OrderAddresses;
use yii\bootstrap5\ActiveForm;

/** @var Order $order */
/** @var OrderAddresses $orderAddress */
/** @var array $cartItems */
/** @var int $productQuantity */
/** @var float $totalPrice */
?>
<?php $form = ActiveForm::begin(
    [
        'id' => 'checkout-form',

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
                        <td class="text-end">
                            <?= Yii::$app->formatter->asCurrency($totalPrice); ?>
                        </td>
                    </tr>
                </table>

                <p class="d-flex justify-content-end mt-3">
                    <button class="btn btn-secondary">Checkout</button>
                </p>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

