<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "student_teacher".
 *
 * @property integer $id
 * @property integer $student_id
 * @property integer $teacher_id
 */
class StudentTeacher extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'student_teacher';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['student_id', 'teacher_id'], 'required'],
            [['student_id', 'teacher_id'], 'integer'],
            ['student_id', 'exist', 'targetClass' => Student::className(), 'targetAttribute' => 'id', 'message' => 'Ученик с таким id не найден'],
            ['teacher_id', 'exist', 'targetClass' => Teacher::className(), 'targetAttribute' => 'id', 'message' => 'Учитель с таким id не найден'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'student_id' => 'ID Студента',
            'teacher_id' => 'ID Учителя',
        ];
    }

    public static function validateEcxistance() {

    }

}
