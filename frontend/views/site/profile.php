<?php

use common\models\User;
use common\models\UserAddress;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/** @var User $user */
/** @var UserAddress $userAddress */
?>

<div class="row">
    <div class="col">
        <?php $addressForm = ActiveForm::begin(['id' => 'form-signup']); ?>
        <?= $addressForm->field($userAddress , 'address') ?>
        <?= $addressForm->field($userAddress , 'city') ?>
        <?= $addressForm->field($userAddress , 'state') ?>
        <?= $addressForm->field($userAddress , 'country') ?>
        <?= $addressForm->field($userAddress , 'zipcode') ?>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="col">
        <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($user, 'firstname')->textInput(['autofocus' => true]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($user, 'lastname')->textInput(['autofocus' => true]) ?>
            </div>

        </div>
        <?= $form->field($user, 'username')->textInput(['autofocus' => true]) ?>

        <?= $form->field($user, 'email') ?>

        <?= $form->field($user, 'password')->passwordInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Signup', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>