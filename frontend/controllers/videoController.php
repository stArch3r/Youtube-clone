<?php


namespace frontend\controllers;


use common\models\Video;
use common\models\VideoLike;
use common\models\VideoView;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class VideoController
 *
 * @package frontend\controllerss
 */
 class VideoController extends Controller
 {

     public function behaviors()
     {
         return [
             'access' => [
                 'class' => AccessControl::class,
                 'only' => ['like', 'dislike', 'history'],
                 'rules' => [
                     [
                         'allow' => true,
                         'roles' => ['@']
                     ]
                 ]
             ],
             'verb' => [
                 'class' => VerbFilter::class,
                 'actions' => [
                     'like' => ['post'],
                     'dislike' => ['post'],
                 ]
             ]
         ];
     }

     public function actionIndex()
     {
         $dataProvider = new ActiveDataProvider([
             'query' => Video::find()->with('createdBy')->published()->latest(),
         ]);

         return $this->render('index', [
             'dataProvider' => $dataProvider
         ]);
     }
     public function actionView($id)
         {
             $this->layout = 'auth';
             $video = $this->findVideo($id);

             $videoView = new VideoView();
             $videoView->video_id = $id;
             $videoView->user_id = \Yii::$app->user->id;
             $videoView->created_at = time();
             $videoView->save();

             $similarVideos = Video::find()
                 ->published()
                 ->byKeyword($video->title)
                 ->andWhere(['NOT', ['video_id' => $id]])
                 ->limit(10)
                 ->all();

             return $this->render('view', [
                 'model' => $video,
                 'similarVideos' => $similarVideos
             ]);
         }


   }
