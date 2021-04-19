<?php

use yii\db\Migration;
use \common\models\User;

/**
 * Class m210419_115056_add_user
 */
class m210419_115056_add_user extends Migration
{
    public function up()
    {
        $user = new User([
            'username' => 'apple',
            'email' => 'apple@apple-app.ml',
            'status' => 10
        ]);
        $user->setPassword('gotapples');
        $user->save();
    }

    public function down()
    {
        return false;
    }
}
