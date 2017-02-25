<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: view.html.php 5032 2012-11-19 13:58:57Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="item_view">
	<h1>{$oDD}</h1>
	<div class="info">
		<span>{$oDD.time_stamp|convert_time} </span>
		<span>&middot; </span>
		<span>{phrase var='event.by'} {$oDD|user:'':'':50}</span>
		<span>&middot;</span>
		<span>
			{phrase var='event.time_separator'}
		</span>
		<span>({$oDD.category})</span>
		<span class="pull-right">
			{$oDD.full_rating}
		</span>
	</div>

	<div class="item_info_more clearfix">
		{if $oDD.user_id != Phpfox::getUserId()}
			<div class="row">
				<div class="col-sm-6">
					<a href="{url link='digitaldownload.user'}{$oDD.user_id}" class="btn btn-link">
						<i class="fa fa-user"></i>
						<span>{_p('All ads by this user')}</span>
					</a>
				</div>
				<div class="col-sm-6">
					<div class="text-right">
						<a href="#" class="btn btn-info digitaldownload_contact_seller" onclick="$Core.composeMessage({l}user_id: {$oDD.user_id}, dd_id: {$oDD.id}{r}); return false;">
							<i class="fa fa-comment"></i>
							<span>{_p('Contact seller')}</span>
						</a>
					</div>
				</div>
			</div>
			<br>
		{/if}
	</div>

	{if ($oDD.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('digitaldownload.can_edit_other')
	|| ($oDD.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('digitaldownload.can_delete_other')
	}
	<div class="item_bar" style="margin-top: -4px">
		<div class="item_bar_action_holder">
			{if (Phpfox::getUserParam('digitaldownload.can_approve'))}
				<a href="#" class="item_bar_approve item_bar_approve_image" onclick="return false;" style="display:none;" id="js_item_bar_approve_image">{img theme='ajax/add.gif'}</a>
				<a href="#" class="item_bar_approve" onclick="$(this).hide(); $('#js_item_bar_approve_image').show(); $.ajaxCall('digitaldownload.approve', 'inline=true&amp;listing_id={$oDD.listing_id}'); return false;">{phrase var='digitaldownload.approve'}</a>
			{/if}
			<a role="button" data-toggle="dropdown" class="item_bar_action"><span>{_p('Actions')}</span></a>
			<ul class="dropdown-menu">
				{template file='digitaldownload.block.menu'}
			</ul>
		</div>
	</div>
	{/if}

	{if $oDD.images}

	<input type="hidden" name="digitaldetail_load_slider" value="1" id="digitaldetail_load_slider"/>

	<div class="ms-digitaldownload-detail-showcase dont-unbind">
		<div class="master-slider ms-skin-default dont-unbind" id="digitaldownload_slider-detail">
			{foreach from=$oDD.images name=images item=aImage}
			<div class="ms-slide dont-unbind">
				<img src="{$core_path}module/core/static/masterslider/blank.gif"
					 data-src="{img server_id=$aImage.server_id path='core.url_pic' file= 'digitaldownload/'.$aImage.image_path suffix='_400' return_url=true}"/>
				<img class="ms-thumb dont-unbind" src="{img server_id=$aImage.server_id path='core.url_pic' file='digitaldownload/'.$aImage.image_path suffix='_120_square' return_url=true}" alt="thumb" />
			</div>
			{/foreach}
		</div>
	</div>
	{/if}

	{module name='digitaldownload.info'}

	{plugin call='digitaldownload.template_default_controller_view_extra_info'}

	<div class="js_moderation_on">
		{module name='feed.comment'}
	</div>
</div>

{literal}
<script type="text/javascript">
	(function(){
		var
				_debug = true,
				_stageSlider = '#digitaldetail_load_slider',
				_required = function(){
					return !/undefined/i.test(typeof MasterSlider)
				},

				_initDetailSlide_flag = false,
				initDetailSlide = function (){
					var stageSlider =  $(_stageSlider);
					if(!stageSlider.length) return;
					if(_initDetailSlide_flag) return;
					if(!_required()) return;

					if($('#digitaldetail_load_slider').val() == 1)
					{
						var slider = new MasterSlider();

						slider.control('arrows');
						slider.control('thumblist' , {
							autohide:false ,
							dir:'h',
							arrows:false,
							align:'bottom',
							width:60,
							height:60,
							margin:5,
							space:5
						});
						slider.setup('digitaldownload_slider-detail' , {
							width: $('#digitaldownload_slider-detail').width(),
							height:386,
							space:5,
							view:'fadeWave',
							fillMode: 'center',
						});
						$('#digitaldetail_load_slider').val(0);
					}

					_initDetailSlide_flag = true;
				}

		$Behavior.initDetailSlide = function() {
			function checkCondition(){
				var stageSlider =  $(_stageSlider);
				if(!stageSlider.length) return;
				if(_initDetailSlide_flag) return;
				if(!_required()){
					window.setTimeout(checkCondition, 1700);
				}
				else
				{
					initDetailSlide();
				}
			}
			window.setTimeout(checkCondition, 1700);
		}

	})();

</script>
{/literal}