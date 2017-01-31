<?php

namespace Apps\CM_DigitalDownload\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Module;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class PurchaseController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {

        Phpfox::isUser(true);
        $bInvoice = ($this->request()->get('invoice') ? true : false);
        $iId = $this->request()->get('id');

        if ($bInvoice)
        {
            if (($aInvoice = Phpfox::getService('digitaldownload.invoice')->get($this->request()->get('invoice'))))
            {
                if ($aInvoice['user_id'] != Phpfox::getUserId())
                {
                    return Phpfox_Error::display(_p('Unable to purchase this item'));
                }
                $iId = $aInvoice['dd_id'];

                $aUserGateways =  [];
                if ($aInvoice['type'] == 'options') { //if buy options, then pay for admin, else for owner
                    $aAdminGateways = Phpfox::getService('api.gateway')->getForAdmin();

                    foreach ($aAdminGateways as &$aAdminGateway) {
                        $aUserGateways[$aAdminGateway['gateway_id']]['gateway'] = unserialize($aAdminGateway['setting']);
                    }

                } else {
                    $aUserGateways = Phpfox::getService('api.gateway')->getUserGateways($aInvoice['dd_user_id']);
                }

                $aActiveGateways = Phpfox::getService('api.gateway')->getActive();
                $aPurchaseDetails = $this->getPurchaseDetails($aInvoice);

                if (is_array($aUserGateways) && count($aUserGateways))
                {
                    foreach ($aUserGateways as $sGateway => $aData)
                    {
                        if (is_array($aData['gateway']))
                        {
                            foreach ($aData['gateway'] as $sKey => $mValue)
                            {
                                $aPurchaseDetails['setting'][$sKey] = $mValue;
                            }
                        }
                        else
                        {
                            $aPurchaseDetails['fail_' . $sGateway] = true;
                        }

                        // Payment gateways added after user configured their payment gateway settings
                        if(empty($aActiveGateways))
                        {
                            continue;
                        }
                        $bActive = false;
                        foreach ($aActiveGateways as $aActiveGateway)
                        {
                            if($sGateway == $aActiveGateway['gateway_id'])
                            {
                                $bActive = true;
                            }
                        }
                        if(!$bActive)
                        {
                            $aPurchaseDetails['fail_' . $aActiveGateway['gateway_id']] = true;
                        }
                    }
                }
                $this->setParam('gateway_data', $aPurchaseDetails);
            }
        }

        if ($iId && ($sDDName = $this->request()->get('dd_name'))) {

            if (!($oDD = Phpfox::getService('digitaldownload.dd')->getDisplayer($iId))) {
                return Phpfox_Error::display(_p('Unable to find the item you are looking for'));
            }
            $aDDField = $oDD->getFields();
            $aDDField = $aDDField[$sDDName];

            if ($this->request()->get('process'))
            {
                if (($iInvoice = Phpfox::getService('digitaldownload.invoice')
                    ->add($oDD['id'], $oDD[$sDDName . '_currency_id'], $oDD[$sDDName . '_price'], 'dd',
                        [
                            'field' => $sDDName,
                            'limit' => $oDD[$sDDName . '_limit']
                        ])))
                {
                    $this->url()->send('digitaldownload.purchase', array('invoice' => $iInvoice));
                }
            }


            $this->template()->assign([
               'oDD' => $oDD,
               'sDDName' => $sDDName,
               'aItemField' => $aDDField,
               'sPrice' => $aDDField->getPrice(),
            ]);

        }

        $this->template()->setTitle(_p('Review and Confirm Purchase'))
            ->setBreadCrumb(_p('Digital Download'), $this->url()->makeUrl('digitaldownload'))
            ->setBreadCrumb(_p('Review and Confirm Purchase'), null, true)
            ->assign([
                    'bInvoice' => $bInvoice
                ]
            );
        return null;
    }

    protected function getPurchaseDetails($aInvoice)
    {
        return [
            'item_number' => 'digitaldownload|' . $aInvoice['invoice_id'],
            'currency_code' => $aInvoice['currency_id'],
            'amount' => $aInvoice['price'],
            'return' => $this->url()->makeUrl('digitaldownload.invoice', ['payment' => 'done']),
            'item_name' => Phpfox::getBaseUrl() . ': '
                . _p('For Digital Download {{ id }}', ['id' => $aInvoice['dd_id']]),
            'recurring' => '',
            'recurring_cost' => '',
            'alternative_cost' => '',
            'alternative_recurring_cost' => '',
        ];
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