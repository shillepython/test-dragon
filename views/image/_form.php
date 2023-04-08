<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;

/** @var app\models\Image $modelsImage */

$form = ActiveForm::begin(['id' => 'dynamic-form']);

DynamicFormWidget::begin([
    'widgetContainer' => 'imagesform_wrapper',
    'widgetBody' => '.images-items',
    'widgetItem' => '.item',
    'limit' => 10,
    'min' => 1,
    'insertButton' => '.add-item',
    'deleteButton' => '.remove-item',
    'model' => $modelsImage[0],
    'formId' => 'dynamic-form',
    'formFields' => [
        'file',
    ],
]);

?>

<div class="images-items">
        <div class="item panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title pull-left">Image</h3>
                <div class="clearfix"></div>
            </div>
            <?php foreach ($modelsImage as $i => $modelImage): ?>

                <div class="panel-body">
                    <?php
                    // necessary for update action.
                    if (! $modelImage->isNewRecord) {
                        echo Html::activeHiddenInput($modelImage, "[{$i}]id");
                    }
                    ?>

                    <?= $form->field($modelImage, "[{$i}]imageFiles")->fileInput(['multiple' => true, 'accept' => 'image/*']) ?>

                    <div class="form-group">
                        <?= Html::button('Remove Image', ['class' => 'btn btn-danger remove-item']) ?>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
</div>

<div class="form-group">
    <?= Html::button('Add Image', ['class' => 'btn btn-success add-item']) ?>
    <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
</div>

<?php DynamicFormWidget::end(); ?>

<?php ActiveForm::end(); ?>

