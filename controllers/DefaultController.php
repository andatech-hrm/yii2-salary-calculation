<?php

namespace andahrm\salaryCalculation\controllers;

use Yii;
use yii\web\Controller;

use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use andahrm\structure\models\BaseSalary;
use andahrm\structure\models\Structure;
use andahrm\structure\models\PositionLine;
use andahrm\structure\models\Position;
use yii\data\ActiveDataProvider;
use andahrm\positionSalary\models\PersonPositionSalary;

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
        $model = new Structure();
        $dataProvider = [];
        if($model->load(Yii::$app->request->get())){
            
            $query  = PersonPositionSalary::find()
            ->joinWith('position', false, 'INNER JOIN')
            ->where([
                'position.section_id' => $model->section_id,
                'position.person_type_id' => $model->person_type_id,
                'position.position_line_id' => $model->position_line_id,
            ])
            ->groupBy([
                //'person_position_salary.user_id',
                'person_position_salary.position_id'
            ])
            ->orderBy(['person_position_salary.adjust_date'=>SORT_DESC]);
            
            $dataProvider = new ActiveDataProvider([
                    'query' => $query,
                    'pagination' => [
                        'pageSize' => 10,
                    ],
                    'sort' => [
                        'defaultOrder' => [
                            'created_at' => SORT_DESC,
                            'title' => SORT_ASC, 
                        ]
                    ],
                ]);
            
            
        }
        
        
        
        
        
            return $this->render('index', [
                'model' => $model,
                'dataProvider' => $dataProvider
            ]);
        
    }
    
    
    protected function MapData($datas,$fieldId,$fieldName){
     $obj = [];
     foreach ($datas as $key => $value) {
         array_push($obj, ['id'=>$value->{$fieldId},'name'=>$value->{$fieldName}]);
     }
     return $obj;
    }
 
    public function actionGetPositionLine() {
     $out = [];
      $post = Yii::$app->request->post();
     if ($post['depdrop_parents']) {
         $parents = $post['depdrop_parents'];
         if ($parents != null) {
             $person_type_id = $parents[0];
             $out = $this->getPositionLine($person_type_id);
             echo Json::encode(['output'=>$out, 'selected'=>'']);
             return;
         }
         }
         echo Json::encode(['output'=>'', 'selected'=>'']);
     }

      protected function getPositionLine($id){
         $datas = PositionLine::find()->where(['person_type_id'=>$id])->all();
         return $this->MapData($datas,'id','title');
     }
     
}
