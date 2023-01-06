<?php

declare(strict_types=1);

namespace omarinina\application\services\realtimeDatabase;

use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Factory;
use Yii;

class RealtimeDatabaseInitializeService
{
    /**
     * @return Database
     */
    public static function intializeRealtimeDatabase(): Database
    {
        $factory = (new Factory)
            ->withServiceAccount(
                Yii::getAlias('@app') . Yii::$app->params['pathToFirebaseCredentials']
            );

        return $factory->createDatabase();
    }
}