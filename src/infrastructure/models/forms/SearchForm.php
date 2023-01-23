<?php

declare(strict_types=1);

namespace omarinina\infrastructure\models\forms;

use yii\base\Model;

class SearchForm extends Model
{
    /** @var string  */
    public string $search = '';

    public function rules(): array
    {
        return [
            ['search', 'required'],
            ['search', 'match', 'pattern' => '/^[A-Za-zА-Яа-яЁё\s]{2,50}$/u'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'search' => '',
        ];
    }
}