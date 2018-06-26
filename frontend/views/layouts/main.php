<?php

/* @var $this \yii\web\View */

/* @var $content string */

use yii\helpers\Html;

use frontend\assets\AppAsset;



AppAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <?= Html::csrfMetaTags() ?>

    </head>
    <body class="body">
    <header  class="header">
    <div class="page" id="vue-app">
        <div id="container">
            <!-- Begin Header Wrapper -->
            <div id="page-top">
                <div id="header-wrapper">
                    <!-- Begin Header -->
                    <div id="header">
                        <!-- Begin Menu -->
                        <div id="menu-wrapper">
                            <div id="smoothmenu1" class="ddsmoothmenu">
                                <ul>
                                    <li><a href="index.php" class="selected">Главная</a></li>
                                    <li><a href="portfolio.html">Категории</a></li>
                                    <li><a href="contact.html">Обратная связь</a></li>
                                </ul>
                            </div>
                        </div>
                        <!-- End Menu -->
                    </div>
                    <!-- End Header -->
                </div>
            </div>
        </div>
    </div>

    </header>
        <? if (!empty($menuItems)): ?>
            <div class="navigation <?= ($isMain && !$isError) ? 'navigation_transparent' : '' ?> js-navigation">
                <div class="navigation__wrapper">
                    <div class="navigation__button js-navigation__button">
                        <span class="navigation__line"></span>
                        <span class="navigation__line"></span>
                        <span class="navigation__line"></span>
                    </div>
                    <nav class="header-nav navigation__content js-navigation__content">
                        <ul class="header-nav__list">

                            <? foreach ($menuItems as $item): ?>
                                <li class="<?= $item['class'] ?> <?= $item['active'] ?>">
                                    <? if (isset($item['subItems']) && !empty($item['subItems'])): ?>
                                        <a class="header-nav__link"
                                           href="<?= $item['url'] ?>">
                                            <?= $item['label'] ?>
                                            <svg class="header-nav__icon">
                                                <use xlink:href="#icon-arrow-down"></use>
                                            </svg>
                                        </a>
                                        <div class="header-nav__dropdown">
                                            <div class="dropdown">
                                                <ul class="dropdown__content">
                                                    <? foreach ($item['subItems'] as $subItem): ?>
                                                        <li class="dropdown__item <?= ($subItem['active']) ? 'active' : '' ?>">
                                                            <a class="dropdown__link"
                                                               href="<?= $subItem['url'] ?>"><?= $subItem['label'] ?></a>
                                                        </li>
                                                    <? endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    <? else: ?>
                                        <a href="<?= $item['url'] ?>"
                                           class="header-nav__link"><?= $item['label'] ?></a>
                                    <? endif; ?>
                                </li>
                            <? endforeach; ?>
                        </ul>
                    </nav>
                </div>
            </div>
        <? endif; ?>
        <main class="main">
            <? if ((!$isMain && !$isContacts) || $isError): ?>
                <div class="wrapper">
                    <div class="page__section-xs">
                        <?= \backend\widgets\AndBreadcrumbs::widget([
                            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                            'tag' => 'ul',
                            'options' => ['itemtype' => "http://schema.org/BreadcrumbList", 'class' => 'breadcrumb'],
                            'itemTemplate' => '<li class="breadcrumb__list" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">{link}</li>',
                            'activeItemTemplate' => '<li class="breadcrumb__list active">{link}</li>',
                            'openContainerTag' => $this->params['breadcrumbsOpenContainerTag'],
                            'closeContainerTag' => $this->params['breadcrumbsCloseContainerTag'],
                        ])
                        ?>
                    </div>

                    <?= $content ?>
                </div>


            <? else: ?>

                <?= $content ?>

            <? endif; ?>
        </main>

        <footer class="footer">
            <div id="footer-wrapper">
                <div id="footer">
                    <div id="footer-content">
                        <p>Email: starmovies@mail.ru     8-800-555-35-35</p>
                        <div id="socials">
                            <ul>
                                <li><a href="#"><img src="img/icon-twitter.png" alt="" /></a></li>
                                <li><a href="#"><img src="img/icon-facebook.png" alt="" /></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </footer>

        <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>