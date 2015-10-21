<?php

namespace app\models;

use Yii;
use yii\data\SqlDataProvider;

/**
 * This is the model class for table "teacher".
 *
 * @property integer $id
 * @property string $name
 * @property string $gender
 * @property string $phone
 */
class Teacher extends \yii\db\ActiveRecord
{

    const GENDER_MALE = 'M';

    const GENDER_FEMALE = 'F';


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'teacher';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','gender','phone'], 'required'],
            [['gender'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 100]
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
            'gender' => 'Пол',
            'phone' => 'Телефон',
            'student_count' => 'Количество учеников',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getstudent_teacher()
    {
        return $this->hasMany(StudentTeacher::className(), ['teacher_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getListWithStudentsCounrt() {
        $query = Teacher::find()
            ->joinWith(StudentTeacher::tableName())
            ->addSelect([Teacher::tableName().'.*','COUNT('.StudentTeacher::tableName().'.student_id) as student_count'])
            ->groupBy(Teacher::tableName().'.id');
        return $query;
    }

    /**
     * @param int $monthNum
     * @return SqlDataProvider
     */
    public function findWhoAllStudentsBornInMonth($monthNum) {

        if (1==strlen($monthNum)) {
            $monthNum = '0' . $monthNum;
        }

        $dataProvider = new SqlDataProvider(
            [
                'sql' => "SELECT t.*, COUNT(s.id) as student_count FROM teacher as t
LEFT JOIN student_teacher as st on t.id=st.teacher_id
LEFT JOIN student as s on s.id=st.student_id
WHERE MONTH(s.birthday)=:month
GROUP BY t.id
HAVING count(s.id)=(SELECT COUNT(st1.student_id) FROM teacher as t1
LEFT JOIN student_teacher as st1 on t1.id=st1.teacher_id WHERE t1.id=t.id)",
                'params' => [':month' => $monthNum],
            ]
        );
        return $dataProvider;

    }


    /**
     * @return SqlDataProvider[]
     */
    public function findWhoHasMaxCommonStudents() {
        $selectedTeachersData = new SqlDataProvider(
            [
                'sql' => "SELECT st1.teacher_id as t_id1, st2.teacher_id as t_id2, count(st1.student_id) as count  FROM student_teacher as st1
INNER JOIN student_teacher as st2
on st1.student_id=st2.student_id AND st1.teacher_id!=st2.teacher_id
GROUP BY st1.teacher_id, st2.teacher_id
ORDER BY count DESC
LIMIT 1;",
            ]
        );
        $selectedTeachersData = $selectedTeachersData->getModels();

        if (!empty($selectedTeachersData[0]) && isset($selectedTeachersData[0]['t_id1'])  && isset($selectedTeachersData[0]['t_id2'])) {
            $tId1 =  $selectedTeachersData[0]['t_id1'];
            $tId2 =  $selectedTeachersData[0]['t_id2'];
        } else {
            return array();
        }


        $commonStudents = new SqlDataProvider(
            [
                'sql' => "SELECT s.* FROM student_teacher  as st1
 LEFT JOIN student_teacher  as st2 on st1.student_id=st2.student_id
LEFT JOIN student as s on s.id=st1.student_id
WHERE st1.teacher_id=:teacher1 AND st2.teacher_id=:teacher2;",
                'params' => [':teacher1' => $tId1, ':teacher2' => $tId2],
            ]
        );

        $teachers = Teacher::find()->andWhere(['id'=>$tId1])->orWhere(['id'=>$tId2]);
        return [$teachers, $commonStudents];
    }


}
