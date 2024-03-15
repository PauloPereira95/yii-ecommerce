<?php
    /** @var \common\models\Order $order  */
$orderAddress = $order->orderAddresses;
?>
Order #<?= $order->id ?> summary ;

Account Information
    Firstname : <?= $order->firstname ?>
    Lastname : <?= $order->lastname ?><
    Email : <?= $order->email ?>

Address Information
    Address : <?= $orderAddress->address ?>
    City : <?= $orderAddress->city ?>
    State : <?= $orderAddress->state ?>
    Country : <?= $orderAddress->country ?>
    Zip-Code : <?= $orderAddress->zipcode ?>

Products
    Name    Image   Quantity    Total Price

<?php foreach ($order->orderItems as $item) : ?>
    <?= $item->product_name ?> <?= $item->quantity ?> <?= Yii::$app->formatter->asCurrency($item->quantity * $item->unit_price) ?>
<?php endforeach; ?>
Total Items : <?= $order->getItemsQuantity() ?>
Total PRice : <?= Yii::$app->formatter->asCurrency($order->total_price) ?>
