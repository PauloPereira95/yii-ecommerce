<?php

/** @var \yii\web\View $this */

/** @var string $content */

/** @var */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

$carItemsCount = $this->params['cartItemsCount'];
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>" class="h-100">

    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <?php $this->registerCsrfMetaTags() ?>
        <title>
            <?= Html::encode($this->title) ?>
        </title>
        <?php $this->head() ?>
    </head>

    <body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>

    <header>
        <?php
        NavBar::begin([
            'brandLabel' => Yii::$app->name,
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar navbar-expand-md navbar-dark bg-dark fixed-top',
            ],
        ]);
        $menuItems = [
            [
                'label' => 'Cart <span id="cart-quantity" class="badge bg-danger" >' . $carItemsCount . '</span>',
                'url' => ['/cart/index'],
                'encode' => false

            ],
        ];
        if (Yii::$app->user->isGuest) {
            $menuItems[] = ['label' => 'Signup', 'url' => ['/site/signup']];
        }
        if (Yii::$app->user->isGuest) {
            echo Html::tag('div', Html::a('Login', ['/site/login'], ['class' => ['btn btn-link login text-decoration-none']]), ['class' => ['d-flex']]);
        } else {
            $menuItems[] = [
                'label' => Yii::$app->user->identity->getDisplayName(),

                'items' => [
                    [
                        'label' => 'Profile',
                        'url' => ['/profile/index'],
                    ],
                    [
                        'label' => 'Logout',
                        'url' => ['/site/logout'],
                        'linkOptions' => [
                            'data-method' => 'post'
                        ],
                    ]
                ]
            ];
        }
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav ml-auto'],
            'items' => $menuItems,
        ]);
        NavBar::end();
        ?>
    </header>

    <main role="main" class="flex-shrink-0">
        <div class="container">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </main>

    <footer class="footer mt-auto py-3 text-muted">
        <div class="container">
            <p class="float-start">&copy;
                <?= Html::encode(Yii::$app->name) ?>
                <?= date('Y') ?>
            </p>
            <p class="float-end">
                <?= Yii::powered() ." e Paulo Pereira"?>
            </p>
        </div>
    </footer>

    <?php $this->endBody() ?>
    </body>

    </html>
<?php $this->endPage();
