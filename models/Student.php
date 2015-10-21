<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "student".
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $birthday
 * @property string $level
 */
class Student extends \yii\db\ActiveRecord
{

    const LEVEL_A1 = 'A1';
    const LEVEL_A2 = 'A1';
    const LEVEL_B1 = 'B1';
    const LEVEL_B2 = 'B1';
    const LEVEL_C1 = 'C1';
    const LEVEL_C2 = 'C1';


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'student';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','email','level','birthday'], 'required'],
            [['email'], 'email'],
            [['birthday'], 'date', 'format'=>'yyyy-M-d', 'message'=>'Пожалуйста, укажите дату рождения в формате YYYY-mm-dd'],
            [['level'], 'string'],
            [['level'], 'default', 'value' => 'A1'],
            [['name', 'email'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'email' => 'Email',
            'birthday' => 'Дата рождения',
            'level' => 'Уровень знания языка',
        ];
    }
}
