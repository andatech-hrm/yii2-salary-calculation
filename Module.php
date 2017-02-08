<?php

namespace andahrm\salaryCalculation;

/**
 * salaryCalculation module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'andahrm\salaryCalculation\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
       $this->layout= 'main';
        parent::init();

        // custom initialization code goes here
    }
}
