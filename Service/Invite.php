<?php
namespace Apps\CM_DigitalDownload\Service;


use Phpfox;
use Phpfox_Plugin;
use Phpfox_Url;

class Invite  extends \Phpfox_Service
{
    protected $_sTable = 'digital_invite';

    /**
     * @param $iId
     * @param $aVals
     * @param $oDD
     */
    public function send($iId, $aVals, $oDD)
    {
        $oParseInput = Phpfox::getLib('parse.input');
        
        if (isset($aVals['emails']) || isset($aVals['invite']))
        {
            $aInvites = $this->database()->select('invited_user_id, invited_email')
                ->from(Phpfox::getT('digitaldownload_invite'))
                ->where('dd_id = ' . (int) $iId)
                ->execute('getRows');
            $aInvited = [];
            foreach ($aInvites as $aInvite)
            {
                $aInvited[(empty($aInvite['invited_email']) ? 'user' : 'email')][(empty($aInvite['invited_email']) ? $aInvite['invited_user_id'] : $aInvite['invited_email'])] = true;
            }
        }

        if (isset($aVals['emails']))
        {
            $aEmails = explode(',', $aVals['emails']);
            $aCachedEmails = [];

            foreach ($aEmails as $sEmail)
            {
                $sEmail = trim($sEmail);
                if (!Phpfox::getLib('mail')->checkEmail($sEmail))
                {
                    continue;
                }

                if (isset($aInvited['email'][$sEmail]))
                {
                    continue;
                }

                $sLink = $oDD['url'];
                $sMessage = Phpfox::getPhrase('digitaldownload.full_name_invited_you_to_view_the_digitaldownload_item_title', [
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'title' => $oParseInput->clean(((string)$oDD), 255),
                        'link' => $sLink
                    ]
                );
                if (!empty($aVals['personal_message']))
                {
                    $sMessage .= "\n\n" . Phpfox::getPhrase('digitaldownload.full_name_added_the_following_personal_message', ['full_name' => Phpfox::getUserBy('full_name')]) . ":\n";
                    $sMessage .= $aVals['personal_message'];
                }

                $oMail = Phpfox::getLib('mail');
                if (isset($aVals['invite_from']) && $aVals['invite_from'] == 1)
                {
                    $oMail->fromEmail(Phpfox::getUserBy('email'))
                        ->fromName(Phpfox::getUserBy('full_name'));
                }
                $bSent = $oMail->to($sEmail)
                    ->subject(array('digitaldownload.full_name_invited_you_to_view_the_listing_title', ['full_name' => Phpfox::getUserBy('full_name'), 'title' => $oParseInput->clean(((string)$oDD), 255)]))
                    ->message($sMessage)
                    ->send();

                if ($bSent)
                {
                    $this->_aInvited[] = ['email' => $sEmail];

                    $aCachedEmails[$sEmail] = true;

                    $this->database()->insert(Phpfox::getT($this->_sTable), [
                            'listing_id' => $iId,
                            'type_id' => 1,
                            'user_id' => Phpfox::getUserId(),
                            'invited_email' => $sEmail,
                            'time_stamp' => PHPFOX_TIME
                        ]
                    );
                }
            }
        }

        if (isset($aVals['invite']) && is_array($aVals['invite']))
        {
            $sUserIds = '';
            foreach ($aVals['invite'] as $iUserId)
            {
                if (!is_numeric($iUserId))
                {
                    continue;
                }
                $sUserIds .= $iUserId . ',';
            }
            $sUserIds = rtrim($sUserIds, ',');

            $aUsers = $this->database()->select('user_id, email, language_id, full_name')
                ->from(Phpfox::getT('user'))
                ->where('user_id IN(' . $sUserIds . ')')
                ->execute('getSlaveRows');

            foreach ($aUsers as $aUser)
            {
                if (isset($aCachedEmails[$aUser['email']]))
                {
                    continue;
                }

                if (isset($aInvited['user'][$aUser['user_id']]))
                {
                    continue;
                }

                $sLink = $oDD['url'];
                $sMessage = Phpfox::getPhrase('digitaldownload.full_name_invited_you_to_view_the_digitaldownload_listing_title', [
                    'full_name' => Phpfox::getUserBy('full_name'),
                    'title' => $oParseInput->clean(((string)$oDD), 255),
                    'link' => $sLink
                ], false, null, $aUser['language_id']
                );
                if (!empty($aVals['personal_message']))
                {
                    $sMessage .= "\n\n" . Phpfox::getPhrase('digitaldownload.full_name_added_the_following_personal_message', ['full_name' => Phpfox::getUserBy('full_name')], false, null, $aUser['language_id']);
                    $sMessage .= $aVals['personal_message'];
                }

                $bSent = Phpfox::getLib('mail')->to($aUser['user_id'])
                    ->subject(['digitaldownload.full_name_invited_you_to_view_the_listing_title', ['full_name' => Phpfox::getUserBy('full_name'), 'title' => $oParseInput->clean(((string)$oDD), 255)]])
                    ->message($sMessage)
                    ->notification('digitaldownload.new_invite')
                    ->send();

                if ($bSent)
                {
                    $this->_aInvited[] = ['user' => $aUser['full_name']];

                    $this->database()->insert(Phpfox::getT($this->_sTable), [
                            'listing_id' => $iId,
                            'user_id' => Phpfox::getUserId(),
                            'invited_user_id' => $aUser['user_id'],
                            'time_stamp' => PHPFOX_TIME
                        ]
                    );

                    (Phpfox::isModule('request') ? Phpfox::getService('request.process')->add('digitaldownload_invite', $iId, $aUser['user_id']) : null);
                }
            }
        }
    }
}