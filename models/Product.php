<?php

namespace backend\models;

use common\helpers\ImageCommon;
use common\models\FS;
use yii\helpers\Url;


/**
 * This is the model class for table "product".
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $brand_id
 * @property string $name
 * @property string $anons
 * @property string $text
 * @property double $cost
 * @property double $cost_old
 * @property string $article
 * @property string $ext
 * @property integer $img_position
 * @property integer $active
 * @property string $title
 * @property string $keywords
 * @property ProductParam[] $paramsBuf
 * @property integer $count
 * @property integer $amount
 * @property string $desc
 * @property integer $pos
 * @property string $flags
 * @property string $surl
 * @property integer $cart_ind
 */
class Product extends Base
{
    use CommonModelEventExtTrait;
    use CommonModelTrait;
    const PER_PAGE = 12;
    const FLAG_HIT = 1;
    const FLAG_NEW = 2;
//    const FLAG_ACTION=4;
    const FLAG_SALE = 8;
    const FLAG_ORDER = 5;

    const NAME = 'Продукты';
    const ONE_NAME = 'продукт';

    const IS_PART_YES = 1;
    const IS_PART_NO = 0;

    public $partners;
    public $cart_ind;
    public $main_id;

    public $paramsBuf;
    public $paramsTotalCost;
    public $count;
    public $amount;

    public static function AllFlagsAsArray()
    {
        return [
            0   =>  'Не выбрано', // закоментить при чекбоксах
            self::FLAG_HIT  => 'Хит продаж',
            self::FLAG_NEW  =>  'Новинка',
//            self::FLAG_ACTION   =>  'Акция',
            self::FLAG_SALE =>  'Лучшая цена',
            //self::FLAG_ORDER =>  'Под заказ'
        ];
    }

    public static function getIsPartRadioArray()
    {
        return [
            self::IS_PART_YES  => 'Да',
            self::IS_PART_NO  =>  'Нет',
        ];
    }


    public function getFlagsAsArray()
    {
        $arFlags = [];
        for($i=0; $i < 10; $i++)
        {
            $f = $this->flags & (1 << $i);

            if($f != 0)
            {
                $arFlags[] = $f;
            }
        }

        return $arFlags;
    }

    public function getFlagClass()
    {
        $classes = [
            self::FLAG_HIT => 'label-hit',
            self::FLAG_NEW  =>  'label-new',
            self::FLAG_SALE =>  'label-discount',
            self::FLAG_ORDER =>  'label-order'
        ];
        return $classes[$this->flags];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        if (\Yii::$app->language == Base::RU) {
            return '{{%product}}';
        } elseif (\Yii::$app->language == Base::EN) {
            return '{{%product_en_us}}';
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
                    'w' =>  600,
                    'h' =>  600,
                    'type' => ImageCommon::RESIZE_CROP,
                    'bg_color'  => '#fff',
                    'doIfImgMoreBigger' => false,
                    'coords_strict' => true
                ],
                '_md'    =>  [
                    'w' =>  264,
                    'h' =>  264,
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
                '_cart'    =>  [
                    'w' =>  120,
                    'h' =>  120,
                    'type' => ImageCommon::RESIZE_CROP,
                    'bg_color'  => '#fff',
//                   'watermarks' =>  [
//                       [
//                           'name'  =>  'watermark.png',
//                            'x' =>  0,
//                            'y' =>  0,
////                            'opacity'   =>  50,
//                            'opacity'   =>  100,
//                       ],
//                        [
//                            'name'  =>  'watermark1.png',
//                            'x' =>  true,
//                            'y' =>  false,
//                            'opacity'   =>  80,

//                            'x' =>  0,
//                            'y' =>  0,
//                            'opacity'   =>  100,
//
//                        ]
//                    ]
                ],
                '_sm'    =>  [
                    'w' =>  70,
                    'h' =>  70,
                    'type' => ImageCommon::RESIZE_CROP,
                    'bg_color'  => '#fff',
//                   'watermarks' =>  [
//                       [
//                           'name'  =>  'watermark.png',
//                            'x' =>  0,
//                            'y' =>  0,
////                            'opacity'   =>  50,
//                            'opacity'   =>  100,
//                       ],
//                        [
//                            'name'  =>  'watermark1.png',
//                            'x' =>  true,
//                            'y' =>  false,
//                            'opacity'   =>  80,

//                            'x' =>  0,
//                            'y' =>  0,
//                            'opacity'   =>  100,
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
    public function rules()
    {
        return [
            [['category_id', 'img_position', 'active', 'pos', 'flags','on_main'], 'integer'],
            ['partners', 'each', 'rule'=>['integer']],
            [['name'], 'required'],
            [['link'], 'string' , 'max' => 20000],
            [['anons'], 'string' , 'max' => 20000],
            [['text'],'string','max' => 100000],
            [['cost','cost_old'], 'double'],
            [['name', 'title', 'keywords', 'desc' , 'surl'], 'string', 'max' => 255],
            [['ext'], 'each' , 'rule' => ['string']],
            [['surl'], 'unique'],
            [['category_id'], 'default', 'value' => 0],
        ];
    }

    public function getCategory()
    {
        return $this->hasOne(Category::className(),['id' => 'category_id']);
    }


    public function getParentLink($request = null)
    {
        if(empty($request))
            return Url::to(['index','ProductSearch[category_id]' => $this->category_id]);
        else
            return Url::to(['index',
                'ProductSearch[category_id]' => $this->category_id,
                'per-page'=> $request->get('per-page',Product::PER_PAGE),
                'page'=>$request->get('page',1)
            ]);
    }

    public function getPhotos()
    {
        return $this->hasMany(ProductPhoto::className(),['product_id'   =>  'id']);
    }

    public function getParams()
    {
        return $this->hasMany(ProductParam::className(),['product_id'   =>  'id']);
    }

    public function getPartnersToProduct()
    {
        return $this->hasMany(ProductToPartner::className(),['product_id'   =>  'id']);
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

    public function afterFind()
    {
        $this->processEventsFor(__FUNCTION__);
    }

    public function beforeDelete()
    {
        if(parent::beforeDelete())
        {
            ProductPhoto::deleteAll(['product_id' => $this->id]);
            FS::DeleteDir($this->getUploadPath(['real' => true , 'tableName' => ProductPhoto::tableName()]) . $this->id . '/');
            $this->deleteImagesMany();
            return true;
        }
        else
            return false;
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Название',
            'surl' => 'ЧПУ',
            'anons' => 'Краткое описание',
            'text' => 'Описание',
            'cost_old' => 'Старая цена',
            'cost' => 'Цена',
            'article' => 'Артикул',
            'active' => 'На сайте',
            'title' => 'Тайтл',
            'keywords' => 'Ключевые слова',
            'desc' => 'Мета описание',
            'flags' => 'Флажки',
            'in_stock' => 'Есть в наличии',
            'date' => 'Дата',
            'link' => 'Ссылка',
            'fio' => 'Имя',
            'tel' => 'Телефон',
            'email' => 'Email',
            'message' => 'Сообщение',
            'question' => 'Вопрос',
            'answer' => 'Ответ',
            'color' => 'Цвет упаковки',
            'verifyCode' => 'Проверочный код',
            'from_page' =>  'Со страницы',
            'count' => 'Количество',
            'balance_amount' => 'Остаток сумма',
            'remainder_amount' => 'Остаток кол-во',
            'is_part_of' => 'Входит в состав',
            'package_id' => 'Вид упаковки',
            'color_id' => 'Цвет упаковки',
            'on_main' => 'На главной',
        ];
    }
}
