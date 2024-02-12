<?php

namespace frontend\controllers;

use common\models\CartItem;
use common\models\Order;
use common\models\OrderAddresses;
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
            $cartItems = CartItem::getItemsForUser(currentUserid());
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
            // Save in session

            // if already have products on session pass to $cartItems if pass empty array
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
            $found = false;
            foreach ($cartItems as &$item) {
                if ($item['id'] == $id) {
                    $item['quantity']++;
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
            CartItem::deleteAll(['product_id' => $id , 'created_by' => currentUserid()]);
        }
        return $this->redirect(['index']);
    }
    public function actionChangeQuantity()
    {
        $id = \Yii::$app->request->post('id');
        $product = Product::find()->id($id)->published()->one();
        if (!$product) {
            throw new NotFoundHttpException("Product does not exist");
        }
        $quantity = \Yii::$app->request->post('quantity');
        if (isGuest()) {
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
            foreach ($cartItems as &$cartItem) {
                if($cartItem['id'] = $id ) {
                    $cartItem['quantity'] = $quantity;
                    break;
                }
            }
            Yii::$app->session->set(CartItem::SESSION_KEY , $cartItems);
        } else {
            $cartItem = CartItem::find()->userId(currentUserid())->productId($id)->one();
            if ($cartItem) {
                $cartItem->quantity = $quantity;
                $cartItem->save();
            }
        }

        return CartItem::getTotalQuantityForUser(currentUserid());
    }
    public function actionCheckout()
    {

        $order = new Order();
        $orderAddress = new OrderAddresses();

        if (!isGuest()) {
            // give the current user
            $user = Yii::$app->user->identity;
            $userAddress = $user->getAddress();

            $order->firstname = $user->firstname;
            $order->lastname = $user->lastname;
            $order->email = $user->email;
            $order->status = Order::STATUS_DRAFT;


            $orderAddress->address = $userAddress->address;
            $orderAddress->city = $userAddress->city;
            $orderAddress->state = $userAddress->state;
            $orderAddress->country = $userAddress->country;
            $orderAddress->zipcode = $userAddress->zipcode;

            $cartItems = CartItem::getItemsForUser(currentUserid());
        } else {
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY,[]);
        }
        $productQuantity = CartItem::getTotalQuantityForUser(currentUserid());
        $totalPrice = CartItem::getTotalPriceForUser(currentUserid());
        return $this->render('checkout', [
            'order' => $order,
            'orderAddress' => $orderAddress,
            'cartItems' => $cartItems,
            'productQuantity' => $productQuantity,
            'totalPrice' => $totalPrice
        ]);
    }

}