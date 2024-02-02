<?php

namespace frontend\controllers;

use common\models\CartItem;
use common\models\Product;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CartController extends \frontend\base\Controller
{
    public function behaviors()
    {
        //return im json on add function
        return [
            [
                'class' => ContentNegotiator::class,
                'only' => ['add'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    'application/xml' => Response::FORMAT_JSON,
                ]
            ],
            [
                'class' => VerbFilter::class,
                'actions' =>
                [
                    'delete' => ['post' , 'DELETE']
                ]
            ]
        ];
    }

    public function actionIndex()
    {
        // If user is not authorize / (Is Guestged)
        if (Yii::$app->user->isGuest) {
            // show products save in session if is empty show empty array
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
        } else {
            $cartItems = Cartitem::findBySql("SELECT c.product_id as id, p.image, p.name, p.price, c.quantity, p.price * c.quantity AS total_price
FROM cart_items c
LEFT JOIN products p ON p.id = c.product_id
WHERE c.created_by = :userId", ['userId' => Yii::$app->user->id])
                ->asArray()->all();

        }
        return $this->render('index',
            [
                'items' => $cartItems
            ]
        );
    }

    public function actionAdd()
    {
        $id = Yii::$app->request->post('id');
        $product = Product::find()->id($id)->published()->one();
        if (!$product) {
            throw new notFoundHttpException('Product Does not Exists');
        }
        if (Yii::$app->user->isGuest) {
            // Savee in session

            // if already have products on session pass to $cartItems if pass empty array
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
            $found = false;
            foreach ($cartItems as &$cartItem) {
                if ($cartItem['id'] == $id) {
                    $cartItem['quantity']++;
                    $found = true;
                    break;
                }
            }
            //if not found create
            if (!$found) {
                $cartItem =
                    [
                        'id' => $id,
                        'name' => $product->name,
                        'image' => $product->image,
                        'price' => $product->price,
                        'quantity' => 1,
                        'total_price' => $product->price

                    ];
                //Add  $cartItem to  $cartItems
                $cartItems[] = $cartItem;
            }
            // save in session
            Yii::$app->session->set(CartItem::SESSION_KEY, $cartItems);
        } else {
            $userId = Yii::$app->user->id;
            $cartItem = CartItem::find()->userId($userId)->productId($id)->one();
            // if product on cart add quantity
            if ($cartItem) {
                $cartItem->quantity++;
            } else {
                $cartItem = new CartItem();
                $cartItem->product_id = $id;
                $cartItem->created_by = Yii::$app->user->id;
                $cartItem->quantity = 1;
            }
            if ($cartItem->save()) {
                return [
                    'success' => true
                ];
            } else {
                return [
                    'success' => false,
                    'errros' => $cartItem->errors
                ];
            }
        }
    }

    public function actionDelete($id)
    {
        // isGuest() function on common/helpers
        if (isGuest()) {
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
            foreach ($cartItems as $i => $cartItem) {
                if ($cartItem['id'] == $id) {
                    array_splice($cartItems, $i, 1);
                    break;
                }
            }
            Yii::$app->session->set(CartItem::SESSION_KEY,$cartItems);
        } else {
            //  common/currentUserid() function on helpers
            CartItem::deleteAll(['product_id' => $id , 'create_by' => currentUserid()]);
        }
        return $this->redirect(['index']);
    }

}