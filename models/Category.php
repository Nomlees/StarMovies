<?php

namespace backend\models;

use common\helpers\ImageCommon;
use yii\helpers\Url;
use common\models\FS;


/**
 * This is the model class for table "category".
 *
 * @property integer $id
 * @property integer $category_id
 * @property string $name
 * @property string $surl
 * @property string $anons
 * @property string $text
 * @property integer $active
 * @property string $ext
 * @property integer $img_position
 * @property integer $pos
 * @property string $title
 * @property string $keywords
 * @property string $desc
 */
class Category extends Base
{
    const NAME = 'Категории';
    const ONE_NAME = 'категорию';

    use CommonModelTrait;
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
                    'w' =>  1920,
                    'h' =>  1080,
                    'type' => ImageCommon::RESIZE_AUTO,
                    'bg_color'  => '#fff',
                    'doIfImgMoreBigger' => true,
//                    'coords_strict' => true
                ],
                '_mid'    =>  [
                    'w' =>  600,
                    'h' =>  494,
                    'type' => ImageCommon::RESIZE_CROP,
                    'bg_color'  => '#fff',
//                    'watermarks' =>  [
//                        [
//                            'name'  =>  'watermark.png',
////                            'x' =>  0,
////                            'y' =>  0,
//////                            'opacity'   =>  50,
////                            'opacity'   =>  100,
//                        ],
//                        [
//                            'name'  =>  'watermark1.png',
////                            'x' =>  true,
////                            'y' =>  false,
////                            'opacity'   =>  80,
//
////                            'x' =>  0,
////                            'y' =>  0,
////                            'opacity'   =>  100,
//
//                        ]
//                 ]
                ],
                '_sm'    =>  [
                    'w' =>  552,
                    'h' =>  392,
                    'type' => ImageCommon::RESIZE_CROP,
                    'bg_color'  => '#fff',
//                    'watermarks' =>  [
//                        [
//                            'name'  =>  'watermark.png',
////                            'x' =>  0,
////                            'y' =>  0,
//////                            'opacity'   =>  50,
////                            'opacity'   =>  100,
//                        ],
//                        [
//                            'name'  =>  'watermark1.png',
////                            'x' =>  true,
////                            'y' =>  false,
////                            'opacity'   =>  80,
//
////                            'x' =>  0,
////                            'y' =>  0,
////                            'opacity'   =>  100,
//
//                        ]
//                    ]
                ],


            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        if (\Yii::$app->language == Base::RU) {
            return '{{%category}}';
        } elseif (\Yii::$app->language == Base::EN) {
            return '{{%category_en_us}}';
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'active', 'img_position', 'pos'], 'integer'],
            [['name'], 'required'],
            //[['anons'], 'string' , 'max' => 20000],
            //[['text'],'string','max' => 100000],
            [['name', 'surl', 'title', 'keywords', 'desc'], 'string', 'max' => 255],
            [['ext'], 'string', 'max' => 6],
            [['surl'], 'unique'],
            [['category_id'], 'default', 'value' => 0],
        ];
    }

    public function getChildCategories()
    {
        return $this->hasMany(Category::className(),['category_id' => 'id']);
    }

    public function getItems()
    {
        return $this->hasMany(Category::className(),['category_id' => 'id']);
    }

    public function getParent()
    {
        return $this->hasOne(Category::className(),['id' => 'category_id']);
    }

    public function getProducts()
    {
        return $this->hasMany(Product::className(),['category_id' => 'id']);
    }

    public function getPhotos()
    {
        return $this->hasMany(CategoryPhoto::className(),['category_id' => 'id']);
    }

    public function getChildLink()
    {
        return Url::to(['category/index','CategorySearch[category_id]' => $this->id]);
    }

    public function getParentLink()
    {
        return Url::to(['category/index','CategorySearch[category_id]' => $this->category_id]);
    }

    public function beforeValidate()
    {
        $this->processEventsFor(__FUNCTION__);

        return parent::beforeValidate();
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

    protected function deleteChildCats($id)
    {
        $m = Category::findOne($id);
        if($m)
        {
            $childs = $m->getChildCategories()->select(['id','name'])->all();
            foreach($childs as $c)
            {

                $childs[] = $this->deleteChildCats($c->id);
                $c->delete();
            }
            return $childs;
        }

        return false;
    }

    public function beforeDelete()
    {
        if(parent::beforeDelete())
        {
            $this->deleteChildCats($this->id);
            CategoryPhoto::deleteAll(['category_id' => $this->id]);
            FS::DeleteDir($this->getUploadPath(['real' => true, 'tableName' => CategoryPhoto::tableName()]) . $this->id . '/');

            foreach($this->products as $p)
            {
                $p->delete();
            }
            $this->deleteImages();
            return true;
        }
        else
            return false;
    }


}
