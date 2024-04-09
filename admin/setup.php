<?php
/* Copyright (C) 2004-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2024 Alfredo Altamirano <alfredo.altamirano@tisinproblemas.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    mxsatcatalogs/admin/setup.php
 * \ingroup mxsatcatalogs
 * \brief   MxSatCatalogs setup page.
 */

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"] . "/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME'];
$tmp2 = realpath(__FILE__);
$i = strlen($tmp) - 1;
$j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--;
	$j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1)) . "/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1)) . "/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1))) . "/main.inc.php")) {
	$res = @include dirname(substr($tmp, 0, ($i + 1))) . "/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

global $langs, $user;

// Libraries
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
require_once '../lib/mxsatcatalogs.lib.php';
//require_once "../class/myclass.class.php";

// Translations
$langs->loadLangs(array("admin", "mxsatcatalogs@mxsatcatalogs"));

// Initialize technical object to manage hooks of page. Note that conf->hooks_modules contains array of hook context
$hookmanager->initHooks(array('mxsatcatalogssetup', 'globalsetup'));

// Access control
if (!$user->admin) {
	accessforbidden();
}

// Parameters
$action = GETPOST('action', 'aZ09');
$backtopage = GETPOST('backtopage', 'alpha');
$modulepart = GETPOST('modulepart', 'aZ09');	// Used by actions_setmoduleoptions.inc.php

$value = GETPOST('value', 'alpha');
$label = GETPOST('label', 'alpha');
$scandir = GETPOST('scan_dir', 'alpha');
$type = 'myobject';
$table = GETPOST('table', 'aZ09');

$error = 0;

// Set this to 1 to use the factory to manage constants. Warning, the generated module will be compatible with version v15+ only
$useFormSetup = 1;

if (!class_exists('FormSetup')) {
	require_once DOL_DOCUMENT_ROOT . '/core/class/html.formsetup.class.php';
}
$formSetup = new FormSetup($db);

$help_url = '';
$page_name = "MxSatCatalogsSetup";

llxHeader('', $langs->trans($page_name), $help_url, '', 0, 0, '', '', '', 'mod-mxsatcatalogs page-admin');

/*
 * Actions
 */

dol_include_once('/mxsatcatalogs/core/modules/modMxSatCatalogs.class.php');
$module = new modMxSatCatalogs($db);

include DOL_DOCUMENT_ROOT . '/core/actions_setmoduleoptions.inc.php';

if ($action == 'updateTable') {
	dol_include_once('/mxsatcatalogs/lib/catalogs.lib.php');
	$currentDate = date('Ymd');
	$lastDownloadDate = dolibarr_get_const($db, 'MXSATCATALOGS_DB_DATE', $conf->entity);
	$sqliteDbPath = $dolibarr_main_data_root . '/mxsatcatalogs/catalogs.db';
	if ($currentDate != $lastDownloadDate || !file_exists($sqliteDbPath)) {
		download_catalog_db($sqliteDbPath);
		dolibarr_set_const($db, 'MXSATCATALOGS_DB_DATE', $currentDate, 'chaine', 0, '', $conf->entity);
	}
	update_table($module->dictionaries['tabname'][$table], $db, $sqliteDbPath);
	$updateMessage = $langs->trans($module->dictionaries['tablib'][$table]) . ' ' . $langs->trans('Updated');
	dol_htmloutput_mesg($updateMessage);
}


/*
 * View
 */

$token = newToken();

// Subheader
$linkback = '<a href="' . ($backtopage ? $backtopage : DOL_URL_ROOT . '/admin/modules.php?restore_lastsearch_values=1') . '">' . $langs->trans("BackToModuleList") . '</a>';

print load_fiche_titre($langs->trans($page_name), $linkback, 'title_setup');

// Configuration header
$head = mxsatcatalogsAdminPrepareHead();
print dol_get_fiche_head($head, 'settings', $langs->trans($page_name), -1, "mxsatcatalogs@mxsatcatalogs");

// Setup page goes here
echo '<span class="opacitymedium">' . $langs->trans("MxSatCatalogsSetupPage") . '</span><br><br>';

echo '<table class="noborder centpercent"><thead>';
echo '<tr class="liste_titre"><td>' . $langs->trans("Dictionary") . '</td><td>' . $langs->trans("Action") . '</td></tr>';
echo '</thead><tbody>';
foreach ($module->dictionaries['tabname'] as $key => $value) {
	echo '<tr class="oddeven"><td class="col-setup-title">' . $langs->trans($module->dictionaries['tablib'][$key]) . '</td>';
	echo '<td class="col-setup-action">';
	echo '<a class="butAction" href="' . $_SERVER["PHP_SELF"] . '?action=updateTable&token=' . $token . '&table=' . $key . '">' . $langs->trans("Update") . '</a>';
	echo '</td></tr>';
};
echo '</tbody></table>';

// Page end
print dol_get_fiche_end();

llxFooter();
$db->close();
