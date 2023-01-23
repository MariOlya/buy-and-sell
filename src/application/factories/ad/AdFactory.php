<?php

declare(strict_types=1);

namespace omarinina\application\factories\ad;

use omarinina\application\factories\ad\dto\NewAdDto;
use omarinina\application\factories\ad\interfaces\AdFactoryInterface;
use omarinina\application\services\category\interfaces\AdCategoriesAddInterface;
use omarinina\application\services\image\interfaces\AdImageAddInterface;
use omarinina\application\services\image\interfaces\ImageParseInterface;
use omarinina\application\services\image\interfaces\ImageSaveInterface;
use omarinina\domain\models\ads\Ads;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;
use yii\web\ServerErrorHttpException;

class AdFactory implements AdFactoryInterface
{
    /**
     * @param NewAdDto $dto
     * @return Ads
     * @throws ServerErrorHttpException
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     * @throws Throwable
     */
    public function createNewAd(NewAdDto $dto): Ads
    {
        $imageParse = Yii::$container->get(ImageParseInterface::class);
        $imageSave = Yii::$container->get(ImageSaveInterface::class);
        $adCategoriesAdd = Yii::$container->get(AdCategoriesAddInterface::class);
        $adImagesAdd = Yii::$container->get(AdImageAddInterface::class);

        $transaction = Yii::$app->db->beginTransaction();
        if (!$transaction) {
            throw new ServerErrorHttpException(
                'Service is not available, please, try later',
                500
            );
        }

        try {
            $newAd = new Ads();
            $newAd->attributes = $dto->form->getAttributes();
            $newAd->email = mb_strtolower($dto->form->email);
            $newAd->author = $dto->author;
            $newAd->save(false);

            $images = [];
            foreach ($dto->form->images as $image) {
                $imageSrc = $imageParse->parseImage($image, false);
                $images[] = $imageSave->saveNewImage($imageSrc);
            }
            $adImagesAdd->addAdImages($images, $newAd->id);

            $adCategoriesAdd->addAdCategories($dto->form->categories, $newAd->id);

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $newAd;
    }
}