<?php


Yii::$container->set(
    \omarinina\application\services\image\interfaces\ImageParseInterface::class,
    \omarinina\application\services\image\ImageParseService::class
);

Yii::$container->set(
    \omarinina\application\factories\user\interfaces\UserFactoryInterface::class,
    \omarinina\application\factories\user\UserFactory::class
);

Yii::$container->set(
    \omarinina\application\factories\ad\interfaces\AdFactoryInterface::class,
    \omarinina\application\factories\ad\AdFactory::class
);

Yii::$container->set(
    \omarinina\application\services\category\interfaces\AdCategoriesAddInterface::class,
    \omarinina\application\services\category\AdCategoriesAddService::class
);

Yii::$container->set(
    \omarinina\application\services\image\interfaces\AdImageAddInterface::class,
    \omarinina\application\services\image\AdImageAddService::class
);

Yii::$container->set(
    \omarinina\application\services\image\interfaces\ImageSaveInterface::class,
    \omarinina\application\services\image\ImageSaveService::class
);

Yii::$container->set(
    \omarinina\application\factories\ad\interfaces\CommentFactoryInterface::class,
    \omarinina\application\factories\ad\CommentFactory::class
);

Yii::$container->set(
    \omarinina\application\services\ad\interfaces\FilterAdsGetInterface::class,
    \omarinina\application\services\ad\FilterAdsGetService::class
);

Yii::$container->set(
    \omarinina\application\services\category\interfaces\AdCategoryUpdateInterface::class,
    \omarinina\application\services\category\AdCategoryUpdateService::class
);

Yii::$container->set(
    \omarinina\infrastructure\services\user\interfaces\UserAuthVkInterface::class,
    \omarinina\infrastructure\services\user\UserAuthVkService::class
);
