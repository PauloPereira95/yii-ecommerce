<?php
/** @var Order $order */

use common\models\Order;

$orderAddress = $order->orderAddresses;
?>
    <script src="https://www.paypal.com/sdk/js?client-id=AdFaWjriPYLCZVL5uPhX0h_IIDMcsf98AZaGsTNHP4Wt5tKZN-VIgqsAK8G-kbpY3prFYRsiTcA2aSAy"></script>
    <h3 class="mb-5">Order Summary : # <?= $order->id ?></h3>
<?php if (!empty($order)) : ?>
    <div class="row">
        <div class="col">
            <table class="table">
                <h5>Account Information</h5>
                <tr>
                    <th>Firstname</th>
                    <td class="text-end"><?= $order->firstname ?></td>
                </tr>
                <tr>
                    <th>Lastname</th>
                    <td class="text-end"><?= $order->lastname ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td class="text-end"><?= $order->email ?></td>
                </tr>
            </table>

        </div>
        <?php if (!empty($orderAddress)) : ?>
            <div class="col">
                <h5>Address Information</h5>
                <table class="table">
                    <tr>
                        <td>Address</td>
                        <td><?= $orderAddress->address ?></td>
                    </tr>
                    <tr>
                        <td>City</td>
                        <td><?= $orderAddress->city ?></td>
                    </tr>
                    <tr>
                        <td>State</td>
                        <td><?= $orderAddress->state ?></td>
                    </tr>
                    <tr>
                        <td>Country</td>
                        <td><?= $orderAddress->country ?></td>
                    </tr>
                    <?php if (!empty($orderAddress->zipcode)) : ?>
                        <tr>
                            <td>Zip-Code</td>
                            <td><?= $orderAddress->zipcode ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <div class="row mt-4">
        <div class="col">
            <div class="row">
                <div class="col">
                    <table class="table">
                        <h5>Products</h5>
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Image</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($order->orderItems as $item) : ?>
                            <tr>
                                <td><?= $item->product_name ?></td>
                                <td><img src="<?= $item->product->getImageUrl() ?>" alt="" style="width:50px; height: 50px;"></td>
                                <td><?= $item->quantity ?></>
                                <td><?= $item->quantity * $item->unit_price ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="col">

                    <table class="table" style="margin-top:32px;">
                        <tr>
                            <th>Total Items</th>
                            <td><?= $order->getItemsQUantity() ?></td>
                        </tr>
                        <tr>
                            <th>Total Price</th>
                            <td><?= YIi::$app->formatter->asCurrency($order->total_price) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div id="paypal-button-container"></div>
                </div>
                <div class="col"></div>
            </div>
        </div>
    </div>
    <script>
        paypal.Buttons({
            createOrder: function (data, actions) {
                // The function sets  up the details of the transaction , including the amount and line item details.
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: <?= $order->total_price?>
                        }
                    }]
                });
            },
            onApprove: function (data, actions) {
                // console.log(data, actions);
                //This function  captures teh funds from the transaction
                return actions.order.capture().then(function (details) {
                    console.warn(data);
                    console.log(details);
                    const $form = $('#checkout-form');
                    const $formData = $form.serializeArray();

                    $formData.push({
                        name: 'transactionId',
                        value: details.id
                    });
                    $formData.push({
                        name: 'orderId',
                        value: data.orderID
                    });
                    $formData.push({
                        name: 'status',
                        value: details.status,
                    })
                    $.ajax({
                        method: 'post',
                        url: '<?= \yii\helpers\Url::to(['/cart/submit-payment', 'orderId' => $order->id])?>',
                        data: data,
                        success: function (response) {
                            // if the purchase is completed show message and redirect to home page
                            alert("Thanks for your business");
                            window.location.href = "";
                        }
                    })
                    // This function shows a transaction success message to your browser
                    alert('Transaction completed ' + details.payer.name.given_name);
                });
            }
        }).render('#paypal-button-container');
    </script>
<?php endif; ?>