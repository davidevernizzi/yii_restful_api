<?php

/**
 * This is the model class for table "tbl_rest_tokens".
 *
 * The followings are the available columns in table 'tbl_rest_tokens':
 * @property integer $id
 * @property string $client_id
 * @property string $client_secret
 * @property integer $status
 * @property string $created_at
 * @property string $last_use
 */
class RestTokens extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_rest_tokens';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('created_at', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('client_id, client_secret', 'length', 'max'=>255),
			array('last_use', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, client_id, client_secret, status, created_at, last_use', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'client_id' => 'Client',
			'client_secret' => 'Client Secret',
			'status' => 'Status',
			'created_at' => 'Created At',
			'last_use' => 'Last Use',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.
        $this->user_id = User::getUserId(); // Ugly, don't we have this info in Yii::app()->user

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('client_id',$this->client_id,true);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('client_secret',$this->client_secret,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('last_use',$this->last_use,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return RestTokens the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function auth($params, $hmac)
    {
        $str = '';
        foreach ($params as $k => $v) {
            if ($k != 'hmac') {
                $str .= $v;
            }
        }
        $str .= $this->client_secret;
        $sha1 = sha1($str);

        if ($sha1 == $hmac) {
            return true;
        }
        else {
            return false;
        }
    }
}
