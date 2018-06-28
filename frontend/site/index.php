

<? if (!empty($banners)): ?>
    <div class="main-slider swiper-container js-main-slider">
        <div class="main-slider__wrapper swiper-wrapper">
            <? foreach ($banners as $banner): ?>
                <? /* @var $banner \backend\models\Banner */ ?>
                <div class="slide swiper-slide">
                    <img class="slide__image swiper-lazy" alt="<?= $banner->name ?>"
                         src="<?= $banner->getSRCPhoto(['suffix' => '_big']) ?>">
                    <div class="slide__content">
                        <div class="slide__cell">

                            <? if (!empty($banner->text)): ?>
                                <div class="slide__caption"><?= $banner->text ?></div>
                            <? endif; ?>
                            <? if (!empty($banner->link) || !empty($banner->link_text)): ?>
                                <div class="slide__button">
                                    <a class="btn"
                                       href="<?= $banner->link ?>"><?= $banner->link_text ?>
                                    </a>
                                </div>
                            <? endif; ?>
                        </div>
                    </div>
                </div>
            <? endforeach; ?>
        </div>
        <div class="main-slider__pagination js-main-slider__pagination"></div>

    </div>
<? endif; ?>
<!-- End Slider -->
<!-- Begin Wrapper -->
<div id="wrapper">
    <!-- Begin Intro -->
    <div class="intro">
        <h1>Популярное</h1>
    </div>
    <!-- End Intro -->
    <!-- Begin About -->
    <div id="about">

        <? foreach ($products as $product): ?>
            <? /* @var $product \backend\models\Product */ ?>
            <div class="one-fourth">
                <a href="<?= $product->link ?>">

                    <img src="<?= $product->getSRCPhoto(['suffix' => '_md','index'=>0]) ?>" height="120" width="211" alt="<?= $product->name?>" />
                    <h2 align="center"><?= $product->name?></h2>
                </a>
                <p><?= $product->anons?></p>
            </div>
        <? endforeach; ?>



    </div>
    <div class="intro">
     <p>  <?= $staticText[\backend\models\StaticTextItem::TEXT]['text']?></p>
    </div>
</div>

