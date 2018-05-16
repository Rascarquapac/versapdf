<?php
/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2015      Jean-François Ferry	<jfefe@aternatik.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file       htdocs/versapdf/template/versapdfindex.php
 *	\ingroup    versapdf
 *	\brief      Home page of versapdf top menu
 */

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include($_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php");
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include(substr($tmp, 0, ($i+1))."/main.inc.php");
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php");
// Try main.inc.php using relative path
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

// Libraries
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
require_once './lib/versapdf.lib.php';

//Globals
global $langs, $user;

// Access control
if (! $user->rights->versapdf->read) accessforbidden();
//if (! $user->admin) accessforbidden();

// Translations
$langs->load("versapdf@versapdf");

// Parameters
$action = GETPOST('action', 'alpha');

// Securite acces client
$socid=GETPOST('socid','int');
if (isset($user->societe_id) && $user->societe_id > 0)
{
    $action = '';
    $socid = $user->societe_id;
}
$max=5;
$now=dol_now();

/*
 * Actions
 */

include DOL_DOCUMENT_ROOT.'/core/actions_setmoduleoptions.inc.php';
if ($action == 'updateall')
{
    $db->begin();
    $res1=$res2=0;
    $res1=dolibarr_set_const($db, 'VERSAPDF_COLWIDTH_VAT', GETPOST('VERSAPDF_COLWIDTH_VAT', 'alpha'), 'chaine', 0, '', $conf->entity);
    $res2=dolibarr_set_const($db, 'VERSAPDF_COLWIDTH_UP', GETPOST('VERSAPDF_COLWIDTH_UP', 'alpha'), 'chaine', 0, '', $conf->entity);
    $res3=dolibarr_set_const($db, 'VERSAPDF_COLWIDTH_UNIT', GETPOST('VERSAPDF_COLWIDTH_UNIT', 'alpha'), 'chaine', 0, '', $conf->entity);
    //$res=dolibarr_set_const($db,$constname,$constvalue,$type[$consttype],0,$constnote,$conf->entity);

    if ($res1 < 0 || $res2 < 0 || $res3 < 0)
    {
        setEventMessages('ErrorFailedToSaveDate', null, 'errors');
        $db->rollback();
    }
    else
    {
        setEventMessages('RecordModifiedSuccessfully', null, 'mesgs');
        $db->commit();
    }
}

/*
 * View
 */
$page_name = "versapdfSetup";
llxHeader('', $langs->trans($page_name));

// Subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">' . $langs->trans("BackToModuleList") . '</a>';
$linkback ='<a> </a>';
print load_fiche_titre($langs->trans($page_name), $linkback);

$h = 0;
$head = array();

$head[$h][0] = dol_buildpath("/custom/versapdf/parameters1_page.php", 1);
$head[$h][1] = $langs->trans('Columns');
$head[$h][2] = 'param1';
$h++;
$head[$h][0] = dol_buildpath("/custom/versapdf/parameters2_page.php", 1);
$head[$h][1] = 'Parameters 2';
$head[$h][2] = 'param2';
$h++;

$arraychoices=array('0'=>$langs->trans("nocolumn"));
$arraychoices['1']='1';
$arraychoices['2']='2';
$arraychoices['3']='3';
$arraychoices['4']='4';
$arraychoices['5']='5';
$arraychoices['6']='6';
$arraychoices['7']='7';
$arraychoices['8']='8';
$arraychoices['9']='9';
$arraychoices['10']='10';
$arraychoices['11']='11';
$arraychoices['12']='12';
$arraychoices['13']='13';
$arraychoices['14']='14';
$arraychoices['15']='15';
$arraychoices['16']='16';
$arraychoices['17']='17';
$arraychoices['18']='18';
$arraychoices['19']='19';
$arraychoices['20']='20';
$arraychoices['21']='21';
$arraychoices['22']='22';
$arraychoices['23']='23';
$arraychoices['24']='24';
$arraychoices['25']='25';
$arraychoices['26']='26';
$arraychoices['27']='27';
$arraychoices['28']='28';
$arraychoices['29']='29';
$arraychoices['30']='30';

// Configuration header
dol_fiche_head(
    $head,
    'param1',
    $langs->trans("versapdf"),
    0,
    "versapdf@versapdf"
    );

//BEGIN TN test
$form = new Form($db);

print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<input type="hidden" name="action" value="updateall">';

print load_fiche_titre($langs->trans("columnsWidths"),'','');
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Description").'</td>';
print '<td>'.$langs->trans("Value").'</td>';
print "</tr>\n";

// VAT column width
print '<tr class="oddeven"><td>'.$langs->trans("VATColumnWidth").'</td><td>';
$versapdf_column_width=(! empty($conf->global->VERSAPDF_COLWIDTH_VAT)?$conf->global->VERSAPDF_COLWIDTH_VAT:'0');
print $form->selectarray('VERSAPDF_COLWIDTH_VAT', $arraychoices,$versapdf_column_width);
print "</td></tr>\n";

// Price Unit column width
print '<tr class="oddeven"><td>'.$langs->trans("UPColumnWidth").'</td><td>';
$versapdf_column_width=(! empty($conf->global->VERSAPDF_COLWIDTH_UP)?$conf->global->VERSAPDF_COLWIDTH_UP:'0');
print $form->selectarray('VERSAPDF_COLWIDTH_UP', $arraychoices,$versapdf_column_width);
print "</td></tr>\n";
// Unit column width
print '<tr class="oddeven"><td>'.$langs->trans("UnitColumnWidth").'</td><td>';
$versapdf_column_width=(! empty($conf->global->VERSAPDF_COLWIDTH_UNIT)?$conf->global->VERSAPDF_COLWIDTH_UNIT:'0');
print $form->selectarray('VERSAPDF_COLWIDTH_UNIT', $arraychoices,$versapdf_column_width);
print "</td></tr>\n";

print '</table>';

print '<center>';
print '<input type="submit" class="button" value="'.$langs->trans("Update").'" name="Button">';
print '</center>';

print '</form>';

print '<br>';

//END TN test



// Page end
dol_fiche_end();
llxFooter();
