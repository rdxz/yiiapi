<?php
namespace app\modules\v1\controllers;

use yii\rest\ActiveController;
use yii;

class ActivityController extends \yii\rest\ActiveController
{
    public $modelClass='app\models\User';

    public function acionIndex()
    {
        return ['name'=>'stark'];
    }
}