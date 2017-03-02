<?php

namespace andahrm\salaryCalculation\controllers;

use Yii;
use yii\web\Controller;

use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use andahrm\structure\models\BaseSalary;
use andahrm\structure\models\Structure;
use andahrm\structure\models\FiscalYear;
use andahrm\structure\models\PositionLine;
use andahrm\structure\models\Position;
use yii\data\ActiveDataProvider;
use andahrm\positionSalary\models\PersonPositionSalary;
use andahrm\positionSalary\models\Assessment;
use andahrm\salaryCalculation\models\Calculator;

/**
 * Default controller for the `salaryCalculation` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
     public function actionIndex()
    {
        
        #ขั้นการขึ้น
        $rangeStep = range(0.5,1.5,0.5);
        $rangeStep = array_combine($rangeStep,$rangeStep);
        
        $model = new Structure();
        $modelFiscalYear = new FiscalYear();
        $dataProvider = [];
        $modelFiscalYearPrevious = [];
        $data=[];
        $count = [];
        if($model->load(Yii::$app->request->get())){
            
            $modelFiscalYear->load(Yii::$app->request->get());
            
            $data  = Calculator::find()
            ->joinWith('position', false, 'INNER JOIN')
            ->joinWith('personPosition', false, 'INNER JOIN')
            ->joinWith('assessment', false, 'LEFT JOIN')
            ->where([
                'position.section_id' => $model->section_id,
                'position.person_type_id' => $model->person_type_id,
                'position.position_line_id' => $model->position_line_id,
            ])
            ->groupBy([
                //'person_position_salary.user_id',
                'person_position_salary.position_id'
            ])
            ->orderBy(['person_position_salary.adjust_date'=>SORT_DESC])
            ->all();
            $data = ArrayHelper::index($data, 'user_id');
            
           //$users = ArrayHelper::getColumn($data, 'user_id');
           
           #ดึงปีย้อนหลัง
           $previous = date("Y", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . " - {$model->previous} year"));
           
           #ดึงข้อมูลปีย้อนหลัง
           $modelFiscalYear = FiscalYear::find()->where(['year'=>$modelFiscalYear->year,'phase'=>$modelFiscalYear->phase])->one();
           
           #ดึงข้อมูลปีย้อนหลังทั้งหมด
           $modelFiscalYearPrevious = FiscalYear::find()
           ->where(['between','year',$previous,$modelFiscalYear->year])
           ->all();
           
           $modelFiscalYearPrevious = ArrayHelper::index($modelFiscalYearPrevious, 'phase',  'year');
           
        //     echo "<pre>";
        //   print_r($modelFiscalYearPrevious);
           $count = [];
           
           $count['phase']=0;
           $newPrevious=[];
           $newStepCal=[];
           #นำมาใส่ในปีย้อนหลัง
           foreach($data as $us){
               $count['year']=0;
               $newPrevious=[];
               foreach($modelFiscalYearPrevious as $kYear =>$year){
                   $newPrevious[$kYear]=[];
                   foreach($year as $kPhase => $phase){
                       $level = PersonPositionSalary::find()
                       ->where(['user_id'=>$us->user_id])
                       ->andWhere(['between','adjust_date',$phase->date_start,$phase->date_end])
                       ->one();
                       //$phase->step = $level->step?$level->step:0;
                       $newPrevious[$kYear][$kPhase] = $level;
                   }
                   
               }
               $us->dataPrevious = $newPrevious;
               
               
               $index_step = 0;
               $salay_previous = $us->salary;
               #คำนวนหาเงินเดือน
               foreach($rangeStep as $step => $val){
                   $index_step+=0.5;
                   $baseSalary = BaseSalary::find()->where([
                       'position_type_id' => $us->position->position_type_id,
                       'position_level_id'=> $us->position->position_level_id,
                       'step'=>$us->step + $index_step,
                       ])->one();
                   $newStepCal[$step]=$baseSalary;
                   $newStepCal[$step]->diff = $baseSalary->salary - $salay_previous ;
                   $salay_previous = $baseSalary->salary;
               }
               $us->dataStepCal = $newStepCal;
           }
           //exit();
           
           
        //   $salary  = Calculator::find()
        //     ->where([
        //         'adjust_date' => $modelFiscalYear,
        //     ])
        //     ->groupBy([
        //         //'person_position_salary.user_id',
        //         'person_position_salary.position_id'
        //     ])
        //     ->orderBy(['adjust_date'=>SORT_DESC])
        //     ->all();
        //     $result = ArrayHelper::index($salary, 'phase', [function ($element) {
        //         return $element['id'];
        //     }, 'device']);
                        
            
           //$data = ArrayHelper::index($data, 'user_id');
           
           
           
        }
        
        
        
        
        
            return $this->render('index', [
                'model' => $model,
                'modelFiscalYear'=>$modelFiscalYear,
                'modelFiscalYearPrevious'=>$modelFiscalYearPrevious,
                //'dataProvider' => $dataProvider,
                'data'=>$data,
                'count'=>$count,
                'rangeStep'=>$rangeStep
            ]);
        
    }
    
    
    protected function MapData($datas,$fieldId,$fieldName){
     $obj = [];
     foreach ($datas as $key => $value) {
         array_push($obj, ['id'=>$value->{$fieldId},'name'=>$value->{$fieldName}]);
     }
     return $obj;
    }
 
 ###############
     public function actionGetPersonType() {
     $out = [];
      $post = Yii::$app->request->post();
     if ($post['depdrop_parents']) {
         $parents = $post['depdrop_parents'];
         if ($parents != null) {
             $section_id = $parents[0];
             $out = $this->getPersonType($section_id);
             echo Json::encode(['output'=>$out, 'selected'=>'']);
             return;
         }
         }
         echo Json::encode(['output'=>'', 'selected'=>'']);
     }

      protected function getPersonType($section_id){
         $datas = Position::find()->where(['section_id'=>$section_id])->groupBy('person_type_id')->all();
         return $this->MapData($datas,'person_type_id','personTypeTitle');
     }
 
 #############
    public function actionGetPositionLine() {
     $out = [];
      $post = Yii::$app->request->post();
     if (isset($post['depdrop_parents'])) {
         $parents = $post['depdrop_parents'];
         $section_id = null;
         $person_type_id = null;
         if (isset($parents) && $parents != null) {
             $section_id = $parents[0];
             if(isset($parents[1]))
             $person_type_id = $parents[1];
             
             $out = $this->getPositionLine($section_id,$person_type_id);
             echo Json::encode(['output'=>$out, 'selected'=>'']);
             return;
         }
         }
         echo Json::encode(['output'=>'', 'selected'=>'']);
     }

      protected function getPositionLine($section_id,$person_type_id=null){
         $datas = PositionLine::find()
         ->joinWith('position')->where([
             'position.section_id'=>$section_id,
             ])
         ->andFilterWhere(['position.person_type_id'=>$person_type_id])
         ->all();
         return $this->MapData($datas,'id','title');
     }
     

 #############
 
     public function actionGetPosition() {
     $out = [];
      $post = Yii::$app->request->post();
     if ($post['depdrop_parents']) {
         $parents = $post['depdrop_parents'];
         if ($parents != null) {
             $section_id = $parents[0];
             $person_type_id = $parents[1];
             $position_line_id = $parents[2];
             $out = $this->getPosition($section_id,$person_type_id,$position_line_id);
             echo Json::encode(['output'=>$out, 'selected'=>'']);
             return;
         }
         }
         echo Json::encode(['output'=>'', 'selected'=>'']);
     }

      protected function getPosition($section_id,$person_type_id = null,$position_line_id = null){
         $datas = Position::find()->where([
             'section_id'=>$section_id,
             'status'=> Position::STASUS_FREE,
             ])
         ->andFilterWhere(['person_type_id'=>$person_type_id])
         ->andFilterWhere(['position_line_id'=>$position_line_id])
         ->all();
         return $this->MapData($datas,'id','codeTitle');
     }
     
}
