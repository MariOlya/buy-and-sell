<?php

declare(strict_types=1);

namespace omarinina\domain\models\ads;

use yii\db\ActiveQuery;
use yii\db\StaleObjectException;

/**
 * This is the model class for table "AdsToImages".
 *
 * @property int $id
 * @property int $imageId
 * @property int $adId
 *
 * @property Ads $ad
 * @property Images $image
 */
class AdsToImages extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'AdsToImages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['imageId', 'adId'], 'required'],
            [['imageId', 'adId'], 'integer'],
            [['imageId'], 'exist', 'skipOnError' => true, 'targetClass' => Images::class, 'targetAttribute' => ['imageId' => 'id']],
            [['adId'], 'exist', 'skipOnError' => true, 'targetClass' => Ads::class, 'targetAttribute' => ['adId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'imageId' => 'Image ID',
            'adId' => 'Ad ID',
        ];
    }

    /**
     * Gets query for [[Ad]].
     *
     * @return ActiveQuery
     */
    public function getAd(): ActiveQuery
    {
        return $this->hasOne(Ads::class, ['id' => 'adId']);
    }

    /**
     * Gets query for [[Image]].
     *
     * @return ActiveQuery
     */
    public function getImage(): ActiveQuery
    {
        return $this->hasOne(Images::class, ['id' => 'imageId']);
    }

    /**
     * @return void
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function deleteImageRelation(): void
    {
        $this->delete();
    }
}
