<?php

use common\models\UserAddress;
use yii\bootstrap5\ActiveForm;
?>

<?php
/** @var UserAddress $userAddress */
\yii\widgets\Pjax::begin([
        'enablePushState' => false
]) ?>
<!-- If success as passed as show the div if not , not show the div-->
<?php if (isset($success) && $success): ?>
<div class="alert alert-success">
    Your Address was successfully updated
</div>
<?php endif; ?>
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