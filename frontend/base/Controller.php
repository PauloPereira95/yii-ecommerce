<?php

namespace frontend\base;

use common\models\CartItem;
use Yii;

class Controller extends \yii\web\Controller
{
    public function beforeAction($action)
    {

        $this->view->params['cartItemsCount'] = CartItem::getTotalQuantityForUser(currentUserid());
        return parent::beforeAction($action);
    }
}