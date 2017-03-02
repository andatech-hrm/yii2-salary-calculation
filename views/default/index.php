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
// print_r($modelFiscalYear);
$countPrevious =count($modelFiscalYearPrevious);
$th1 = $countPrevious*2;

//print_r($rangeStep);
$css = "
    table.tData > tr > th {
        text-align: center;
        vertical-align: middle;
    }
";
$this->registerCss($css);
?>


<div class="salaryCalculation-default-index">
    
    <?= $this->render('_form', [
        'model' => $model,
        'modelFiscalYear'=>$modelFiscalYear,
    ]) ?>
    
    <hr/>
    
     <div class="row">
        <div class="col-sm-12 text-center">
        <?=Html::tag('h4',Yii::t('andahrm/calculator','Information moves in {previous} ',['previous'=>$model->previous]))?>
        <?=Html::tag('h4',$modelFiscalYear->phaseTitle);?>
        <?=Html::tag('h4','กลุ่มบุคคล');?>
        <br />
        </div>
    </div>
    
    
<div class="row">
    <div class="col-sm-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered tData">
        
        <thead>
            <tr>
                <th rowspan="3" >#</th>
                <th rowspan="3" class="text-nowrap text-center">ชื่อ-สกุล / ตำแหน่ง</th>
                <th rowspan="3" >ขั้น</th>
                <th rowspan="3" >อัตราเงินเดือน</th>
                <!--Pass-->
                <th class="text-center" colspan="<?=$th1?>" >ข้อมูลย้อนหัง <?=$model->previous?> ปี</th>
                <!--Pass-->
                <th rowspan="3" >ผลการประเมิน</th>
                <th rowspan="3" >ร้อยละ %</th>
                <th rowspan="3" >ระดับ</th>
                <!--Pass-->
                <?php 
                #Step
                foreach($rangeStep as $step => $val):
                ?>
                     <th rowspan="2" colspan="2">กรณีเพิ่ม</th>
                <?php 
                endforeach;
                ?>
                <!--Pass-->
                <th rowspan="3" >หมายเหตุ</th>
            </tr>
            
            <tr>
                <!--Pass-->
                <?php foreach($modelFiscalYearPrevious as $year=>$val):?>
                <th class="text-center" colspan="2">งบปี <?=$year+543?></th>
                <?php endforeach;?>
                <!--Pass-->
            </tr>
            <tr>
                <!--Pass-->
               
                
                <?php 
                #Previous
                foreach($modelFiscalYearPrevious as $year=>$phase):
                    foreach($phase as $val):
                ?>
                     <th class="text-nowrap text-center"><?=$val->dateStart?></th>
                <?php 
                    endforeach;
                endforeach;
                ?>
                
                <?php 
                #Step
                foreach($rangeStep as $step => $val):
                ?>
                     <th ><?=$step?></th>
                     <th >เป็นเงิน</th>
                <?php 
                endforeach;
                ?>
                <!--Pass-->
            </tr>
        </thead>
        
        <tbody>
            <?php
            
                #รวมเงินเดือนปัจจุบัน
                    $totalSalary = 0; 
                    $totalStepSalary = []; 
            if($data){
                //$data = $dataProvider->getModels();
                foreach($data as $key => $model):
                    
                    $assessment=$model->assessment;
                    
                    $totalSalary += $model->salary;
            ?>
                <tr >
                    <td><?=($key+1)?></td>
                    <td class="text-nowrap"><?=$model->user->fullname?><br/><?=$model->user->positionTitle?></td>
                    <td><?=$model->step?></td>
                    <td><?=Yii::$app->formatter->asDecimal($model->salary)?></td>
                        <?php 
                        foreach($model->dataPrevious as $year=>$phase):
                            // echo "<pre>";
                            // print_r($model->dataPrevious);
                            // exit();
                            foreach($phase as $val):
                        ?>
                            <td ><?=isset($val->step_adjust)?$val->step_adjust:''?></td>
                        <?php 
                            endforeach;
                        endforeach;
                        ?>
                    <td><?=$assessment?$assessment->assessment:null?></td>
                    <td><?=$assessment?$assessment->percent:null?></td>
                    <td><?=$assessment?$assessment->level:null?></td>
                    
                     <?php 
                        foreach($model->dataStepCal as $step=>$val):
                            // echo "<pre>";
                            // print_r($model->dataPrevious);
                            // exit();
                            if(!isset($totalStepSalary[$step]['salary'])){
                            $totalStepSalary[$step]['salary'] = 0;
                            $totalStepSalary[$step]['diff'] = 0;
                            }
                            
                            $totalStepSalary[$step]['salary'] += $val->salary;
                            $totalStepSalary[$step]['diff'] += $val->diff;
                        ?>
                        <td ><?=Yii::$app->formatter->asDecimal($val->salary)?></td>
                        <td ><?=Yii::$app->formatter->asDecimal($val->diff)?></td>
                        <?php 
                        endforeach;
                        ?>
                    
                    <td></td>
                </tr>
                
            <?php
                endforeach;
            }else{
            ?>
            <tr><td colspan ="20" class="text-center">โปรดเลือกตัวเลือกก่อน</td></tr>
            <?php }?>
        </tbody>
        <?php if($data):?>
        <tfoot>
            <tr><td>&nbsp;</td><td><?=Yii::t('andahrm','Total')?></td><td>&nbsp;</td>
            <td><?=Yii::$app->formatter->asDecimal($totalSalary)?></td>
            <td colspan="<?=$th1?>">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <?php
            foreach($totalStepSalary as $k=>$val):?>
                <td><?=Yii::$app->formatter->asDecimal($val['salary'])?></td>
                <td><?=Yii::$app->formatter->asDecimal($val['diff'])?></td>
            <?php endforeach;?>
               <td>&nbsp;</td>
            </tr>
        </tfoot>
        <?php endif;?>
    </table>
        </div>
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