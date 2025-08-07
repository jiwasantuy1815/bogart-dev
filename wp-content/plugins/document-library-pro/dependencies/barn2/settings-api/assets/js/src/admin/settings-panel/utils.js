import { applyFilters } from '@wordpress/hooks';
import { JSON_DATA } from './config';
import Button from './components/fields/Button';
import TextInput from './components/fields/TextInput';
import CheckboxInput from './components/fields/CheckboxInput';
import RadioInput from './components/fields/RadioInput';
import TextareaInput from './components/fields/TextareaInput';
import NumberInput from './components/fields/NumberInput';
import SelectInput from './components/fields/SelectInput';
import MultiselectInput from './components/fields/MultiselectInput';
import CheckboxesInput from './components/fields/CheckboxesInput';
import ToggleInput from './components/fields/ToggleInput';
import LicenseInput from './components/fields/LicenseInput';
import HiddenInput from './components/fields/HiddenInput';
import { forEach } from 'lodash';
import checkConditions from 'json-conditions';
import apiFetch from '@wordpress/api-fetch';
import ColorInput from './components/fields/ColorInput';
import ColorSizeInput from './components/fields/ColorSizeInput';
import ImageLabel from './components/fields/ImageLabel';
import PostSelect from './components/fields/PostSelect';
import ColumnsEditor from './components/fields/ColumnsInput';

/**
 * Get the registered tabs with their sections and fields.
 *
 * @return {Array} The registered tabs.
 */
export function getRegisteredTabs() {
	return JSON_DATA.tabs; // eslint-disable-line camelcase, no-undef
}

/**
 * Get the layout of the settings panel.
 *
 * @return {string} The layout of the settings panel.
 */
export function getLayout() {
	return JSON_DATA.layout; // eslint-disable-line camelcase, no-undef
}

/**
 * Get the language variables.
 *
 * @return {Object} The language variables.
 */
export function getLanguageVariables() {
	return JSON_DATA.lang; // eslint-disable-line camelcase, no-undef
}

/**
 * Get the current tab.
 *
 * @param {string} currentPathName The current path name.
 * @return {Object} The current tab.
 */
export function getCurrentTab( currentPathName ) {
	const tabs = getRegisteredTabs();
	let currentTab = null;

	// Find the tab where the "id" property equals to the currentPathName but without the initial "/" character.
	tabs.forEach( ( tab ) => {
		if ( tab.id === currentPathName.substring( 1 ) ) {
			currentTab = tab;
		}
	} );

	// If the currentPathName equals to "/" get the very first tab.
	if ( currentPathName === '/' ) {
		currentTab = tabs[ 0 ];
	}

	return currentTab;
}

/**
 * Get the input component for the form.
 *
 * @param {string} inputType The type of the form input component.
 * @return {Function} The functional component.
 */
export function getFormInputComponent( inputType = null ) {
	let type = inputType;

	switch ( type ) {
		case 'button':
			type = Button;
			break;
		case 'text':
			type = TextInput;
			break;
		case 'checkbox':
			type = CheckboxInput;
			break;
		case 'radio':
			type = RadioInput;
			break;
		case 'textarea':
			type = TextareaInput;
			break;
		case 'number':
			type = NumberInput;
			break;
		case 'select':
			type = SelectInput;
			break;
		case 'multiselect':
			type = MultiselectInput;
			break;
		case 'checkboxes':
			type = CheckboxesInput;
			break;
		case 'toggle':
			type = ToggleInput;
			break;
		case 'license':
			type = LicenseInput;
			break;
		case 'hidden':
			type = HiddenInput;
			break;
		case 'color':
			type = ColorInput;
			break;
		case 'color_size':
			type = ColorSizeInput;
			break;
		case 'image_label':
			type = ImageLabel;
			break;
		case 'post_select':
			type = PostSelect;
			break;
		case 'columns_editor':
			type = ColumnsEditor;
			break;
	}

	/**
	 * Filters the form input component.
	 *
	 * @param {Function} type      The type of the form input component.
	 * @param {string}   inputType The input component requested.
	 * @return {Function} The functional component.
	 */
	return applyFilters( 'barn2_settings_panel.InputComponent', type, inputType );
}

/**
 * Parses the conditions rules object into an array.
 *
 * @param {Object} rules - The conditions rules object.
 * @return {Array} The parsed rules array.
 */
export function parseConditionRules( rules ) {
	const parsedRules = [];

	forEach( rules, ( rule, key ) => {
		parsedRules.push( {
			property: key,
			...rule,
		} );
	} );

	return parsedRules;
}

/**
 * Determines if a field should be displayed based on the conditions.
 *
 * @param {Object} values     - The values of the form.
 * @param {Object} conditions - The conditions of the field.
 * @return {boolean} Whether the field should be displayed.
 */
export function shouldFieldDisplay( values, conditions ) {
	if ( ! conditions ) {
		return true;
	}

	const { rules, satisfy } = conditions;

	const satisfyCheck = satisfy || 'ALL';
	const parsedRules = parseConditionRules( rules ) || {};

	return checkConditions(
		{
			rules: parsedRules,
			satisfy: satisfyCheck,
		},
		values
	);
}

/**
 * Get the callable response from the REST API.
 *
 * @param {Object} field - The field object.
 * @param {mixed}  value - The value to pass to the callable.
 * @return {Promise}
 */
export async function getCallableFromRestAPI( field, value ) {
	return await apiFetch( {
		path: JSON_DATA.apiURL + '/callable',
		method: 'POST',
		data: {
			field,
			value,
		},
	} );
}
