<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use andahrm\structure\models\PersonType;
use andahrm\structure\models\BaseSalary;
use andahrm\structure\models\PositionLine;
use andahrm\structure\models\Section;
use kartik\widgets\DepDrop;
/* @var $this yii\web\View */
/* @var $model andahrm\structure\models\BaseSalary */
/* @var $form ActiveForm */
?>
<div class="default-_form">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' => [
            'data-pjax' => true
            ]
    ]
    ); ?>

    <div class="row">
            
    <div class="row">
        
      <div class="col-sm-3">
        <?= $form->field($model, 'section_id')->dropDownList(Section::getList(),['prompt'=>Yii::t('app','Select')]) ?>
      </div>
      
      <div class="col-sm-3">
        <?= $form->field($model, 'person_type_id')->dropDownList(PersonType::getList(),[
          'prompt'=>Yii::t('app','Select'),
          'id'=>'ddl-person_type',
        ]) ?>
      </div>
      

      <div class="col-sm-3">
        <?= $form->field($model, 'position_line_id')->dropDownList(PositionLine::getListGroup(),['prompt'=>Yii::t('app','Select')])->widget(DepDrop::classname(), [
            'options'=>['id'=>'ddl-position_type'],
            'data'=> PositionLine::getListByPersonType($model->person_type_id),
            'pluginOptions'=>[
                'depends'=>['ddl-person_type'],
                'placeholder'=>Yii::t('app','Select'),
                'url'=>Url::to(['get-position-line'])
            ]
        ]); ?>
      </div>
    </div>  
            
            
      
      
    </div>  
    
        <div class="form-group">
            <?= Html::submitButton(Yii::t('andahrm/calculator', 'Submit'), ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- default-_form -->
