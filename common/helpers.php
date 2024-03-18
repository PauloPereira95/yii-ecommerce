<?php
 function isGuest (){
     return Yii::$app->user->isGuest;
 }
 function currentUserid(){
     return YII::$app->user->id;
 }

 function param($key){
    return Yii::$app->params[$key];
 }