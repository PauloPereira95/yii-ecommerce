<?php

namespace common\models;

use Yii;
use yii\base\Behavior;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use yii\helpers\StringHelper;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%products}}".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $image
 * @property float $price
 * @property int $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property CartItems[] $cartItems
 * @property User $createdBy
 * @property OrderItems[] $orderItems
 * @property User $updatedBy
 */
class Product extends \yii\db\ActiveRecord
{
    /** 
     * @var UploadedFile $uploadedFile
     * 
     */
    public $imageFile;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%products}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'price', 'status'], 'required'],
            [['description'], 'string'],
            [['price'], 'number'],
            [['imageFile'], 'image', 'extensions' => 'png,jpg,jpeg,webp', 'maxSize' => 10 * 1024 * 1024], // set max size image to 10 MB
            [['status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['image'], 'string', 'max' => 2000],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'image' => 'Product Image',
            'imageFile' => 'Product Image ',
            'price' => 'Price',
            'status' => 'Published',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }
    public function behaviors(){
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ] ;
    }
    /**
     * Gets query for [[CartItems]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\CartItemsQuery
     */
    public function getCartItems()
    {
        return $this->hasMany(CartItems::class, ['product_id' => 'id']);
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
     * Gets query for [[OrderItems]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\OrderItemsQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItems::class, ['product_id' => 'id']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\ProductQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ProductQuery(get_called_class());
    }
    public function save($runValidation = true, $attributeNames = null)
    {

        if ($this->imageFile) {
            // full path
            // $this->image = Yii::getAlias('@frontend/web/storage/products/'.Yii::$app->security->generateRandomString(255).'/'. $this->imageFile->name);
            // path relativo
            $this->image = '/products/' . Yii::$app->security->generateRandomString() . '/' . $this->imageFile->name;
        }
        $transaction = Yii::$app->db->beginTransaction();
        $ok = parent::save($runValidation, $attributeNames);
        if ($ok && $this->imageFile) {
            $fullPath = Yii::getAlias('@frontend/web/storage' . $this->image);
            $dir = dirname($fullPath);
            // if directory is not created and image is not saved rollback operation and return false
            if (!FileHelper::createDirectory($dir, 0777, true) | !$this->imageFile->saveAs($fullPath)) {
                $transaction->rollBack();
                return false;
            }
        }
        $transaction->commit();
        return $ok;

    }
    public function getImageUrl(){
        return self::formatImageUrl($this->image);
    }
    public static function formatImageUrl($imagePath){

         return $imagePath ?  Yii::$app->params['frontendUrl'].'/storage'.$imagePath :  Yii::$app->params['frontendUrl'].'/img/noimage.png' ;
    }
    public function getShortDescription(){
        return StringHelper::truncateWords(strip_tags($this->description),30);
    }
}