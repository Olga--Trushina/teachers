<?php

namespace app\controllers;

use Yii;
use app\models\StudentTeacher;
use yii\web\Controller;
use yii\filters\VerbFilter;

/**
 * StudentTeacherController implements the CRUD actions for StudentTeacher model.
 */
class StudentTeacherController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
            ],
        ];
    }


    /**
     * Creates a new StudentTeacher model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new StudentTeacher();

        $post = Yii::$app->request->post();
        if (isset($post['StudentTeacher'])) {
            $studentId = $post['StudentTeacher']['student_id'];
            $teacherId = $post['StudentTeacher']['teacher_id'];
            $exists = StudentTeacher::find()->andWhere(['student_id' => $studentId])->andWhere(['teacher_id' => $teacherId])->exists();
            if (true === $exists) {
                return $this->render('create', [
                    'model' => $model,
                    'message' => 'Этут студент уже учится у этого учителя'
                ]);
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->render('create', [
                'model' => $model,
                'message' => 'Ученик успешно назначен учителю'
            ]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'message' => ''
            ]);
        }
    }
}
