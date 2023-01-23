<?php

declare(strict_types=1);

namespace omarinina\infrastructure\models\forms;

use omarinina\domain\models\ads\AdCategories;
use omarinina\domain\models\ads\AdTypes;
use yii\base\Model;
use yii\web\UploadedFile;

class AdCreateForm extends Model
{
    /** @var string  */
    public string $name = '';

    /** @var UploadedFile[] */
    public array $images = [];

    /** @var int  */
    public $typeId;

    /** @var string  */
    public string $description = '';

    /** @var string  */
    public string $email = '';

    /** @var int */
    public $price;

    /** @var int[]  */
    public $categories;

    public function rules(): array
    {
        return [
            [['name', 'images', 'typeId', 'description', 'email', 'price', 'categories'], 'required'],
            ['name', 'string', 'min' => 10, 'max' => 100],
            ['description', 'string', 'min' => 50, 'max' => 1000],
            ['categories', 'validateCategories'],
            ['email', 'email'],
            ['price', 'default'],
            ['price', 'integer', 'min' => 100],
            ['typeId', 'exist', 'targetClass' => AdTypes::class, 'targetAttribute' => ['typeId' => 'id']],
            ['images', 'image', 'maxFiles' => 10, 'extensions' => 'png, jpg', 'maxSize' => 5 * 1024 * 1024],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Название',
            'images' => 'Изображения',
            'typeId' => 'Тип объявления',
            'description' => 'Описание',
            'email' => 'Эл. почта для связи',
            'price' => 'Цена',
            'categories' => 'Категория публикации'
        ];
    }

    public function validateCategories($attribute, $params)
    {
        if (!$this->hasErrors() &&
            AdCategories::find()->where(['id' => $this->categories])->count() !== count(
                $this->categories
            )) {
                $this->addError($attribute, 'Выбранной категории(й) не существует');
        }
    }
}