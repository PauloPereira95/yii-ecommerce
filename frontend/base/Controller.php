<?php

namespace frontend\base;

use common\models\CartItem;
use Yii;

class Controller extends \yii\web\Controller
{
    public function beforeAction($action)
    {
        $this->view->params['cartItemsCount'] = CartItem::findBySql("SELECT SUM(quantity) FROM cart_items
        WHERE created_by = :userId", ['userId' => Yii::$app->user->id])->scalar();
        return parent::beforeAction($action);
    }
}