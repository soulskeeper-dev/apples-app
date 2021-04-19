<?php

use yii\db\Migration;

/**
 * Class m210419_105612_create_table_apple
 */
class m210419_105612_create_table_apple extends Migration
{
    public function up()
    {
        $this->createTable('{{%apple}}', [
            'id'      => $this->primaryKey(),
            'color'   => $this->string(20),
            'status'  => $this->tinyInteger(1)->notNull(),
            'size'    => $this->double()->notNull(),
            'created' => $this->integer()->notNull(),
            'fell'    => $this->string(),
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%apple}}');
        return true;
    }
}
