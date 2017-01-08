<?php
namespace Apps\CM_DigitalDownload\Block;


class ManageOptions extends \Phpfox_Component
{
    public function process()
    {
        $aPlan = $this->getParam('aPlan');
        $oDD = $this->getParam('oDD');
        $sPlanCurrencyId = $aPlan['price_currency_id'];


        unset($aPlan['allowed_count_pictures']);
        unset($aPlan['life_time']);
        unset($aPlan['name']);
        unset($aPlan['price_currency_id']);
        unset($aPlan['plan_id']);
        unset($aPlan['price']);

        $aExtraOption = \Phpfox::getService('digitaldownload.plan')->getExtraOptions();

        $aAlreadyActivatedOptions = [];
        $aFreeOptions = [];
        $aPaidOptions = [];

        foreach($aExtraOption as $sOption) {
            if ($aPlan[$sOption . '_allowed']) {
                if ($oDD[$sOption]) {
                    $aAlreadyActivatedOptions[$sOption] = _p($sOption . ' option');
                }elseif ($aPlan[$sOption] > 0) {
                    $aPaidOptions[$sOption]  = [
                        'caption' => _p($sOption),
                        'price' => $aPlan[$sOption],
                    ];
                } else {
                    $aFreeOptions[$sOption] = _p($sOption . ' option');
                }
            }
        }

        $this->template()->assign([
            'sPlanCurrencyId' => $sPlanCurrencyId,
            'aActivatedOptions' => $aAlreadyActivatedOptions,
            'aFreeOptions' => $aFreeOptions,
            'aPaidOptions' => $aPaidOptions,
        ]);

        return 'block';
    }
}