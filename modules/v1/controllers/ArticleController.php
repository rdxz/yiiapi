<?php
namespace app\modules\v1\controllers;

use yii\rest\ActiveController;
use app\models\Article;
use yii;

class ArticleController extends \yii\rest\ActiveController
{
    public $modelClass='app\models\User';

    public function actionIndex()
    {
        return ['name'=>'stark'];
        // $i =  ['name'=>'stark','age'=>'18','sex'=>'man'];
        // return $i;
    }

    public function actionCreate()
    {   
        $article = new Article();
        $request = Yii::$app->request;
        $post = $request->post();
        $parser = new \HyperDown\Parser;
        // $data['title'] = $post['title'];
		// $data['content'] = $post['content'];
		// $data['content_md'] = $parser->makeHtml($data['content']);
		// $data['tag_id'] = $post['tag_id'];
        // $data['tag_name'] = 'test';
        // $data['title'] = 'testddd';
        // print_r($data);
        // die;
        $article->title = $post['title'];
        $article->content = $post['content'];
        $article->content_md = $parser->makeHtml($post['content']);;
        $article->tag_id = $post['tag_id'];
        $result = $article->save();
        return $result;
    }

    public function actions()
    {

        $actions = parent::actions();

        // 全部的API都手动写出来,然后用权限控制
        unset($actions['delete'], $actions['create'], $actions['index'], $actions['view'], $actions['update']);

        return $actions;
    }
}