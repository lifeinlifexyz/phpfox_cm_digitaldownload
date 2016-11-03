<?php
namespace Core\Library\Form\Field\Type;


use Core\Library\Form\Field\AbstractType;

class SelectType extends AbstractType
{
    protected $aInfo  = [
        'template' => 'Core:Type/select',
    ];


    /**
     * @return void
     */
    protected function assignVars()
    {
        parent::assignVars();
        $this->oView->assign([
           'name'   => $this->aInfo['name'],
           'title'  => $this->aInfo['title'],
           'items'  => $this->aInfo['items'],
           'active' => $this->aInfo['active']
        ]);
    }

}