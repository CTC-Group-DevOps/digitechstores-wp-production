<?php

namespace WPDesk\FCF\Pro\Field;

use WPDesk\FCF\Free\Field\Type\TypeIntegration;
use WPDesk\FCF\Pro\Field\Type\CheckboxDefaultType;
use WPDesk\FCF\Pro\Field\Type\CheckboxType;
use WPDesk\FCF\Pro\Field\Type\ColorType;
use WPDesk\FCF\Pro\Field\Type\DateType;
use WPDesk\FCF\Pro\Field\Type\DefaultType;
use WPDesk\FCF\Pro\Field\Type\EmailType;
use WPDesk\FCF\Pro\Field\Type\FileType;
use WPDesk\FCF\Pro\Field\Type\HeadingType;
use WPDesk\FCF\Pro\Field\Type\HiddenType;
use WPDesk\FCF\Pro\Field\Type\HtmlType;
use WPDesk\FCF\Pro\Field\Type\ImageType;
use WPDesk\FCF\Pro\Field\Type\MultiCheckboxType;
use WPDesk\FCF\Pro\Field\Type\MultiSelectType;
use WPDesk\FCF\Pro\Field\Type\NumberType;
use WPDesk\FCF\Pro\Field\Type\ParagraphType;
use WPDesk\FCF\Pro\Field\Type\PhoneType;
use WPDesk\FCF\Pro\Field\Type\RadioColorsType;
use WPDesk\FCF\Pro\Field\Type\RadioDefaultType;
use WPDesk\FCF\Pro\Field\Type\RadioImagesType;
use WPDesk\FCF\Pro\Field\Type\RadioType;
use WPDesk\FCF\Pro\Field\Type\SelectType;
use WPDesk\FCF\Pro\Field\Type\TextareaType;
use WPDesk\FCF\Pro\Field\Type\TextType;
use WPDesk\FCF\Pro\Field\Type\TimeType;
use WPDesk\FCF\Pro\Field\Type\UrlType;
use WPDesk\FCF\Pro\Field\Type\Wc\WcAddress2Type;
use WPDesk\FCF\Pro\Field\Type\Wc\WcContactType;
use WPDesk\FCF\Pro\Field\Type\Wc\WcCountryType;
use WPDesk\FCF\Pro\Field\Type\Wc\WcDefaultType;
use WPDesk\FCF\Pro\Field\Type\Wc\WcNotesType;
use WPDesk\FCF\Pro\Field\Type\Wc\WcPostcodeType;
use WPDesk\FCF\Pro\Field\Type\Wc\WcStateType;

/**
 * Supports management of field types.
 */
class Types {

	/**
	 * Initializes actions for class.
	 *
	 * @return void
	 */
	public function init() {
		( new TypeIntegration( new TextType() ) )->hooks();
		( new TypeIntegration( new TextareaType() ) )->hooks();
		( new TypeIntegration( new NumberType() ) )->hooks();
		( new TypeIntegration( new EmailType() ) )->hooks();
		( new TypeIntegration( new PhoneType() ) )->hooks();
		( new TypeIntegration( new UrlType() ) )->hooks();

		( new TypeIntegration( new CheckboxType() ) )->hooks();
		( new TypeIntegration( new MultiCheckboxType() ) )->hooks();
		( new TypeIntegration( new SelectType() ) )->hooks();
		( new TypeIntegration( new MultiSelectType() ) )->hooks();
		( new TypeIntegration( new RadioType() ) )->hooks();
		( new TypeIntegration( new RadioImagesType() ) )->hooks();
		( new TypeIntegration( new RadioColorsType() ) )->hooks();

		( new TypeIntegration( new ColorType() ) )->hooks();
		( new TypeIntegration( new DateType() ) )->hooks();
		( new TypeIntegration( new TimeType() ) )->hooks();
		( new TypeIntegration( new FileType() ) )->hooks();

		( new TypeIntegration( new HeadingType() ) )->hooks();
		( new TypeIntegration( new ParagraphType() ) )->hooks();
		( new TypeIntegration( new ImageType() ) )->hooks();
		( new TypeIntegration( new HtmlType() ) )->hooks();
		if ( class_exists( 'WPDesk\FCF\Free\Field\Type\HiddenType' ) ) {
			( new TypeIntegration( new HiddenType() ) )->hooks();
		}

		( new TypeIntegration( new DefaultType() ) )->hooks();
		( new TypeIntegration( new CheckboxDefaultType() ) )->hooks();
		( new TypeIntegration( new RadioDefaultType() ) )->hooks();
		( new TypeIntegration( new WcDefaultType() ) )->hooks();
		( new TypeIntegration( new WcContactType() ) )->hooks();
		( new TypeIntegration( new WcAddress2Type() ) )->hooks();
		( new TypeIntegration( new WcCountryType() ) )->hooks();
		( new TypeIntegration( new WcPostcodeType() ) )->hooks();
		( new TypeIntegration( new WcStateType() ) )->hooks();
		( new TypeIntegration( new WcNotesType() ) )->hooks();
	}
}
