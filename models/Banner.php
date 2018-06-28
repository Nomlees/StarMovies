<?php

namespace backend\models;

use common\helpers\ImageCommon;
use Yii;
use \yii\image\drivers\Image;
/**
 * This is the model class for table "banner".
 *
 * @property integer $id
 * @property string $name
 * @property string $text
 * @property string $link
 * @property string $link_text
 * @property string $ext
 * @property integer $active
 * @property integer $pos
 */
class Banner extends Base
{
    const NAME = 'Баннеры';
    const ONE_NAME = 'баннер';
    use CommonModelTrait;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        if(\Yii::$app->language==Base::RU) {
            return '{{%banner}}';
        }elseif(\Yii::$app->language==Base::EN){
            return '{{%banner_en_us}}';
        }
    }

    public static function getConfig()
    {
        return [
            'image' => [
//                'engine'    =>  'ImageUtilLegacyHelper',
//                'engineWatermark'   =>  'WideImageHelper',
////                'engineWatermark'   =>  'YiiImageHelper',

            ],
            'images'    =>  [
                '_big'    =>  [
                    'w' =>  1400,
                    'h' =>  380,
                    'type' => ImageCommon::RESIZE_CROP,
                    'bg_color'  => '#fff',
                    //'doIfImgMoreBigger' => true,
                    //'coords_strict' => true
                ],


            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['text'],'string','max' => 100000],
            [['active', 'pos'], 'integer'],
            [['name', 'link', 'link_text'], 'string', 'max' => 255],
            [['ext'], 'string', 'max' => 6],
            ['link','url'],
        ];
    }




    public function beforeDelete()
    {
        if(parent::beforeDelete())
        {
            $this->deleteImages();

            return true;
        }
        else
            return false;
    }




    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert))
        {
            $this->processEventsFor(__FUNCTION__,$insert);

            return true;
        }
        else
            return false;
    }

}
