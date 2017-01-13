<?php

namespace Apps\CM_DigitalDownload\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Module;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class ApplyOptionsController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {

        Phpfox::isUser(true);
        $aOptions = $this->getParam('aOptions');
        $oDD = $this->getParam('oDD');
        $aPlan = $this->getParam('aPlan');

        $aFreeOptions = isset($aOptions['free']) ? $aOptions['free'] : [];
        $aPaidOptions = isset($aOptions['paid']) ? $aOptions['paid'] : [];
        $extraOptions = Phpfox::getService('digitaldownload.plan')->getExtraOptions();

        if (count($aFreeOptions) > 0) {
            //apply free options
            $aVal = [];
            foreach($aFreeOptions as $sOptionName => $iOption) {
                if (in_array($sOptionName, $extraOptions)) {
                    $aVal[$sOptionName] = (int) $iOption;
                }
            }
            Phpfox::getService('digitaldownload.dd')->updateById($oDD['id'], $aVal);
        }

        if (!(count($aPaidOptions) > 0) && (isset($aVal) && count($aVal) > 0)) {
            $this->url()->send('digitaldownload.add',['dd_id' => $oDD['id']], _p('Free options successfully applied'));
        }

        $bInvoice = ($this->request()->get('invoice') ? true : false);
        $iTotalPrice = 0;
        $aPaidOptionsInfo = [];
        if (isset($aPaidOptions['activate'])) {
            $aPaidOptionsInfo['activate'] = [
                'caption' => _p('Activation'),
                'price' => $aPlan['price'],
            ];
            $iTotalPrice += $aPlan['price'];
        }

        foreach($extraOptions as $sOption) {
            if (isset($aPlan[$sOption . '_allowed']) && $aPlan[$sOption . '_allowed'] && key_exists($sOption, $aPaidOptions)) {
                $aPaidOptionsInfo[$sOption] = [
                    'caption' => _p($sOption),
                    'price' => $aPlan[$sOption],
                ];
                $iTotalPrice += $aPlan[$sOption];
            }
        }

        if ($this->request()->get('process'))
        {
            if (($iInvoice = Phpfox::getService('digitaldownload.invoice')->add($oDD['id'], $aPlan['price_currency_id'], $iTotalPrice, 'options', $aPaidOptionsInfo)))
            {
                $this->url()->send('digitaldownload.purchase', array('invoice' => $iInvoice));
            }
        }

        $this->template()->setTitle(_p('Review and Confirm Purchase'))
            ->setBreadCrumb(_p('Digital Download'), $this->url()->makeUrl('digitaldownload'))
            ->setBreadCrumb(_p('Edit digital download'), $this->url()->makeUrl('digitaldownload.add', ['dd_id' => $oDD['id']]))
            ->setBreadCrumb(_p('Review and Confirm Purchase'), null,  true)
            ->assign([
                    'aOptions' => $aPaidOptionsInfo,
                    'bInvoice' => $bInvoice,
                    'iTotalPrice' => $iTotalPrice,
                    'sPlanCurrencyId' => $aPlan['price_currency_id'],
                    'oDD' => $oDD,
                ]
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('digitaldownload.component_controller_apply_options_clean')) ? eval($sPlugin) : false);
    }
}