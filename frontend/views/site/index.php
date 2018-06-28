<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use backend\assets\SortableAsset;

SortableAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \backend\models\Category::NAME;
//$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'] = $this->context->getBreadcrumbs();

$nested_level = count($this->context->getBreadcrumbs())-1;
$allow_nested_level = 1;

$sort_url  = Url::to(['sort']) . '?min_pos=' . end($dataProvider->models)->pos;
$saveForMoveUrl = urlTo(['save-for-move']);
$haveItemsForMove = \backend\models\Category::getFromSession('save_for_move') ? 1 : 0;
$insertUrl = urlTo(['insert','id' => Yii::$app->request->queryParams['CategorySearch']['category_id'] ? Yii::$app->request->queryParams['CategorySearch']['category_id'] : 0]);
if($haveItemsForMove)
    $idsToMove = implode(',',\backend\models\Category::getFromSession('save_for_move'));
$js2 = <<<JS2

    var saveForMoveUrl = '$saveForMoveUrl';
    var haveMove = $haveItemsForMove;
    var idsToMove = [$idsToMove];
    
    for (var id in idsToMove)
    {
        $('tr#' + idsToMove[id]).remove();
    }
    if(haveMove)
    {
        showInsertButton();
    }
    else 
    {
        hideInsertButton();
    }
    
    //hideButtons();
    
    function saveForMove() {
        var ids = '';
        $('input[name="copy_move"]:checked').each(function(i) {
            ids += ',' + $(this).attr('model_id');
            $('tr#' + $(this).attr('model_id')).remove();
        })
        ids = ids.replace(',','');
        
        $.get(saveForMoveUrl + '?ids=' + ids );
        hideButtons();
        // showInsertButton();
    }
    
    function saveForCopy() {
      
    }
    function showInsertButton() {
      $('.btn-danger').show();
    }
    function hideInsertButton() {
      $('.btn-danger').hide();
    }
    function showButtons() {
      $('.btn-warning').show();
    }
    
    function hideButtons() {
      $('.btn-warning').hide();
    }
    
    function activateMoveCopyButtons(checked) {
          if(checked || $('input[name="copy_move"]:checked').length > 0)
          {
               showButtons();
          }
          else 
          {
              hideButtons();
          }
    }
JS2;
$this->registerJs($js2,\yii\web\View::POS_END);

$js =<<<JS
    
    init_sortable('tbody',"$sort_url");
    


JS;

$p  = Yii::$app->request->queryParams;
if($dataProvider->totalCount>1 && (!isset(array_pop($p)['name']) || strlen(array_pop($p)['name']) == 0))
    $this->registerJs($js,\yii\web\View::POS_END);

$query_string = 'CategorySearch[category_id]=' .Yii::$app->request->queryParams['CategorySearch']['category_id'];
$query_string = $query_string ? '?'.$query_string . '&' : '?';
?>
<div class="category-index">

    <h1><?= Html::encode($this->title) ?> </h1>
<!--    --><?php // echo print_r(Yii::$app->request->queryParams['CategorySearch']['cateogry_id']) ?>

    <p>
        <?php if($nested_level < $allow_nested_level): ?>
            <?= Html::a('Добавить категорию', ['create','category_id' => Yii::$app->request->queryParams['CategorySearch']['category_id']], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
        <?php if($dataProvider->totalCount==0): ?>
            <?= Html::a('Добавить продукт', ['product/create', 'category_id' => Yii::$app->request->queryParams['CategorySearch']['category_id']], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
        &nbsp;
        &nbsp;
        <?= Html::button('Переместить', ['class' => 'btn btn-warning', 'style' => 'display:none;','onclick' => 'saveForMove()']) ?>
<!--        --><?//= Html::button('Скопировать', ['class' => 'btn btn-warning', 'onclick' => '$.get("");']) ?>
        <?= Html::a('Вставить здесь',$insertUrl ,['class' => 'btn btn-danger' , 'style' => 'display:none;']) ?>

    </p>
    <p>
        Выводить по <?= Html::dropDownList('page_size',Yii::$app->request->queryParams['per-page'],array_combine(range(10,210,5),range(10,210,5)),[
            'onchange'  =>  "location.href='{$query_string}per-page='+this.value;"
        ]); ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions'    => function($model,$id ,$index,$grid)
        {
            return ['id' => $id];
        },
        'summary'   => false,
        'columns' => [
//            [
//                'format'    => 'raw',
//                'contentOptions'=> ['style' => 'width: 20px'],
//                'value'     =>  function($model){
//                    return Html::checkbox('copy_move',null,[
//                        'onchange'   => "activateMoveCopyButtons(this.checked)",
//                        'model_id'    => $model->id
//                    ]);
//                }
//            ],
            [
                'attribute' =>  'name',
                'format'    =>  'raw',
                'value'     => function($m)
                {
                    return Html::a($m->name,$m->childLink);
                }

            ],
            [
                'label' => \backend\models\Category::attributeLabels()['active'],
                'format'    => 'raw',
                'value'     =>  function($m){
                    $activate_url = Url::to(['activate' , 'id' => $m->id]);
                    return Html::checkbox(null,$m->active,[
                        'onchange'   => "$.get('$activate_url')"
                    ]);
                }
            ],



            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions'=>[ 'style'=>'width: 120px'],
                'buttons' => [
                    'gallery' => function($url , $m , $key)
                    {
                        $url = Url::to(['category-photo/view', 'id' => $key , 'category_id' => Yii::$app->request->queryParams['CategorySearch']['category_id']],true);
                        return Html::a('&nbsp;<i class="fa fa-camera"></i>&nbsp;' ,$url);
                    },
                    'viewonsite' => function($url , $m , $key)
                    {

                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>' ,$m->linkOut , ['target' => '_blank']);
                    },
                    'update' => function($url , $m , $key)
                    {

                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>' ,Url::to(['update' , 'id' => $m->id , 'category_id' => Yii::$app->request->queryParams['CategorySearch']['category_id']]) /*, ['target' => '_blank']*/);
                    }
                ],
                'template' => '{update} {gallery} &nbsp;&nbsp; {delete}' /*{viewonsite} {gallery}*/
            ],
        ],
    ]); ?>
</div>
