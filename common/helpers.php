<?php
 function isGuest (){
     return Yii::$app->user->isGuest;
 }
 function currentUserid(){
     return YII::$app->user->id;
 }