<?php

namespace frontend\controllers;

use common\models\CartItem;
use common\models\Order;
use common\models\OrderAddresses;
use common\models\Product;
use Faker\Provider\Payment;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use PayPalCheckoutSdk\Payments\AuthorizationsGetRequest;
use Sample\PayPalClient;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
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
                'only' => ['add', 'create-order','submit-payment'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    'application/xml' => Response::FORMAT_JSON,
                ]
            ],
            [
                'class' => VerbFilter::class,
                'actions' =>
                    [
                        'delete' => ['post', 'DELETE'],
                        'create-order' => ['post']
                    ]
            ]
        ];
    }

    public function actionIndex()
    {

        $cartItems = CartItem::getItemsForUser(currentUserid());

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
            Yii::$app->session->set(CartItem::SESSION_KEY, $cartItems);
        } else {
            //  common/currentUserid() function on helpers
            CartItem::deleteAll(['product_id' => $id, 'created_by' => currentUserid()]);
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
                if ($cartItem['id'] = $id) {
                    $cartItem['quantity'] = $quantity;
                    break;
                }
            }
            Yii::$app->session->set(CartItem::SESSION_KEY, $cartItems);
        } else {
            $cartItem = CartItem::find()->userId(currentUserid())->productId($id)->one();
            if ($cartItem) {
                $cartItem->quantity = $quantity;
                $cartItem->save();
            }
        }

        $cartQuantity = CartItem::getTotalQuantityForUser(currentUserid());
        // get total price for the product is modified
        $totalPrice = Yii::$app->formatter->asCurrency(CartItem::getTotalPriceProduct($id));
        //formatt return in to json for the ajax
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'totalQuantity' => $cartQuantity,
            'totalPrice' => $totalPrice,
        ];
    }

    public function actionCheckout()
    {
        $cartItems = CartItem::getItemsForUser(currentUserid());
        $productQuantity = CartItem::getTotalQuantityForUser(currentUserid());
        $totalPrice = CartItem::getTotalPriceForUser(currentUserid());
        // if the cart is empty redirect to homepage
        if (empty($cartItems)) {
            return $this->redirect([Yii::$app->homeUrl]);
        }
        $order = new Order();

        $order->total_price = $totalPrice;
        $order->status = Order::STATUS_DRAFT;
        $order->created_at = time();
        $order->created_by = currentUserid();
        $transaction = Yii::$app->db->beginTransaction();
        if ($order->load(Yii::$app->request->post())
            && $order->save()
            && $order->saveAddress(Yii::$app->request->post())
            && $order->saveOrderItems()) {
            $transaction->commit();

            CartItem::clearCartItems(currentUserid());
            return $this->render('pay-now', [
                'order' => $order
            ]);
        }
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

        }
        return $this->render('checkout', [
            'order' => $order,
            'orderAddress' => $orderAddress,
            'cartItems' => $cartItems,
            'productQuantity' => $productQuantity,
            'totalPrice' => $totalPrice
        ]);
    }

    public function actionSubmitPayment($orderId)
    {
        $where = ['id' => $orderId, 'status' => Order::STATUS_DRAFT];
        if (!isGuest()) {
            $where['created_by'] = currentUserid();
        }
        $order = Order::findOne($where);
        if (!$order) {
            throw new NotFoundHttpException();
        }
        $request = Yii::$app->request;
        $paypalOrderId = $request->post('orderID');
        // If someone send on transaction_id with one already exists on database
        $exists = Order::find()->andWhere(['paypal_order_id' => $paypalOrderId])->exists();
        if ($exists) {
            throw new BadRequestHttpException();
        }

        $environment = new SandboxEnvironment(Yii::$app->params['paypalClientId'], Yii::$app->params['paypalSecret']);
        $client = new PayPalHttpClient($environment);

        $response = $client->execute(new OrdersGetRequest($paypalOrderId));
        
        // if request made with success
        if($response->statusCode === 200){
        
            $order->paypal_order_id = $paypalOrderId;
            $paidAmount  = 0;
            foreach ($response->result->purchase_units as $purchase_unit) {
                //only alow purchase on USD Dolares
                if($purchase_unit->amount->currency_code === "USD") {
                    $paidAmount += $purchase_unit->amount->value;
                }
                if ($paidAmount === (float)$order->total_price && $response->result->status === 'COMPLETED') {
                    // set order  status to 1
                    // $order->status = Order::STATUS_COMPLETED;
                    $order->status = Order::STATUS_COMPLETED;
                }
                $order->transaction_id = $response->result->purchase_units[0]->payments->captures[0]->id;
                if ($order->save()) {
                    // if not send the email's
                    if(!$order->sendEmailToVendor()) {
                        Yii::error('Email to the Vendor is not Send !');
                    }
                    if(!$order->sendEmailToCustomer()) {
                        Yii::error('Email to the Customer is not Send !');
                    }
                    return [
                        'success' => true,
                       
                    ];

                } else {
                    Yii::error("Order was not saved : Data: ".VarDumper::dumpAsString($order->toArray()).'. Errors: '
                        . VarDumper::dumpAsString($order->errors));
                }
            }
        }
        throw new BadRequestHttpException();

    }
}