<?php

declare(strict_types=1);

namespace omarinina\domain\models\ads;

use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\StaleObjectException;

/**
 * This is the model class for table "images".
 *
 * @property int $id
 * @property string $imageSrc
 *
 * @property AdsToImages[] $adsToImages
 * @property Ads[] $ads
 */
class Images extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'images';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['imageSrc'], 'required'],
            [['imageSrc'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'imageSrc' => 'Image Src',
        ];
    }

    /**
     * Gets query for [[AdsToImages]].
     *
     * @return ActiveQuery
     */
    public function getAdsToImages(): ActiveQuery
    {
        return $this->hasMany(AdsToImages::class, ['imageId' => 'id']);
    }

    /**
     * Gets query for [[Ads]].
     *
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getAds(): ActiveQuery
    {
        return $this->hasMany(Ads::class, ['id' => 'adId'])
            ->viaTable('adsToImages', ['imageId' => 'id']);
    }

    /**
     * @return void
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function deleteImage(): void
    {
        $this->delete();
    }
}
