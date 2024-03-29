<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%cart_items}}".
 *
 * @property int $id
 * @property int $product_id
 * @property int $quantity
 * @property int|null $created_by
 *
 * @property User $createdBy
 * @property Product $product
 */
class CartItem extends \yii\db\ActiveRecord
{
    const SESSION_KEY = 'CART_ITEMS';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%cart_items}}';
    }

    public static function getTotalQuantityForUser(?int $currentUserid)
    {
        if (isGuest()) {
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
            $sum = 0;
            foreach ($cartItems as $cartItem) {
                $sum += $cartItem['quantity'];
            }
        } else {
            $sum = CartItem::findBySql("SELECT SUM(quantity) FROM cart_items
        WHERE created_by = :userId", ['userId' => $currentUserid])->scalar();
        }
        return $sum;
    }
    public static function getTotalPriceForUser(?int $currUserid)
    {
        if (isGuest()) {
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
            $sum = 0;
            foreach ($cartItems as $cartItem) {
                $sum += $cartItem['quantity'] * $cartItem['price'];
            }
        } else {
            $sum = CartItem::findBySql("SELECT SUM(c.quantity * p.price) 
            FROM cart_items c
            left join  products p on p.id = c.product_id
        WHERE c.created_by = :userId", ['userId' => $currUserid])->scalar();
        }
        return $sum; 
    }
    public static function getItemsForUser(?int $currUserid)
    {
        // If user is not authorize / (Is Guestged)
        if (Yii::$app->user->isGuest) {
            // show products save in session if is empty show empty array
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
        } else {

            $cartItems = Cartitem::findBySql("SELECT 
    c.product_id as id, p.image, p.name, p.price, c.quantity, p.price * c.quantity AS total_price
    FROM cart_items c
    LEFT JOIN products p ON p.id = c.product_id
    WHERE c.created_by = :userId", ['userId' => $currUserid])
            ->asArray()->all();
        }
        return $cartItems;
    }
    public static function getTotalPriceProduct($product_id)
    {
        return CartItem::findBySql("SELECT c.quantity * p.price as total_price
        FROM cart_items c 
        INNER JOIN products p on c.product_id = p.id
        WHERE c.product_id = :product_id" , ['product_id' => $product_id])->scalar();
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'quantity'], 'required'],
            [['product_id', 'quantity', 'created_by'], 'integer'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'quantity' => 'Quantity',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\ProductQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\CartItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\CartItemQuery(get_called_class());
    }
    public static function clearCartitems($currentUserId)
    {
        if (isGuest()) {
            Yii::$app->session->remove(CartItem::SESSION_KEY);
        } else {
            CartItem::deleteAll(['created_by' => $currentUserId]);
        }
    }
}
