<?php
/**
 * Form fields loader.
 *
 * @since     9.0
 * @var Vc_Manager $this
 */

// phpcs:disable Squiz.Commenting.FileComment
require_once $this->path( 'FORM_FIELDS_DIR', 'abstract-class-wpb-form-field.php' ); // @phpstan-ignore variable.undefined
require_once $this->path( 'FORM_FIELDS_DIR', 'class-wpb-form-field-checkbox.php' ); // @phpstan-ignore variable.undefined
// @phpstan-ignore variable.undefined
require_once $this->path( 'FORM_FIELDS_DIR', 'class-wpb-form-field-toggle.php' );
// @phpstan-ignore variable.undefined
require_once $this->path( 'FORM_FIELDS_DIR', 'class-wpb-form-field-textfield.php' );
// @phpstan-ignore variable.undefined
require_once $this->path( 'FORM_FIELDS_DIR', 'class-wpb-form-field-radio.php' );
// @phpstan-ignore variable.undefined
require_once $this->path( 'FORM_FIELDS_DIR', 'class-wpb-form-field-button-group.php' );
// @phpstan-ignore variable.undefined
require_once $this->path( 'FORM_FIELDS_DIR', 'class-wpb-form-field-linked-fields.php' );
// @phpstan-ignore variable.undefined
require_once $this->path( 'FORM_FIELDS_DIR', 'class-wpb-form-field-textarea.php' );
// @phpstan-ignore variable.undefined
require_once $this->path( 'FORM_FIELDS_DIR', 'class-wpb-form-field-textarea-html.php' );
// @phpstan-ignore variable.undefined
require_once $this->path( 'FORM_FIELDS_DIR', 'class-wpb-form-field-range.php' );
// @phpstan-ignore variable.undefined
require_once $this->path( 'FORM_FIELDS_DIR', 'class-wpb-form-field-number.php' );
// @phpstan-ignore variable.undefined
require_once $this->path( 'FORM_FIELDS_DIR', 'class-wpb-form-field-dropdown.php' );
// @phpstan-ignore variable.undefined
require_once $this->path( 'FORM_FIELDS_DIR', 'class-wpb-form-field-autocomplete.php' );
// @phpstan-ignore variable.undefined
require_once $this->path( 'FORM_FIELDS_DIR', 'class-wpb-form-field-colorpicker.php' );
// @phpstan-ignore variable.undefined
require_once $this->path( 'FORM_FIELDS_DIR', 'class-wpb-form-field-iconpicker.php' );
// @phpstan-ignore variable.undefined
require_once $this->path( 'FORM_FIELDS_DIR', 'class-wpb-form-field-attach-image.php' );
// @phpstan-ignore variable.undefined
require_once $this->path( 'FORM_FIELDS_DIR', 'class-wpb-form-field-attach-images.php' );
// @phpstan-ignore variable.undefined
require_once $this->path( 'FORM_FIELDS_DIR', 'class-wpb-form-field-textarea-ace.php' );
// @phpstan-ignore variable.undefined
require_once $this->path( 'FORM_FIELDS_DIR', 'class-wpb-form-field-link.php' );
// @phpstan-ignore variable.undefined
require_once $this->path( 'FORM_FIELDS_DIR', 'class-wpb-form-field-hidden.php' );
