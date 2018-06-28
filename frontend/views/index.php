<?php
/* @var $this yii\web\View */

use backend\models\Category;


?>


<body>


 <div id="container">
    <div id="wrapper">
        <div id="portfolio">
            <ul id="gallery" class="grid">

                <? foreach ($models as $ind => $item): ?>
                    <? /* @var $product \backend\models\Category */ ?>
                    <li data-id="id-<?= $ind?>" class="photography">
                        <a class="ticket" href="<?= $item->linkOut ?>">
                            <img src="<?=  $item->getSRCPhoto (['suffix' => '_mid']) ?>" height="120" width="211" alt="" style="opacity: 1;">
                            <br>
                            <h2><?= $item->name ?></h2>

                        </a>
                    </li>
                <? endforeach; ?>
            </ul>
        </div>
    </div>

</body>
