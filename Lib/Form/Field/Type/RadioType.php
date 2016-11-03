<?php
namespace Core\Library\Form\Field\Type;


use Core\Library\Form\Field\AbstractType;

class RadioType extends AbstractType
{
    protected $aInfo  = [
        'template' => 'Core:Type/radio',
    ];


    /**
     * @return void
     */
    protected function assignVars()
    {
        parent::assignVars();
        $this->oView->assign([
           'name'   => $this->aInfo['name'],
           'title'  => $this->aInfo['title']
        ]);
    }

}