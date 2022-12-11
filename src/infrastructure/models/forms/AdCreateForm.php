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
    public int $typeId = 0;

    /** @var string  */
    public string $description = '';

    /** @var string  */
    public string $email = '';

    /** @var null|int */
    public ?int $price = null;

    /** @var int[]  */
    public array $categories = [];

    public function rules(): array
    {
        return [
            [['name', 'images', 'typeId', 'description', 'email', 'price', 'categories'], 'required'],
            ['name', 'string', 'min' => 10, 'max' => 100],
            ['description', 'string', 'min' => 50, 'max' => 1000],
            ['categories', 'validateCategories'],
            ['email', 'email'],
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
            'typeId' => '',
            'description' => 'Описание',
            'email' => 'Эл. почта для связи',
            'price' => 'Цена',
            'categories' => 'Выбрать категорию публикации'
        ];
    }

    public function validateCategories($attribute, $params)
    {
        if (!$this->hasErrors()) {
            foreach ($this->categories as $categoryId) {
                if (!AdCategories::findOne($categoryId)) {
                    $this->addError($attribute, 'Такой категории не существует');
                }
            }
        }
    }
}