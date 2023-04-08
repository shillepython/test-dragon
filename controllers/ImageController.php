<?php

namespace app\controllers;

use app\models\Image;
use app\models\ImageSearch;
use app\Service\ImageService;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * class ImageController
 */
class ImageController extends Controller
{
    public $imageService = null;

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function __construct($id, $module, ImageService $imageService, $config = [])
    {
        $this->imageService = $imageService;
        parent::__construct($id, $module, $config);
    }

    /**
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ImageSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }


    /**
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $modelsImage = [new Image()];

        if (!Yii::$app->request->isPost) {
            return $this->render('create', [
                'modelsImage' => $modelsImage,
            ]);
        }

        try {
            $this->imageService->uploadImage();
            return $this->redirect(['index']);
        } catch (\Exception $exception) {
            Yii::$app->session->setFlash('danger', $exception->getMessage());
            return $this->render('create', [
                'modelsImage' => $modelsImage,
            ]);
        }
    }

    /**
     * @param int $id
     * @return \yii\console\Response|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDownload(int $id)
    {
        return $this->imageService->downloadImage($id);
    }

    /**
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param int $id ID
     * @return Image the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Image::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
