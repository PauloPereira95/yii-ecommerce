<?php

use yii\helpers\StringHelper;

/** @var $model Product */
?>
<div class="card h-100">
    <!-- Product image-->
    <img class="card-img-top" src="<?= $model->getImageUrl() ?>" alt="..." />
    <!-- Product details-->
    <div class="card-body p-4">
        <div class="text-center">
            <!-- Product name-->
            <h5 class="fw-bolder">
                <?= $model->name ?>
            </h5>
            <!-- Product price-->
            <?= Yii::$app->formatter->asCurrency($model->price) ?>
            <div class="card-text">
                <!-- Show max 30 caracters and remove tags like strong etc -->
                <?= $model->getShortDescription() ?>
            </div>
        </div>
    </div>
    <!-- Product actions-->
    <div class="card-footer text-end">
        <a href="#" class="btn btn-primary">Add to Cart</a>
    </div>
</div>