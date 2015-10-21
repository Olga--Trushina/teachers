<?php

use yii\db\Schema;
use yii\db\Migration;

class m151021_124367_init extends Migration
{
    public function up()
    {
        $this->execute("CREATE TABLE `student` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(255) DEFAULT NULL,
          `email` varchar(255) DEFAULT NULL,
          `birthday` date NOT NULL,
          `level` enum('A1','A2','B1','B2','C1','C2') DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `birthday` (`birthday`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        $this->execute("CREATE TABLE `teacher` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(255) DEFAULT NULL,
          `gender` enum('M','F') DEFAULT NULL,
          `phone` varchar(100) DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        $this->execute("CREATE TABLE `student_teacher` (
          `student_id` int(11) NOT NULL,
          `teacher_id` int(11) NOT NULL,
          PRIMARY KEY (`student_id`,`teacher_id`),
          KEY `teacher` (`teacher_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    }

    public function down()
    {
        $this->dropTable('student_teacher');
        $this->dropTable('student');
        $this->dropTable('teacher');
    }

}
