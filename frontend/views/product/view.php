<?php
/* @var $this yii\web\View */

/* @var $model \backend\models\Product */

use yii\helpers\Html;
use backend\models\Base;
use yii\widgets\Pjax;

$this->params['breadcrumbs'] = \frontend\controllers\CategoryController::getBreadcrumbs($model->category_id, false);
$this->params['breadcrumbs'][] = ['label' => $model->name];


$this->registerJsFile('/js/product/product.js', ['position' => \yii\web\View::POS_END, 'depends' => 'yii\web\YiiAsset']);


$this->title = $model->name;
$index = 0;

$costCurNone = $model->cost;
$costOldNone = (!empty($model->cost_old)) ? $model->cost_old : 0;

?>

<section class="page__section">
    <div class="columns">
        <div class="columns__item">
            <div class="product">
                <? if ($model->flags != 0): ?>
                    <div class="product__badge"><?= $model->AllFlagsAsArray()[$model->flags] ?></div>
                <? endif; ?>

                <div class="product__column product__column_small">
                    <div class="thumbnails js-product__thumbnails">
                        <div class="thumbnails__slide">
                            <img class="thumbnails__image"
                                 src="<?= $model->getSRCPhoto(['index' => 0, 'suffix' => '_sm']) ?>"
                                 alt="<?= $model->name ?>">
                        </div>
                        <? if (!empty($model->photos)): ?>
                            <? foreach ($gallery as $photo) : ?>
                                <div class="thumbnails__slide">
                                    <img class="thumbnails__image"
                                         src="<?= $photo->getSRCPhoto(['parent_id' => $model->id, 'suffix' => '_sm']) ?>"
                                         alt="<?= $photo->name ?>">
                                </div>
                            <? endforeach; ?>
                        <? endif; ?>
                    </div>
                </div>

                <div class="product__column">
                    <div class="slider js-product__slider">
                        <div class="slider__slide">
                            <div class="adjuster">
                                <canvas class="adjuster__canvas" height="1" width="1"></canvas>
                                <img class="adjuster__image adjuster__image_centered"
                                     data-lazy="<?= $model->getSRCPhoto(['index' => 0, 'suffix' => '_big']) ?>"
                                     alt="<?= $model->name ?>">
                            </div>
                        </div>
                        <? foreach ($gallery as $photo) : ?>
                            <div class="slider__slide">
                                <div class="adjuster">
                                    <canvas class="adjuster__canvas" height="1" width="1"></canvas>
                                    <img class="adjuster__image adjuster__image_centered"
                                         data-lazy="<?= $photo->getSRCPhoto(['parent_id' => $model->id, 'suffix' => '_big']) ?>"
                                         alt="<?= $photo->name ?>">
                                </div>
                            </div>
                        <? endforeach; ?>
                    </div>
                </div>
            </div>
            <? if (!empty($model->text)) : ?>
                <div class="product-info product-info_bottom">
                    <div class="product-info__title title title_size_s title_low">Описание</div>
                    <div class="user-content">
                        <?= $model->text ?>
                    </div>
                </div>
            <? endif; ?>
        </div>
        <div class="columns__item columns__item_small">
            <div class="params">
                <div class="params__title title title_size_s title_low"> <?= $model->name ?></div>
                <? if (!empty($model->article)) : ?>
                    <div class="params__field">
                        <div class="user-content">
                            <?= $model->article ?>
                        </div>
                    </div>
                <? endif; ?>
                <? if (!empty($model->params)): ?>
                    <? $params = $model->getParams()->orderBy(['pos'=>SORT_DESC])->all();?>
                    <? foreach ($params as $param): ?>
                        <? /* @var $param \backend\models\ProductParam */ ?>
                        <? $options = json_decode($param->value) ?>
                        <? $costCurNone += $options[0]->cost; ?>
                        <? $costOldNone += $options[0]->cost; ?>
                  
                        <div id="<?= $param->id ?>_field" class="params__field">
                            <div id="label_<?= $param->id ?>" class="params__label"><?= $param->name ?></div>
                            <div class="select">
                                <select id="select_<?= $param->id ?>" type="select" class="select__input">
                                    <? foreach ($options as $key => $option): ?>
                                        <option cost="<?= $option->cost ?>"
                                                value="<?= $key ?>"><?= $option->value ?></option>
                                    <? endforeach; ?>
                                </select>
                                <div class="select__dummy"></div>
                            </div>
                        </div>

                    <? endforeach; ?>
                <? endif; ?>
                <? if (!empty($model->anons)) : ?>
                    <div class="params__field">
                        <div class="user-content">
                            <?= $model->anons ?>
                        </div>
                    </div>
                <? endif; ?>
                <? $captionText = \backend\models\StaticTextItem::findOne(['id'=>\backend\models\StaticTextItem::PRODUCT_CAPTION_TEXT])->text?>
                <? if (!empty($captionText)):?>
                <div class=params__field-l>
                    <div class=params__caption><?= $captionText?>
                    </div>
                </div>
                <? endif; ?>
                <div class="params__price">
                    <div class="price">
                        <? if (!empty($model->cost_old)): ?>
                            <div cost-none="<?= $model->cost_old ?>"
                                 class="price__old"><?= number_format($costOldNone, 0, '', '&nbsp;') ?>&nbsp;р.
                            </div>
                        <? endif; ?>
                        <div cost-none="<?= $model->cost ?>"
                             class="price__current"><?= number_format($costCurNone, 0, '', '&nbsp;') ?>&nbsp;р.
                        </div>
                    </div>
                </div>

                <div class="params__field">
                    <div id="count" class="params__label">Количество</div>
                    <div class="select">
                        <div class="count-select">
                            <div id="minus" class="count-select__item count-select__item_button"></div>
                            <div class="count-select__item">
                                <input id="count_field" class="count-select__input"
                                       prodID="<?= $model->id ?>" type="number" value="1">
                            </div>
                            <div id="plus"
                                 class="count-select__item count-select__item_button count-select__item_button_minus"></div>
                        </div>
                    </div>
                </div>

                <div class="params__submit">
                    <a href="#popup_add-to-cart" id="add-to-cart" prodID="<?= $model->id ?>"
                       class="button button_size_l js-popup__open" data-effect="mfp-zoom-in">Купить</a>

                </div>
            </div>
        </div>
