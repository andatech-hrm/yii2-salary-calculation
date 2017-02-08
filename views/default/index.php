<?php

use yii\helpers\Html;
use yii\helpers\Url;

use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel andahrm\person\models\PersonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('andahrm/calculator', 'Salary Calculation');
$this->params['breadcrumbs'][] = ['label' => Yii::t('andahrm/structure', 'Base Salaries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="salaryCalculation-default-index">
    
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
    
    
    <div class="row">
        <div class="col-sm-12">
        <table class="table table-striped table-bordered">
            
            <thead>
                <tr>
                    <th rowspan="3" >#</th>
                    <th rowspan="3" >ชื่อ-สกุล / ตำแหน่ง</th>
                    <th rowspan="3" >ขั้น</th>
                    <th rowspan="3" >อัตราเงินเดือน</th>
                    <th colspan="6" >ข้อมูลย้อนหลัง 3 ปี</th>
                </tr>
                <tr>
                    <th colspan="2">งบปี 55</th>
                    <th colspan="2">งบปี 55</th>
                </tr>
                <tr>
                    <th >1/4/56</th>
                    <th >1/4/56</th>
                    <th >1/4/56</th>
                    <th >1/4/56</th>
                </tr>
            </thead>
            
            <tbody>
                <?php
                if($dataProvider){
                    $data = $dataProvider->getModels();
                    
                    foreach($data as $key => $model):
                ?>
                    <tr >
                        <td><?=($key+1)?></td>
                        <td><?=$model->user->fullname?><br/><?=$model->user->positionTitle?></td>
                        <td><?=$model->step?></td>
                        <td><?=$model->salary?></td>
                        <td><span class="not-set">(ไม่ได้ตั้ง)</span></td>
                    </tr>
                    
                <?php
                    endforeach;
                }
                ?>
            </tbody>
            
            <tfoot>
                <tr><td>&nbsp;</td><td>Total</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            </tfoot>

        </table>
        </div>
    </div>
        <?php
    
       /* 
        echo GridView::widget([
        'dataProvider' => $dataProvider,
        'showFooter' => true,
        'showHeader' => true,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['rowspan'=>3]
            ],
            [
                'attribute'=>'user_id',
                'value' =>'user.fullname',
                'headerOptions' => ['rowspan'=>3],
                'footer' => Yii::t('andahrm/calculator','Total'),
            ],
            [
                'attribute'=>'position_id',
                'value' =>'position.title',
                'headerOptions' => ['rowspan'=>3],
            ],
             [
                'attribute'=>'step',
                'headerOptions' => ['rowspan'=>3],
            ],
            [
                'attribute'=>'salary',
                'headerOptions' => ['rowspan'=>3],
            ],
            ['label'=>'ข้อมูลย้อนหลัง 3 ปี',
            'headerOptions' => ['colspan'=>6],
            ],
            
            
           
            
            
            ]
        ]);
        
        */
    
?>
    
</div>

<?php
$js ='

//$(".grid-view table thead tr th:eq(0)").attr("rowspan",2);
$(".grid-view table thead").append("<tr><th>งบปี</th></tr>");
$(".grid-view table thead").append("<tr><th>1/4/56</th></tr>");

/*
$(".grid-view table thead tr th").each(function(index){
    $(this).attr("rowspan",2);
});
*/


';




//$this->registerJs($js);