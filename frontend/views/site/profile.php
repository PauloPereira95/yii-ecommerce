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
                <?php \yii\widgets\Pjax::begin([
                    'enablePushState' => false,
                ]) ?>
                <?= $this->render('user_address', [
                    'userAddress' => $userAddress
                ]) ?>
                <?php \yii\widgets\Pjax::end() ?>
            </div>

        </div>

    </div>
    <div class="col">
        <div class="card">
            <div class="card-header">
                Account information
            </div>
            <div class="card-body">
                <?php \yii\widgets\Pjax::begin([
                    'enablePushState' => false,
                ]) ?>
                <?= $this->render('user_account', [
                    'user' => $user
                ]) ?>
                <?php \yii\widgets\Pjax::end() ?>

            </div>
        </div>
    </div>
</div>