<?php
/**
 * LaraClassified - Classified Ads Web Application
 * Copyright (c) BedigitCom. All Rights Reserved
 *
 * Website: http://www.bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from Codecanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

namespace App\Models\HomeSection;

use App\Models\Setting\Traits\UploadTrait;
use App\Models\Language;

class GetSearchForm
{
	use UploadTrait;
	
	public static function getValues($value)
	{
		if (empty($value)) {
			
			$value['enable_form_area_customization'] = '1';
			$value['background_image'] = null;
			
		} else {
			
			if (!isset($value['enable_form_area_customization'])) {
				$value['enable_form_area_customization'] = '1';
			}
			if (!isset($value['background_image'])) {
				$value['background_image'] = null;
			}
			
		}
		
		return $value;
	}
	
	public static function setValues($value, $setting)
	{
		// Background Image
		if (isset($value['background_image'])) {
			$backgroundImage = [
				'attribute' => 'background_image',
				'path'      => 'app/logo',
				'default'   => null,
				'width'     => (int)config('larapen.core.picture.otherTypes.bgHeader.width', 2000),
				'height'    => (int)config('larapen.core.picture.otherTypes.bgHeader.height', 1000),
				'ratio'     => config('larapen.core.picture.otherTypes.bgHeader.ratio', '1'),
				'upsize'    => config('larapen.core.picture.otherTypes.bgHeader.upsize', '0'),
				'quality'   => 100,
				'filename'  => 'header-',
				'orientate' => true,
			];
			$value = self::upload($setting, $value, $backgroundImage);
		}
		
		return $value;
	}
	
	public static function getFields($diskName)
	{
		$fields = [
			[
				'name'  => 'enable_form_area_customization',
				'label' => 'Enable search form area customization',
				'type'  => 'checkbox',
				'hint'  => 'The options below require to enable the search form area customization',
			],
			[
				'name'  => 'separator_1',
				'type'  => 'custom_html',
				'value' => 'getSearchForm_html_background',
			],
			[
				'name'                => 'background_color',
				'label'               => 'Background Color',
				'type'                => 'color_picker',
				'colorpicker_options' => [
					'customClass' => 'custom-class',
				],
				'attributes'          => [
					'placeholder' => '#444',
				],
				'hint'                => 'Enter a RGB color code',
			],
			[
				'name'   => 'background_image',
				'label'  => 'Background Image',
				'type'   => 'image',
				'upload' => true,
				'disk'   => $diskName,
				'hint'   => 'getSearchForm_background_image_hint',
			],
			[
				'name'              => 'height',
				'label'             => 'Height',
				'type'              => 'number',
				'attributes'        => [
					'placeholder' => '450',
				],
				'hint'              => 'Enter a value greater than 50px',
				'wrapperAttributes' => [
					'class' => 'form-group col-md-6',
				],
			],
			[
				'name'              => 'parallax',
				'label'             => 'Enable Parallax Effect',
				'type'              => 'checkbox',
				'wrapperAttributes' => [
					'class' => 'form-group col-md-6',
					'style' => 'margin-top: 20px;',
				],
			],
			[
				'name'  => 'separator_2',
				'type'  => 'custom_html',
				'value' => 'getSearchForm_html_search_form',
			],
			[
				'name'  => 'hide_form',
				'label' => 'Hide the Form',
				'type'  => 'checkbox',
			],
			[
				'name'                => 'form_border_color',
				'label'               => 'Form Border Color',
				'type'                => 'color_picker',
				'colorpicker_options' => [
					'customClass' => 'custom-class',
				],
				'attributes'          => [
					'placeholder' => '#333',
				],
				'hint'                => 'Enter a RGB color code',
				'wrapperAttributes'   => [
					'class' => 'form-group col-md-3',
				],
			],
			[
				'name'              => 'form_border_width',
				'label'             => 'Form Border Width',
				'type'              => 'number',
				'attributes'        => [
					'placeholder' => '5',
				],
				'hint'              => 'Enter a number with unit',
				'wrapperAttributes' => [
					'class' => 'form-group col-md-3',
				],
			],
			[
				'name'                => 'form_btn_background_color',
				'label'               => 'Form Button Background Color',
				'type'                => 'color_picker',
				'colorpicker_options' => [
					'customClass' => 'custom-class',
				],
				'attributes'          => [
					'placeholder' => '#4682B4',
				],
				'hint'                => 'Enter a RGB color code',
				'wrapperAttributes'   => [
					'class' => 'form-group col-md-3',
				],
			],
			[
				'name'                => 'form_btn_text_color',
				'label'               => 'Form Button Text Color',
				'type'                => 'color_picker',
				'colorpicker_options' => [
					'customClass' => 'custom-class',
				],
				'attributes'          => [
					'placeholder' => '#FFF',
				],
				'hint'                => 'Enter a RGB color code',
				'wrapperAttributes'   => [
					'class' => 'form-group col-md-3',
				],
			],
			[
				'name'  => 'separator_3',
				'type'  => 'custom_html',
				'value' => 'getSearchForm_html_titles',
			],
			[
				'name'  => 'hide_titles',
				'label' => 'Hide Titles',
				'type'  => 'checkbox',
			],
			[
				'name'  => 'separator_3_1',
				'type'  => 'custom_html',
				'value' => 'getSearchForm_html_titles_content',
			],
			[
				'name'  => 'separator_3_2',
				'type'  => 'custom_html',
				'value' => 'getSearchForm_html_titles_content_hint',
			],
		];
		
		$languages = Language::active()->get();
		if ($languages->count() > 0) {
			$titlesFields = [];
			foreach ($languages as $language) {
				$titlesFields[] = [
					'name'              => 'title_' . $language->abbr,
					'label'             => mb_ucfirst(trans('admin.title')) . ' (' . $language->name . ')',
					'attributes'        => [
						'placeholder' => t('sell_and_buy_near_you', [], 'global', $language->abbr),
					],
					'wrapperAttributes' => [
						'class' => 'form-group col-md-6',
					],
					'disableTrans'      => 'true',
				];
				$titlesFields[] = [
					'name'              => 'sub_title_' . $language->abbr,
					'label'             => trans('admin.Sub Title') . ' (' . $language->name . ')',
					'attributes'        => [
						'placeholder' => t('simple_fast_and_efficient', [], 'global', $language->abbr),
					],
					'wrapperAttributes' => [
						'class' => 'form-group col-md-6',
					],
					'disableTrans'      => 'true',
				];
			}
			
			$fields = array_merge($fields, $titlesFields);
		}
		
		$fields = array_merge($fields, [
			[
				'name'  => 'separator_3_3',
				'type'  => 'custom_html',
				'value' => 'getSearchForm_html_titles_color',
			],
			[
				'name'                => 'big_title_color',
				'label'               => 'Big Title Color',
				'type'                => 'color_picker',
				'colorpicker_options' =>
					[
						'customClass' => 'custom-class',
					],
				'attributes'          =>
					[
						'placeholder' => '#FFF',
					],
				'hint'                => 'Enter a RGB color code',
				'wrapperAttributes'   =>
					[
						'class' => 'form-group col-md-6',
					],
			],
			[
				'name'                => 'sub_title_color',
				'label'               => 'Sub Title Color',
				'type'                => 'color_picker',
				'colorpicker_options' =>
					[
						'customClass' => 'custom-class',
					],
				'attributes'          =>
					[
						'placeholder' => '#FFF',
					],
				'hint'                => 'Enter a RGB color code',
				'wrapperAttributes'   =>
					[
						'class' => 'form-group col-md-6',
					],
			],
			[
				'name'  => 'separator_last',
				'type'  => 'custom_html',
				'value' => '<hr>',
			],
			[
				'name'  => 'hide_on_mobile',
				'label' => 'hide_on_mobile_label',
				'type'  => 'checkbox',
				'hint'  => 'hide_on_mobile_hint',
			],
			[
				'name'  => 'active',
				'label' => 'Active',
				'type'  => 'checkbox',
			],
		]);
		
		return $fields;
	}
}
