<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "books".
 *
 * @property int $id
 * @property string|null $isbn
 * @property string|null $title
 * @property string|null $author
 * @property float|null $price
 * @property int|null $inventory
 */
class Book extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'books';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['price'], 'number'],
            [['inventory'], 'integer'],
            [['isbn'], 'string', 'max' => 13],
            [['title', 'author'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'isbn' => 'Isbn',
            'title' => 'Title',
            'author' => 'Author',
            'price' => 'Price',
            'inventory' => 'Inventory',
        ];
    }
}
