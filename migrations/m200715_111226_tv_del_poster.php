<?php

use yii\db\Migration;

/**
 * Class m200715_111226_tv_del_poster
 */
class m200715_111226_tv_del_poster extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('tv', 'poster');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200715_111226_tv_del_poster cannot be reverted.\n";

        return false;
    }
}
