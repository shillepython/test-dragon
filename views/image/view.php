<?php

use app\models\Image;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Image $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Images', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="image-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'image',
                'format' => 'raw',

                'value' => function (Image $model) {
                    return \yii\helpers\Html::a('<img width="500px" src=' . \yii\helpers\Url::toRoute(['image/download', 'id' => $model->id]) . '>', ['image/download', 'id' => $model->id], [
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
        ],
    ]) ?>

</div>
