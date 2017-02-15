<?php

namespace andahrm\salaryCalculation\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\Model;
use andahrm\positionSalary\models\PersonPositionSalary;

/**
 * This is the model class for table "section".
 *
 * @property integer $id
 * @property string $code
 * @property string $title
 * @property integer $status
 * @property string $note
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 *
 * @property Position[] $positions
 */
class Calculator extends PersonPositionSalary
{
    public $section_id;
    public $percent;
    public $previous;
    public $person_type_id;
    public $position_line_id;
    public $dataPrevious;
    public $dataStepCal;
    
    
    public function attributeLabels()
    {
        $attr = parent::attributeLabels();
        $attr['previous'] = Yii::t('andahrm/position-salary', 'ข้อมูลย้อนหลัง(ปี)');
        return $attr;
    }
  
}
