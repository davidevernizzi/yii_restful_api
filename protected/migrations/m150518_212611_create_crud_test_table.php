<?php

class m150518_212611_create_crud_test_table extends CDbMigration
{
	public function up()
	{
        $this->createTable('tbl_crud_test', array(
            'id' => 'pk',
            'key1' => 'string',
            'key2' => 'string',
        ));
	}

	public function down()
	{
        $this->dropTable('tbl_crud_test');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}
