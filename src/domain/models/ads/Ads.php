<?php

declare(strict_types=1);

namespace omarinina\domain\models\ads;

use omarinina\application\services\category\interfaces\AdCategoryUpdateInterface;
use omarinina\application\services\image\AdImageDeleteService;
use omarinina\application\services\image\interfaces\AdImageAddInterface;
use omarinina\application\services\image\interfaces\ImageParseInterface;
use omarinina\application\services\image\interfaces\ImageSaveInterface;
use omarinina\infrastructure\models\forms\AdCreateForm;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use omarinina\domain\models\Users;
use yii\web\ServerErrorHttpException;

/**
 * This is the model class for table "ads".
 *
 * @property int $id
 * @property string $name
 * @property int $typeId
 * @property string $description
 * @property int $author
 * @property string $email
 * @property string $createAt
 * @property int $price
 *
 * @property AdsToCategories[] $adsToCategories
 * @property Users $authorUser
 * @property Comments[] $comments
 * @property AdTypes $type
 * @property AdCategories[] $adCategories
 * @property Images[] $images
 * @property AdsToImages[] $adsToImages
 */
class Ads extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'ads';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'typeId', 'description', 'author', 'email', 'price'], 'required'],
            [['description'], 'string'],
            [['typeId', 'author'], 'integer'],
            [['createAt'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 50],
            [['price'], 'integer', 'min' => 100],
            [['author'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['author' => 'id']],
            [['typeId'], 'exist', 'skipOnError' => true, 'targetClass' => AdTypes::class, 'targetAttribute' => ['typeId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'imageSrc' => 'Image Src',
            'typeId' => 'Type ID',
            'description' => 'Description',
            'author' => 'Author',
            'email' => 'Email',
            'createAt' => 'Create At',
        ];
    }

    /**
     * Gets query for [[AdsToCategories]].
     *
     * @return ActiveQuery
     */
    public function getAdsToCategories(): ActiveQuery
    {
        return $this->hasMany(AdsToCategories::class, ['adId' => 'id']);
    }

    /**
     * Gets query for [[AuthorUser]].
     *
     * @return ActiveQuery
     */
    public function getAuthorUser(): ActiveQuery
    {
        return $this->hasOne(Users::class, ['id' => 'author']);
    }

    /**
     * Gets query for [[Comments]].
     *
     * @return ActiveQuery
     */
    public function getComments(): ActiveQuery
    {
        return $this->hasMany(Comments::class, ['adId' => 'id']);
    }

    /**
     * Gets query for [[Type]].
     *
     * @return ActiveQuery
     */
    public function getType(): ActiveQuery
    {
        return $this->hasOne(AdTypes::class, ['id' => 'typeId']);
    }

    /**
     * Gets query for [[AdCategories]].
     *
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getAdCategories(): ActiveQuery
    {
        return $this->hasMany(AdCategories::class, ['id' => 'categoryId'])
            ->viaTable('adsToCategories', ['adId' => 'id']);
    }

    /**
     * Gets query for [[Images]].
     *
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getImages(): ActiveQuery
    {
        return $this->hasMany(Images::class, ['id' => 'imageId'])
            ->viaTable('adsToImages', ['adId' => 'id']);
    }

    /**
     * @return string|null
     */
    public function getFirstImage() : ?string
    {
        $images = $this->images ?? [];
        if (!empty($images)) {
            return array_pop($images)->imageSrc;
        }
        return null;
    }

    /**
     * Gets query for [[AdsToCategories]].
     *
     * @return ActiveQuery
     */
    public function getAdsToImages(): ActiveQuery
    {
        return $this->hasMany(AdsToImages::class, ['adId' => 'id']);
    }

    /**
     * @param AdCreateForm $form
     * @return $this
     * @throws InvalidConfigException
     * @throws ServerErrorHttpException
     * @throws Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     * @throws \yii\di\NotInstantiableException
     */
    public function updateAd(AdCreateForm $form): Ads
    {
        $imageParse = Yii::$container->get(ImageParseInterface::class);
        $imageSave = Yii::$container->get(ImageSaveInterface::class);
        $adCategoriesUpdate = Yii::$container->get(AdCategoryUpdateInterface::class);
        $adImagesAdd = Yii::$container->get(AdImageAddInterface::class);

        $transaction = Yii::$app->db->beginTransaction();
        if (!$transaction) {
            throw new ServerErrorHttpException(
                'Service is not available, please, try later',
                500
            );
        }

        try {
            if ($this->name !== $form->name) {
                $this->name = $form->name;
            }

            if ($this->description !== $form->description) {
                $this->description = $form->description;
            }

            if ($this->price !== $form->price) {
                $this->price = $form->price;
            }

            if ($this->typeId !== $form->typeId) {
                $this->typeId = $form->typeId;
            }

            if ($this->email !== $form->email) {
                $this->email = mb_strtolower($form->email);
            }

            $this->save(false);

            if ($form->images) {
                $imagePaths = [];
                $previousImages = $this->images ?? null;
                $imageRelations = $this->adsToImages ?? null;

                foreach ($imageRelations as $imageRelation) {
                    $imageRelation->deleteImageRelation();
                }

                foreach ($previousImages as $previousImage) {
                    $imagePaths[] = Yii::$app->basePath . '/web/' . $previousImage->imageSrc;
                    $previousImage->deleteImage();
                }

                $images = [];
                foreach ($form->images as $image) {
                    $imageSrc = $imageParse->parseImage($image, false);
                    $images[] = $imageSave->saveNewImage($imageSrc);
                }
                $adImagesAdd->addAdImages($images, $this->id);
            }

            $adCategoriesUpdate->updateAdCategories($this, $form->categories);

            $transaction->commit();

            AdImageDeleteService::deleteAdImages($imagePaths ?? []);
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * @return void
     * @throws Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function deleteAd(): void
    {
        $transaction = Yii::$app->db->beginTransaction();
        if (!$transaction) {
            throw new ServerErrorHttpException(
                'Service is not available, please, try later',
                500
            );
        }

        $images = $this->images ?? null;
        $imageRelations = $this->adsToImages ?? null;
        $comments = $this->comments ?? null;
        $imagePaths = [];

        try {
            foreach ($imageRelations as $imageRelation) {
                $imageRelation->deleteImageRelation();
            }

            foreach ($images as $image) {
                $imagePaths[] = Yii::$app->basePath . '/web/' . $image->imageSrc;
                $image->deleteImage();
            }

            foreach ($this->adsToCategories as $category) {
                $category->deleteCategory();
            }

            foreach ($comments as $comment) {
                $comment->deleteComment();
            }

            $this->delete();

            $transaction->commit();

            AdImageDeleteService::deleteAdImages($imagePaths ?? []);
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}
