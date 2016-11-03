<?php
 route('/digital', function(){
     $builder = new \Apps\CM_DigitalDownload\Lib\Form\Builder(request(), Core\Controller::$__view, new \Apps\CM_DigitalDownload\Lib\Form\Validator\Validator());

     $form = $builder->build([
         [
             'type' => 'string',
             'name' => 'title',
             'title' => 'Title',
             'rules' => 'required|3:35:length',
             'errorMessages' => [
                 'title.required' => 'required',
             ]
         ],
         [
             'type' => 'string',
             'name' => 'int',
             'title' => 'Int',
             'rules' => '3:min|10:max',
             'errorMessages' => [
                 'int.min' => 'кичине',
                 'int.max' => 'чон'
             ]
         ],
         [
             'type' => 'string',
             'name' => 'int2',
             'title' => 'Int2',
             'rules' => '3:min|10:max',
             'errorMessages' => [
                 'int2.min' => 'кичине2',
                 'int2.max' => 'чон2'
             ]
         ],
         [
             'type' => 'submit',
             'name' => 'save',
             'title' => 'save',
         ],
     ]);
     if ($_POST && $form->isValid())
     {
         echo "<h1>Is Valid</h1>";
         echo '<pre>';
            var_dump($form->getFieldValue('title'));
            var_dump($form->getFieldsValue());
         echo '</pre>';
     }
     echo $form;
    return 'controller';
 });