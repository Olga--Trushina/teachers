<?php

use yii\helpers\Html;
use yii\grid\GridView;


$this->params['breadcrumbs'][] = $this->title;
?>
 <h1><?= Html::encode($this->title) ?></h1>

<h3>Учителя:</h3>
<?= GridView::widget([
    'dataProvider' => $teachers,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        'id',
        'name',
        'gender',
        'phone',

        ['class' => 'yii\grid\ActionColumn'],
    ],

]); ?>
<h3>Их общие студенты:</h3>
<?= GridView::widget([
    'dataProvider' => $commonStudents,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        'id',
        'name',
        'email',
        'birthday',
        'level',

        ['class' => 'yii\grid\ActionColumn'],
    ],

]); ?>