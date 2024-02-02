<?php
/** @var array $items */

use yii\helpers\Url;

?>
<div class="card">
    <div class="card-header">
        <h3>You cart Items</h3>
    </div>
    <div class="card-body-p-0">
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
                <tr>
                    <td><?= $item['name'] ?></td>
                    <td>
                        <img src="<?= \common\models\Product::formatImageUrl($item['image']); ?>"
                             style="width: 100px;"
                             alt="<?= $item['name'] ?>"
                        >
                    </td>
                    <td><?= $item['price'] ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= $item['total_price'] ?></td>
                    <td>
                        <?= \yii\bootstrap5\Html::a('Delete ', ['cat/delete', 'id' => $item['id']],
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
    </div>
</div>
