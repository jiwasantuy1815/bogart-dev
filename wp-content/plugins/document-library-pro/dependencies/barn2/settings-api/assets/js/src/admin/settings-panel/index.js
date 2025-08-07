/**
 * WordPress dependencies
 */
import { render, createRoot } from '@wordpress/element';

/**
 * External dependencies
 */
import { HashRouter } from 'react-router-dom';

/**
 * Internal dependencies.
 */
import SettingsPanel from './SettingsPanel';

const domElement = document.getElementById( 'barn2-settings-panel' );
const uiElement = (
	<HashRouter>
		<SettingsPanel />
	</HashRouter>
);

if ( domElement ) {
	if ( createRoot ) {
		createRoot( document.getElementById( 'barn2-settings-panel' ) ).render( uiElement );
	} else {
		render( uiElement, domElement );
	}
}
