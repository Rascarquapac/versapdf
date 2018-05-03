<?php
/* Copyright (C) 2004-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2018 Thierrry Nancy <thierry.nancy@gmail.com>
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    admin/setup.php
 * \ingroup versapdf
 * \brief   versapdf setup page.
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
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

global $langs, $user;

// Libraries
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
require_once '../lib/versapdf.lib.php';
//require_once "../class/myclass.class.php";
// Translations
$langs->load("versapdf@versapdf");

// Access control
if (! $user->admin) accessforbidden();

// Parameters
$action = GETPOST('action', 'alpha');


/*
 * Actions
 */

include DOL_DOCUMENT_ROOT.'/core/actions_setmoduleoptions.inc.php';


/*
 * View
 */

$page_name = "versapdfSetup";
llxHeader('', $langs->trans($page_name));

// Subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">' . $langs->trans("BackToModuleList") . '</a>';
print load_fiche_titre($langs->trans($page_name), $linkback);

// Configuration header
$head = versapdfAdminPrepareHead();
dol_fiche_head(
	$head,
	'settings',
	$langs->trans("versapdf"),
	0,
	"versapdf@versapdf"
);

// Setup page goes here
echo $langs->trans("versapdfSetupPage");

//BEGIN TN test
$form = new Form($db);

$help_url='EN:Module_Foundations|FR:Module_Adh&eacute;rents|ES:M&oacute;dulo_Miembros';

print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="updateall">';

print load_fiche_titre($langs->trans("MemberMainOptions"),'','');
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Description").'</td>';
print '<td>'.$langs->trans("Value").'</td>';
print "</tr>\n";

// Login/Pass required for members
print '<tr class="oddeven"><td>'.$langs->trans("AdherentLoginRequired").'</td><td>';
print $form->selectyesno('ADHERENT_LOGIN_NOT_REQUIRED',(! empty($conf->global->ADHERENT_LOGIN_NOT_REQUIRED)?0:1),1);
print "</td></tr>\n";

// Mail required for members
print '<tr class="oddeven"><td>'.$langs->trans("AdherentMailRequired").'</td><td>';
print $form->selectyesno('ADHERENT_MAIL_REQUIRED',(! empty($conf->global->ADHERENT_MAIL_REQUIRED)?$conf->global->ADHERENT_MAIL_REQUIRED:0),1);
print "</td></tr>\n";

// Send mail information is on by default
print '<tr class="oddeven"><td>'.$langs->trans("MemberSendInformationByMailByDefault").'</td><td>';
print $form->selectyesno('ADHERENT_DEFAULT_SENDINFOBYMAIL',(! empty($conf->global->ADHERENT_DEFAULT_SENDINFOBYMAIL)?$conf->global->ADHERENT_DEFAULT_SENDINFOBYMAIL:0),1);
print "</td></tr>\n";

// Insert subscription into bank account
print '<tr class="oddeven"><td>'.$langs->trans("MoreActionsOnSubscription").'</td>';
$arraychoices=array('0'=>$langs->trans("None"));
if (! empty($conf->banque->enabled)) $arraychoices['bankdirect']=$langs->trans("MoreActionBankDirect");
if (! empty($conf->banque->enabled) && ! empty($conf->societe->enabled) && ! empty($conf->facture->enabled)) $arraychoices['invoiceonly']=$langs->trans("MoreActionInvoiceOnly");
if (! empty($conf->banque->enabled) && ! empty($conf->societe->enabled) && ! empty($conf->facture->enabled)) $arraychoices['bankviainvoice']=$langs->trans("MoreActionBankViaInvoice");
print '<td>';
print $form->selectarray('ADHERENT_BANK_USE',$arraychoices,$conf->global->ADHERENT_BANK_USE,0);
print '</td>';
print "</tr>\n";

// Use vat for invoice creation
if ($conf->facture->enabled)
{
    print '<tr class="oddeven"><td>'.$langs->trans("VATToUseForSubscriptions").'</td>';
    if (! empty($conf->banque->enabled))
    {
        print '<td>';
        print $form->selectarray('ADHERENT_VAT_FOR_SUBSCRIPTIONS', array('0'=>$langs->trans("NoVatOnSubscription"),'defaultforfoundationcountry'=>$langs->trans("Default")), (empty($conf->global->ADHERENT_VAT_FOR_SUBSCRIPTIONS)?'0':$conf->global->ADHERENT_VAT_FOR_SUBSCRIPTIONS), 0);
        print '</td>';
    }
    else
    {
        print '<td align="right">';
        print $langs->trans("WarningModuleNotActive",$langs->transnoentities("Module85Name"));
        print '</td>';
    }
    print "</tr>\n";
    
    if (! empty($conf->product->enabled) || ! empty($conf->service->enabled))
    {
        print '<tr class="oddeven"><td>'.$langs->trans("ADHERENT_PRODUCT_ID_FOR_SUBSCRIPTIONS").'</td>';
        print '<td>';
        $form->select_produits($conf->global->ADHERENT_PRODUCT_ID_FOR_SUBSCRIPTIONS, 'ADHERENT_PRODUCT_ID_FOR_SUBSCRIPTIONS');
        print '</td>';
    }
    print "</tr>\n";
}

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
