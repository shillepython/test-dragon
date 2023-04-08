<?php

namespace app\models;

use app\Service\ImageService;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 *
 * @property int $id
 * @property string|null $image
 * @property int|null $size
 * @property string|null $updated_at
 * @property string $created_at
 */
class Image extends \yii\db\ActiveRecord
{
    public $imageFiles;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['imageFiles'], 'file', 'skipOnEmpty' => false, 'when' => function($model) {
                return !$model->isNewRecord || !empty($model->imageFile);
            }, 'extensions' => 'png, jpg, gif, jpeg', 'maxSize' => 1024 * 1024 * 5],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'image' => 'Image',
            'size' => 'Size',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }

    public static function createMultiple($multipleModels=null)
    {
        $model = new self();
        $formName = $model->formName();
        $images = \Yii::$app->request->post($formName);
        $models = [];

        if ($multipleModels !== null && is_array($multipleModels) && !empty($multipleModels)) {
            $keys = array_keys(ArrayHelper::map($multipleModels, 'id', 'id'));
            $multipleModels = array_combine($keys, $multipleModels);
        }

        $modelIds = ArrayHelper::getColumn($images, 'id');
        $modelsFromDb = self::findAll(['id' => $modelIds]);

        foreach ($images as $i => $item) {
            if (isset($item['id']) && !empty($item['id']) && isset($multipleModels[$item['id']])) {
                $model = $multipleModels[$item['id']];
            } else {
                $model = new self();
            }

            $modelFromDb = array_filter($modelsFromDb, function($modelFromDb) use ($item) {
                return $modelFromDb->id == $item['id'];
            });
            $modelFromDb = array_shift($modelFromDb);

            if ($modelFromDb !== null) {
                $model->setAttributes($modelFromDb->getAttributes());
            }

            $model->load($item, '');
            $models[] = $model;
        }

        unset($model, $formName, $images, $modelIds, $modelsFromDb);
        return $models;

    }

    /**
     * @throws \yii\db\Exception
     */
    public function uploadImage(): void
    {
        if (!$this->validate()) {
            throw new Exception($this->getFirstError('imageFiles'));
        }

        $imageService = new ImageService();
        $imageService->uploadImage($this->imageFiles);
    }

}
