<?php
use yii\widgets\ListView;
use yii\bootstrap5\LinkPager;



/** @var yii\web\View $this */
/** @var  yii\data\ActiveDataProvider $dataProvider  */
$this->title = 'My Yii Application';
?>
<div class="site-index">
    <div class="body-content">
        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'layout' => '{summary}<div class="row">{items}</div>{pager}',
            'itemView' => '_product_item',
            // 'options' => [
            //     'class' => 'row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center'
            // ],
            'itemOptions' => [
                'class' => 'col-lg-4 col-md-6 product-item'
            ],
            'pager' => ['class' => LinkPager::class, 'pagination' => $dataProvider->pagination]
        ]) ?>

    </div>
</div>