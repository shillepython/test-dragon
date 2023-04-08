<?php

use app\models\Image;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\ImageSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Images';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="image-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Image', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'image-pjax']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'attribute' => 'image',
                'format' => 'raw',

                'value' => function (Image $model) {
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-download-alt">' . $model->image . '</span>', ['image/download', 'id' => $model->id], [
                        'data-pjax' => '0'
                    ]);
                },
            ],
            [
                'attribute' => 'size',
                'format' => 'raw',

                'value' => function (Image $model) {
                    return $model->size . ' Mb';
                }
            ],
            'updated_at',
            'created_at',
            [
                'class' => ActionColumn::className(),
                'template' => '{view} {delete}',
                'urlCreator' => function ($action, Image $model) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ]
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