</section>
<? if (!empty($otherProducts)) : ?>
    <section class="page__section-main">
        <h2 class="title title_top">
            Другие предметы из коллекции
        </h2>
        <div class="items-slider">
            <div class="items-slider__container swiper-container js-items-slider">
                <?= $this->render('@frontend/views/category/_item', compact('otherProducts', 'index')); ?>
            </div>
            <div class="items-slider__button js-items-slider__prev">
                <svg class="items-slider__icon">
                    <use xlink:href="#icon-arrow-down"></use>
                </svg>
            </div>
            <div class="items-slider__button items-slider__button_next js-items-slider__next">
                <svg class="items-slider__icon">
                    <use xlink:href="#icon-arrow-down"></use>
                </svg>
            </div>
            <? if (false) {
                echo '<div class="slider-pagination slider-pagination_bottom js-items-slider__pagination"></div>';
            } ?>
        </div>
    </section>
<? endif; ?>

<!-- Высплывашка, добавить в корзину-->
<div id="popup_add-to-cart" class="popup mfp-with-anim mfp-hide small">
    <div class="title title_size_s title_centered">Товар добавлен в корзину</div>
    <div class="page__section-xs" style="text-align: center">
        Вы можете продолжать <a class="link" href="<?= $model->linkOut ?>">покупки</a>, либо перейти в <a class="link"
                                                                                                          href="/cart/">корзину</a>!
    </div>
</div>