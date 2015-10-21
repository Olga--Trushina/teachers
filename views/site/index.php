<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

NavBar::begin([
    'brandUrl' => Yii::$app->homeUrl,

]);
echo Nav::widget([
    'options' => ['class' => ''],
    'items' => [
        ['label' => 'Добавление нового учителя', 'url' => ['/teacher/create']],
        ['label' => 'Добавление нового ученика', 'url' => ['/student/create']],
        ['label' => 'Назначение учителю ученика', 'url' => ['/student-teacher/create']],
        ['label' => 'Список учителей с количеством занимающихся учеников', 'url' => ['/teacher']],
        ['label' => 'Список учителей, с которыми занимаются только ученики, родившиеся в апреле', 'url' => ['/teacher/studentfilter']],
        ['label' => 'Имена любых двух учителей, у которых максимальное количество общих учеников, и список этих общих учеников', 'url' => ['/teacher/commonstudents']],

    ],
]);
NavBar::end();
?>

