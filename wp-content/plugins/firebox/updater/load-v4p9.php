<?php
require dirname(__FILE__) . '/Puc/v4p9/Autoloader.php';
new Puc_v4p9_Autoloader();

require dirname(__FILE__) . '/Puc/v4p9/Factory.php';
require dirname(__FILE__) . '/Puc/v4/Factory.php';

//Register classes defined in this version with the factory.
foreach (
	array(
		'Plugin_UpdateChecker' => 'Puc_v4p9_Plugin_UpdateChecker',
		'Vcs_PluginUpdateChecker' => 'Puc_v4p9_Vcs_PluginUpdateChecker'
	)
	as $pucGeneralClass => $pucVersionedClass
) {
	Puc_v4_Factory::addVersion($pucGeneralClass, $pucVersionedClass, '4.9');
	//Also add it to the minor-version factory in case the major-version factory
	//was already defined by another, older version of the update checker.
	Puc_v4p9_Factory::addVersion($pucGeneralClass, $pucVersionedClass, '4.9');
}

