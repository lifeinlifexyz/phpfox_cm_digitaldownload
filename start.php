<?php
\Phpfox_Module::instance()->addServiceNames([
    'cmSlider'          => '\Apps\CM_DigitalDownload\Service\Slider',
    'formBuilderFactory'          => '\Apps\CM_DigitalDownload\Lib\Form\BuilderFactory',
]);

 route('/digital', function(){
     $builder = Phpfox::getService('formBuilderFactory')->make();
     $form = $builder->getBindedForm('cmSlider', 4);
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