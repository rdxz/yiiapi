<?php
namespace app\modules\v1\controllers;

use yii\rest\ActiveController;
use app\models\Article;
use yii\data\Pagination;
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

        // print_r($post);
        // die;
        $parser = new \HyperDown\Parser;
        $article->title = $post['title'];
        $article->content = $post['content_md'];
        $article->excerpt =  $this->cutChar($post['content_md']);
        // print_r( $article->excerpt);
        // print_r(getPreg())
        // die;
        $article->content_md = $post['content'];
        $article->create_time = time();
        $article->update_time = time();
        $article->status = $post['status'];
        // $article->content_md = $parser->makeHtml($post['content']);;
        $article->tag_id = $post['tag_id'];
        $result = $article->save();
        return $result;
    }

    		//截取摘要
		public function cutChar($document){
			$document = trim($document);
			if (strlen($document) <= 0){
			  return $document;
			}
			$search = array ("'<script[^>]*?>.*?</script>'si",  // 去掉 javascript
			                  "'<[\/\!]*?[^<>]*?>'si",          // 去掉 HTML 标记
			                  "'([\r\n])[\s]+'",                // 去掉空白字符
			                  "'/\s(?=\s)/'",
			                  "'/[nrt]/'",
			                  
			                  "'/[\n\r\t]/'",
			                  "'/s(?=s)/'",
			                  "'&(quot|#34);'i",                // 替换 HTML 实体
			                  "'&(amp|#38);'i",
			                  "'&(lt|#60);'i",
			                  "'&(gt|#62);'i",
			                  "'&(nbsp|#160);'i"
			                  );                   
			$replace = array ("","","\\1","\"","&","<",">"," ");
			$String=@preg_replace ($search, $replace, $document);
			return $this->sysSubStr($String,500,true);
        }



        public function sysSubStr($String,$Length,$Append = false){ 
    		if (strlen($String) <= $Length ){ 
        		return $String; 
    		}else{ 
        		$I = 0; 
        	while ($I < $Length) { 
            	$StringTMP = substr($String,$I,1); 
            	if (ord($StringTMP) >=224){ 
                	$StringTMP = substr($String,$I,3); 
                	$I = $I + 3; 
            	}elseif(ord($StringTMP) >=192){ 
                	$StringTMP = substr($String,$I,2); 
                	$I = $I + 2; 
            	}else{ 
                	$I = $I + 1; 
            	} 
            	$StringLast[] = $StringTMP; 
        	} 
        	$StringLast = implode("",$StringLast); 
        	if($Append){ 
            	$StringLast .= "..."; 
        	} 
        	return $StringLast; 
  			} 
		}
	
        
    public function  CloseTags($html) { 
            // 直接过滤错误的标签 <[^>]的含义是 匹配只有<而没有>的标签    
            // 而preg_replace会把匹配到的用''进行替换
            $html = preg_replace('/<[^>]*$/','',$html); 

            // 匹配开始标签，这里添加了1-6，是为了匹配h1~h6标签
            preg_match_all('#<([a-z1-6]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result); 
            $opentags = $result[1]; 
            // 匹配结束标签
            preg_match_all('#</([a-z1-6]+)>#iU', $html, $result); 
            $closetags = $result[1]; 
            $len_opened = count($opentags); 
            // 如何两种标签数目一致 说明截取正好
            if (count($closetags) == $len_opened) { return $html; } 
            
            $opentags = array_reverse($opentags); 
            // 过滤自闭和标签，也可以在正则中过滤 <(?!meta|img|br|hr|input)>
            $sc = array('br','input','img','hr','meta','link'); 
            
            for ($i=0; $i < $len_opened; $i++) { 
                $ot = strtolower($opentags[$i]); 
                if (!in_array($opentags[$i], $closetags) && !in_array($ot,$sc)) { 
                    $html .= '</'.$opentags[$i].'>'; 
                } else { 
                    unset($closetags[array_search($opentags[$i], $closetags)]); 
                } 
            } 
            return $html; 
        }

    public function actionDesc()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        // print_r($get['id']);
        // die;
        $article = Article::findOne($get['id']);
        return $article;
    }

    public function actionList($perPage = 20)
    {
        // print_r($perPage);
        // die;
        $request = Yii::$app->request;
        $get = $request->get();
        // print_r($get['id']);
        // die;
        $query = Article::find()->asArray();
        $countQuery = clone $query;
        $pagination = new Pagination([
                'totalCount' => $countQuery->count(),
                'pageSize' => $perPage,
            ]);

        $totalCount = $pagination->totalCount;

        $article = $query->offset($pagination->offset)
                ->limit($pagination->limit)
                ->all();

        return [
                'article' => $article,
                'totalCount' => $totalCount,
            ];
    }

    public function actions()
    {

        $actions = parent::actions();

        // 全部的API都手动写出来,然后用权限控制
        unset($actions['delete'], $actions['create'], $actions['index'], $actions['view'], $actions['update']);

        return $actions;
    }
}