<?php

namespace app\Service;

use app\models\Image;
use yii\db\Exception;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class ImageService
{
    /**
     * @throws Exception
     */
    public function uploadImage(): void
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $modelsImage = Image::createMultiple();

            if (Image::loadMultiple($modelsImage, \Yii::$app->request->post()) && !Image::validateMultiple($modelsImage)) {
                throw new Exception('Not validate images');
            }

            if (!is_dir('uploads') && !mkdir('uploads', 0777, true) && !is_dir('uploads')) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', 'uploads'));
            }

            /** @var Image $modelImage */
            foreach ($modelsImage as $index => $modelImage) {
                /** @var UploadedFile $file */
                $file = UploadedFile::getInstance($modelImage, "[{$index}]imageFiles");
                if ($file->error) {
                    throw new Exception('Failed upload image');
                }

                $fileName = uniqid('', true) . '.' . $file->extension;

                $modelImage->image = $fileName;
                $modelImage->size = round($file->size / 1048576, 2);
                $modelImage->save();
            }

            $transaction->commit();
            \Yii::$app->session->setFlash('success', 'Images uploaded successfully');
        } catch (\Exception $exception) {
            $transaction->rollBack();
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param int $id
     * @return \yii\console\Response|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function downloadImage(int $id)
    {
        $model = Image::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Not found file on server');
        }

        $path = \Yii::getAlias('@webroot/uploads/' . $model->image);
        if (!file_exists($path)) {
            throw new NotFoundHttpException('Not found file on storage.');
        }

        return \Yii::$app->response->sendFile('uploads/' . $model->image);
    }
}