<?php

class m150309_220340_create_tbl_rest_tokens extends CDbMigration
{
	public function up()
	{
        $this->createTable('tbl_rest_tokens', array(
            'id' => 'pk',
            'user_id' => 'integer',
            'client_id' => 'string',
            'client_secret' => 'string',
            'status' => 'integer',
            'created_at' => 'timestamp',
            'last_use' => 'timestamp',
        ));
	}

	public function down()
	{
        $this->dropTable('tbl_rest_tokens');
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
