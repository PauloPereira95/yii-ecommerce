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


<?php if (isset($success) && $success) : ?>
<div class="alert alert-success">
    You information was successfully updated
</div>
<?php endif ?>

<?php $form = ActiveForm::begin(
    [
        'action' => ['/site/update-account'],
        'options' =>['data-pjax' => 1]
    ]
); ?>
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
<button class="btn btn-primary">Update</button>

<?php ActiveForm::end(); ?>
