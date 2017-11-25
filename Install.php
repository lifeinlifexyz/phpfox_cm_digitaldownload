<?php
            namespace Apps\CM_DigitalDownload;

            use Core\App;

            /**
             * Class Install
             * @author  Neil
             * @version 4.5.0
             * @package Apps\CM_DigitalDownload
             */
            class Install extends App\App
            {
                private $_app_phrases = [
        
                ];
                protected function setId()
                {
                    $this->id = 'CM_DigitalDownload';
                }
                protected function setAlias() 
                {
            
                $this->alias = 'digitaldownload';
             }
            protected function setName()
            {
                $this->name = 'Digital Download';
            } protected function setVersion() {
                $this->version = '1.0.2';
            } protected function setSupportVersion() {
            $this->start_support_version = '4.4.0';
            $this->end_support_version = '4.4.0';
        } protected function setSettings() {
                $this->settings = ['cm_dd_enabled' => ['info' => 'Digital Download App Enabled','type' => 'input:radio','value' => '1','js_variable' => '1',],'cm_dd_feature_limit' => ['info' => 'Featured in block','type' => 'input:text','value' => '4','js_variable' => '1',],'cm_dd_most_viewed_limit' => ['info' => 'Most viewed in block','type' => 'input:text','value' => '4','js_variable' => '1',],'cm_dd_most_talked_limit' => ['info' => 'Most talked in block','type' => 'input:text','value' => '4','js_variable' => '1',],'cm_dd_most_liked_limit' => ['info' => 'Most liked in block','type' => 'input:text','value' => '4','js_variable' => '1',],'cm_dd_most_downloaded_limit' => ['info' => 'Most downloaded in block','type' => 'input:text','value' => '4','js_variable' => '1',],'cm_dd_similar_limit' => ['info' => 'Similar products in block','type' => 'input:text','value' => '10','js_variable' => '1',],'cm_dd_last_viewed_limit' => ['info' => 'User recently viewed in block','type' => 'input:text','value' => '10','js_variable' => '1',],'cm_dd_highlighted_color' => ['info' => 'Highlighted color','type' => 'input:text','value' => '#FFF0D1','js_variable' => '1',],];
            } protected function setUserGroupSettings() {
                $this->user_group_settings = ['digitaldownload.cm_dd_add' => ['info' => 'Can add digital download?','type' => 'input:radio','value' => ['1' => '1','2' => '1','3' => '0','4' => '1','5' => '0',],'options' => ['yes' => 'Yes','no' => 'No',],],'digitaldownload.can_view_dd' => ['info' => 'Can view digital download?','type' => 'input:radio','value' => ['1' => '1','2' => '1','3' => '0','4' => '1','5' => '0',],'options' => ['yes' => 'Yes','no' => 'No',],],'digitaldownload.can_post_comment_on_dd' => ['info' => 'Can post comment?','type' => 'input:radio','value' => ['1' => '1','2' => '1','3' => '0','4' => '1','5' => '0',],'options' => ['yes' => 'Yes','no' => 'No',],],'digitaldownload.can_edit_other' => ['info' => 'Can edit other?','type' => 'input:radio','value' => ['1' => '1','2' => '0','3' => '0','4' => '1','5' => '0',],'options' => ['yes' => 'Yes','no' => 'No',],],'digitaldownload.can_activate_deactivate_other' => ['info' => 'Can activate/deactivate other?','type' => 'input:radio','value' => ['1' => '1','2' => '0','3' => '0','4' => '1','5' => '0',],'options' => ['yes' => 'Yes','no' => 'No',],],'digitaldownload.can_rate' => ['info' => 'Can set rate?','type' => 'input:radio','value' => ['1' => '1','2' => '1','3' => '0','4' => '1','5' => '0',],'options' => ['yes' => 'Yes','no' => 'No',],],'digitaldownload.can_moderate' => ['info' => 'Can moderate?','type' => 'input:radio','value' => ['1' => '1','2' => '0','3' => '0','4' => '1','5' => '0',],'options' => ['yes' => 'Yes','no' => 'No',],],'digitaldownload.can_view_expired' => ['info' => 'Can view expired?','type' => 'input:radio','value' => ['1' => '1','2' => '0','3' => '0','4' => '1','5' => '0',],'options' => ['yes' => 'Yes','no' => 'No',],],'digitaldownload.can_delete_other' => ['info' => 'Can delete other?','type' => 'input:radio','value' => ['1' => '1','2' => '0','3' => '0','4' => '1','5' => '0',],'options' => ['yes' => 'Yes','no' => 'No',],],];
            } protected function setComponent() {
                $this->component = ['block' => ['filter' => '','most-viewed' => '','most-talked' => '','most-liked' => '','most-downloaded' => '','similar' => '','user-recently-viewed' => '','featured' => '',],'controller' => ['index' => 'digitaldownload.index','add' => 'digitaldownload.add','category' => 'digitaldownload.category','plan' => 'digitaldownload.plan','view' => 'digitaldownload.view',],];
            } protected function setComponentBlock() {
                $this->component_block = ['Digital download filter' => ['type_id' => '0','m_connection' => 'digitaldownload.index','component' => 'filter','location' => '1','is_active' => '1','ordering' => '3',],'Most Viewed' => ['type_id' => '0','m_connection' => 'digitaldownload.index','component' => 'most-viewed','location' => '1','is_active' => '1','ordering' => '4',],'Most Talked' => ['type_id' => '0','m_connection' => 'digitaldownload.index','component' => 'most-talked','location' => '1','is_active' => '1','ordering' => '5',],'Feautured' => ['type_id' => '0','m_connection' => 'digitaldownload.index','component' => 'featured','location' => '3','is_active' => '1','ordering' => '3',],'Feautured in view' => ['type_id' => '0','m_connection' => 'digitaldownload.view','component' => 'featured','location' => '1','is_active' => '1','ordering' => '3',],'Similar products' => ['type_id' => '0','m_connection' => 'digitaldownload.view','component' => 'similar','location' => '4','is_active' => '1','ordering' => '1',],'User recently viewed' => ['type_id' => '0','m_connection' => 'digitaldownload.view','component' => 'user-recently-viewed','location' => '4','is_active' => '1','ordering' => '0',],'Most downloaded' => ['type_id' => '0','m_connection' => 'digitaldownload.index','component' => 'most-downloaded','location' => '3','is_active' => '1','ordering' => '5',],'Featured in choose category' => ['type_id' => '0','m_connection' => 'digitaldownload.category','component' => 'featured','location' => '3','is_active' => '1','ordering' => '1',],'Featured in choose plan' => ['type_id' => '0','m_connection' => 'digitaldownload.plan','component' => 'featured','location' => '3','is_active' => '1','ordering' => '1',],'Featured on add' => ['type_id' => '0','m_connection' => 'digitaldownload.add','component' => 'featured','location' => '3','is_active' => '1','ordering' => '1',],'Most liked' => ['type_id' => '0','m_connection' => 'digitaldownload.index','component' => 'most-liked','location' => '3','is_active' => '1','ordering' => '4',],];
            } protected function setPhrase() {
            $this->phrase = $this->_app_phrases;
        } protected function setOthers() {
                $this->admincp_route = '/admincp/digitaldownload/categories';
        	$this->_publisher = 'Codemake';
	        $this->_publisher_url = 'https://store.phpfox.com/techie/u/ecodemaster';
                $this->admincp_menu = ['Manage Categories' => 'digitaldownload.categories','Manage Fields' => 'digitaldownload.fields','+ Add Field' => 'digitaldownload.fields.add','Manage Plans' => 'digitaldownload.plans','+ Add Plan' => 'digitaldownload.plan.add',];
            
                $this->admincp_action_menu = ['/digitaldownload/admincp/add-category' => 'New Category',];
            
                $this->menu = ['name' => 'Digital Download','url' => '/digitaldownload','icon' => 'download',];
            
                $this->icon = 'https://raw.githubusercontent.com/codemakeorg/logo/master/dd.png';
            }
                public $vendor = '<a href="//codemake.org" target="_blank">CodeMake.Org</a> - See all our products <a href="//store.phpfox.com/techie/u/ecodemaster" target=_new>HERE</a> - contact us at: support@codemake.org';
                public $store_id = '1773';}
