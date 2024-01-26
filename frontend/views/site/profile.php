<?php

use common\models\User;
use common\models\UserAddress;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\web\View;

/** @var User $user */
/** @var UserAddress $userAddress */
/** @var View $this */
?>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                Address Information
            </div>
            <div class="card-body">
               <?= $this->render('user_address' , [
                       'userAddress' => $userAddress
               ]) ?>
            </div>

        </div>

    </div>
    <div class="col">
        <div class="card">
            <div class="card-header">
                Account information
            </div>
            <div class="card-body">
                <?php $form = ActiveForm::begin(); ?>
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
                <div class="row">
                    <div class="col">
                        <?= $form->field($user, 'password')->passwordInput() ?>
                    </div>
                    <div class="col">
                        <?= $form->field($user, 'passwordConfirm')->passwordInput() ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= Html::submitButton('Update', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>