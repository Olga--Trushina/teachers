<?php

namespace app\controllers;

use Yii;
use app\models\Teacher;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TeacherCrudController implements the CRUD actions for Teacher model.
 */
class TeacherController extends Controller
{

    /**
     * по умолчанию - Апрель
     */
    const DEFAULT_STUDENT_BORN_MONTH = 4;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }


    /**
     * @return string
     */
    public function actionIndex()
    {
        $model = new Teacher();
        $query = $model->getListWithStudentsCounrt();
        $query->asArray(true);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->view->title = 'Учетиля (с количеством учеников)';

        return $this->render('index', [
            'showMonthFilter' => false,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * "Флиьтр" по месяцу рождения студенов
     * @return string
     */
    public function actionStudentfilter()
    {

        $month = Yii::$app->getRequest()->getQueryParam('month', self::DEFAULT_STUDENT_BORN_MONTH);
        if ($month<1 || $month >12) {
            $month = self::DEFAULT_STUDENT_BORN_MONTH;
        }
        $monthes = array(
            1 => 'Январь',
            2 => 'Февраль',
            3 => 'Март',
            4 => 'Апрель',
            5 => 'Май',
            6 => 'Июнь',
            7 => 'Июль',
            8 => 'Август',
            9 => 'Сентябрь',
            10 => 'Октябрь',
            11 => 'Ноябрь',
            12 => 'Декабрь',
        );

        $model = new Teacher();
        $dataProvider = $model->findWhoAllStudentsBornInMonth($month);

        $dataProvider->setPagination(false);

        $this->view->title = 'Список учителей, все ученики которых рождены в месяце "' .$monthes[$month]. '"';
        return $this->render('index', [
            'showMonthFilter' => true,
            'monthes' => $monthes,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Вывод учителей с максимальным количеством общих учеников
     * @return string
     */
    public function actionCommonstudents()
    {

        $model = new Teacher();
        list($teachers, $commonStudents) = $model->findWhoHasMaxCommonStudents();

        $teachers = new ActiveDataProvider([
            'query' => $teachers,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        $commonStudents->setPagination(false);

        $this->view->title = 'Имена любых двух учителей, у которых максимальное количество общих учеников, и список этих общих учеников.';
        return $this->render('_common_students', [
            'commonStudents' => $commonStudents,
            'teachers' => $teachers,
        ]);
    }

    /**
     * Displays a single Teacher model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Teacher model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Teacher();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Teacher model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Teacher model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Teacher model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Teacher the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Teacher::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
