<?php

namespace app\commands;

use app\models\Student;
use app\models\StudentTeacher;
use app\models\Teacher;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;


/**
 * Class FillController
 * Cli класс для заполнения БД тестовыми данными
 * @package app\commands
 */
class FillController extends  \yii\console\Controller
{

    /**
     * Максимальное количество учеников у одного учителя
     */
    const MAX_TEACHER_STUDENTS = 15;

    const STUDENTS_NUM = 100000;

    const TEACHERS_NUM = 10000;

    const STUDENT_MIN_AGE = 18;

    const STUDENT_MAX_AGE = 80;

    const INSERT_PORTION_SIZE = 500;

    private $minStudentId;

    private $maxStudentId;

    private $minTeacherId;

    private $maxTeacherId;


    /**
     * Гененирует тестовый данные в БД. Единственная точка входа
     */
    public function actionIndex()
    {

        try {

            echo "Начинаю генерировать " . $this->getConfig('students_num') . " студентов" . "\n";
            $this->minStudentId = Student::find()->max('id') + 1;
            $this->generateStudents();
            $this->maxStudentId = Student::find()->max('id');
            echo 'Студенты успешно сгенерированы' . "\n";

            echo "Начинаю генерировать " . $this->getConfig('teachers_num') . " учителей" . "\n";
            $this->minTeacherId = Teacher::find()->max('id') + 1;
            $this->generateTeachers();
            $this->maxTeacherId = Teacher::find()->max('id');
            echo 'Учителя успешно сгенерированы' . "\n";

            echo "Начинаю генерировать привязки студентов к учителям." . "\n";
            $this->generateLinks();
            echo 'Привязки успешно сгенерированы' . "\n";

        } catch (Exception $e) {
            echo 'Ошибка. Не удалось сгенерировать тестовые данные. Причина: ' . $e->getMessage();
        }

    }


    /**
     *
     */
    private function generateTeachers() {
        $genderList = array(Teacher::GENDER_MALE, Teacher::GENDER_FEMALE);

        $prepareItemData = function($itemNum) use ($genderList) {
            return [
                'Teacher Name ' . $itemNum,
                $genderList[ rand(0,1) ],
                '+79260000'.rand(100,999)
            ];
        };

        $this->loadByPortion($prepareItemData, Teacher::tableName(), ['name', 'gender', 'phone'], $this->getConfig('teachers_num'));
    }


    /**
     *
     */
    private function generateStudents() {

        $levels = array(Student::LEVEL_A1, Student::LEVEL_A2, Student::LEVEL_B1,
            Student::LEVEL_B2, Student::LEVEL_C1, Student::LEVEL_C2, );
        $oldestMktime = mktime(0 ,0, 0, 0, 0, date('Y')-$this->getConfig('student_max_age'));
        $newestMktime = mktime(0, 0, 0, 0, 0, date('Y')-$this->getConfig('student_min_age'));

        $prepareItemData = function($itemNum) use ($oldestMktime,$newestMktime,$levels) {
            return [
                'Student Name ' . $itemNum,
                "test{$itemNum}@email.com",
                date('Y-m-d', rand($oldestMktime, $newestMktime)),
                $levels[rand(0, count($levels) - 1)],
            ];
        };

        $this->loadByPortion($prepareItemData, Student::tableName(), ['name', 'email', 'birthday', 'level'], $this->getConfig('students_num'));
    }

    /**
     * @param \Closure $prepareItemData
     * @param string $tableName
     * @param array $fieldsList
     * @param int $fullCount
     * @throws \yii\db\Exception
     */
    private function loadByPortion(\Closure $prepareItemData, $tableName, $fieldsList, $fullCount) {
        $num = 0;
        do {
            if (($fullCount - $num)<= $this->getConfig('insert_portion_size')) {
                $portionSize = $fullCount - $num;
            } else {
                $portionSize = $this->getConfig('insert_portion_size');
            }
            $portionData = array();
            for ($i = 0; $i < $portionSize; $i++) {
                $itemNum = $num + $i + 1;
                $portionData[] = $prepareItemData($itemNum);
            }
            $num += $i;

            $c = Yii::$app->db->createCommand();
            $c->batchInsert(
                $tableName,
                $fieldsList,
                $portionData
            );
            $c->execute();

        } while ($num<$fullCount);
    }


    /**
     * @throws \yii\db\Exception
     */
    private function generateLinks() {

        $c = Yii::$app->db->createCommand();

        $newBatch = [];
        $currentTeacherId = $this->minTeacherId;
        do {
            $studentsNum = rand(0, $this->getConfig('max_teacher_students'));

            if (0==$studentsNum) {
                continue;
            }

            $batch = [];
            while(count($batch) < $studentsNum)
            {
                $batch[rand($this->minStudentId, $this->maxStudentId)] = true;
            }

            foreach($batch as $k => $_) {
                $newBatch[] = [$k, $currentTeacherId];
            }

            if(count($newBatch) >= $this->getConfig('insert_portion_size'))
            {
                $c->batchInsert(
                    StudentTeacher::tableName(),
                    ['student_id', 'teacher_id'],
                    $newBatch
                );
                $c->execute();
                $newBatch = [];
            }

            $currentTeacherId++;

        } while ($currentTeacherId <= $this->maxTeacherId);

        if(!empty($newBatch))
        {
            $c->batchInsert(
                StudentTeacher::tableName(),
                ['student_id', 'teacher_id'],
                $newBatch
            );
            $c->execute();
        }

    }


    /**
     * @param $paramName
     * @return mixed
     * @throws InvalidConfigException
     */
    private function getConfig($paramName) {
        $configSectionName = 'test_data_fill';
        $config = Yii::$app->params;
        if (!isset($config[$configSectionName])) {
            throw new InvalidConfigException("Конфигурационные данные должны быть указаны в секции '$configSectionName'");
        }
        $config = $config[$configSectionName];
        if (isset($config[$paramName])) {
            return $config[$paramName];
        } else {
            throw new InvalidConfigException('Не определён параметр '.$paramName);
        }
    }

}
