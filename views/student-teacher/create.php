<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\StudentTeacher */

$this->title = 'Назначить учителю ученика';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-teacher-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <? if (isset($message)) : ?>
        <b><?=$message?></b><br>
    <? endif; ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
