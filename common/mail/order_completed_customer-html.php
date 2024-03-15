<?php
    /** @var \common\models\Order $order  */
$orderAddress = $order->orderAddresses;
?>
<h3>Order # <?= $order->id ?> Summary : </h3>
<hr>
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
    </div>
</div>