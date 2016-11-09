<?php
\Phpfox_Module::instance()->addServiceNames([
    'cmSlider'          => '\Apps\CM_DigitalDownload\Service\Slider',
]);

 route('/digital', function(){
     $oSlider = Phpfox::getService('cmSlider');
     $oSlider->setKey(1);

     $form = $oSlider->getForm();
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