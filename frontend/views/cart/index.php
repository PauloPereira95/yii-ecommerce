<?php
/** @var array $items */

use yii\helpers\Url;

?>
<div class="card">
    <div class="card-header">
        <h3>You cart Items</h3>
    </div>
    <div class="card-body p-0">
        <?php if(!empty($items)) : ?>
        <table class="table table-hover">
            <thead>
            <tr>
                <th>Product</th>
                <th>Image</th>
                <th>Unit Price</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($items as $item) :?>
                <tr data-id="<?= $item['id'] ?>" data-url="<?= Url::to(['/cart/change-quantity'])?>">
                    <td><?= $item['name'] ?></td>
                    <td>
                        <img src="<?= \common\models\Product::formatImageUrl($item['image']); ?>"
                             style="width: 100px;"
                             alt="<?= $item['name'] ?>"
                        >
                    </td>
                    <td><?= Yii::$app->formatter->asCurrency($item['price']); ?></td>
                    <td>
                        <!-- item-quantity is a class identifier  -->
                        <input type="number" min="1" value="<?= $item['quantity'] ?>" class="form-control item-quantity" style="width:80px;">
                    </td>
                    <td><?= Yii::$app->formatter->asCurrency($item['total_price']) ?></td>
                    <td>
                        <?= \yii\bootstrap5\Html::a('Delete ', ['cart/delete', 'id' => $item['id']],
                            [
                                'class' => 'btn btn-outline-danger btn-small',
                                'data-method' => 'post',
                                // alert to confirm
                                'data-confirm' => 'Are You sure do you want remove ?'

                            ]) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="card-body d-flex justify-content-end">
            <!--  Checkout button   -->
            <a href="<?= Url::to(['/cart/checkout']) ?>" class="btn btn-primary">Checkout</a>

        </div>
        <?php else : ?>
        <p class="text-muted text-center p-5">There are no items in the cart !</p>
        <?php endif;?>
    </div>
</div>
