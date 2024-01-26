<?php

use common\models\UserAddress;
use yii\bootstrap5\ActiveForm;
?>

<?php
/** @var UserAddress $userAddress */
\yii\widgets\Pjax::begin([
        'enablePushState' => false
]) ?>
<?php $addressForm = ActiveForm::begin([
    'action' => ['/site/update-address'],
    'options' => [
        'data-pjax' => 1
    ]
]); ?>
<?= $addressForm->field($userAddress, 'address') ?>
<?= $addressForm->field($userAddress, 'city') ?>
<?= $addressForm->field($userAddress, 'state') ?>
<?= $addressForm->field($userAddress, 'country') ?>
<?= $addressForm->field($userAddress, 'zipcode') ?>
    <button class="btn btn-primary">Update</button>
<?php ActiveForm::end() ?>
<?php \yii\widgets\Pjax::end() ?>