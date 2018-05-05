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

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';

if (! $user->rights->versapdf->read) accessforbidden();

$langs->load("versapdf@versapdf");

$action=GETPOST('action', 'alpha');

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
/*
include DOL_DOCUMENT_ROOT.'/core/actions_setmoduleoptions.inc.php';
if ($action == 'updateall')
{
    $db->begin();
    $res1=$res2=$res3=$res4=$res5=$res6=0;
    $res1=dolibarr_set_const($db, 'ADHERENT_LOGIN_NOT_REQUIRED', GETPOST('ADHERENT_LOGIN_NOT_REQUIRED', 'alpha'), 'chaine', 0, '', $conf->entity);
    $res2=dolibarr_set_const($db, 'ADHERENT_MAIL_REQUIRED', GETPOST('ADHERENT_MAIL_REQUIRED', 'alpha'), 'chaine', 0, '', $conf->entity);
    $res3=dolibarr_set_const($db, 'ADHERENT_DEFAULT_SENDINFOBYMAIL', GETPOST('ADHERENT_DEFAULT_SENDINFOBYMAIL', 'alpha'), 'chaine', 0, '', $conf->entity);
    $res4=dolibarr_set_const($db, 'ADHERENT_BANK_USE', GETPOST('ADHERENT_BANK_USE', 'alpha'), 'chaine', 0, '', $conf->entity);
    // Use vat for invoice creation
    if ($conf->facture->enabled)
    {
        $res4=dolibarr_set_const($db, 'ADHERENT_VAT_FOR_SUBSCRIPTIONS', GETPOST('ADHERENT_VAT_FOR_SUBSCRIPTIONS', 'alpha'), 'chaine', 0, '', $conf->entity);
        $res5=dolibarr_set_const($db, 'ADHERENT_PRODUCT_ID_FOR_SUBSCRIPTIONS', GETPOST('ADHERENT_PRODUCT_ID_FOR_SUBSCRIPTIONS', 'alpha'), 'chaine', 0, '', $conf->entity);
        if (! empty($conf->product->enabled) || ! empty($conf->service->enabled))
        {
            $res6=dolibarr_set_const($db, 'ADHERENT_PRODUCT_ID_FOR_SUBSCRIPTIONS', GETPOST('ADHERENT_PRODUCT_ID_FOR_SUBSCRIPTIONS', 'alpha'), 'chaine', 0, '', $conf->entity);
        }
    }
    if ($res1 < 0 || $res2 < 0 || $res3 < 0 || $res4 < 0 || $res5 < 0 || $res6 < 0)
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

// Action mise a jour ou ajout d'une constante
if ($action == 'update' || $action == 'add')
{
    $constname=GETPOST('constname','alpha');
    $constvalue=(GETPOST('constvalue_'.$constname) ? GETPOST('constvalue_'.$constname) : GETPOST('constvalue'));
    
    if (($constname=='ADHERENT_CARD_TYPE' || $constname=='ADHERENT_ETIQUETTE_TYPE' || $constname=='ADHERENT_PRODUCT_ID_FOR_SUBSCRIPTIONS') && $constvalue == -1) $constvalue='';
    if ($constname=='ADHERENT_LOGIN_NOT_REQUIRED') // Invert choice
    {
        if ($constvalue) $constvalue=0;
        else $constvalue=1;
    }
    
    $consttype=GETPOST('consttype','alpha');
    $constnote=GETPOST('constnote');
    $res=dolibarr_set_const($db,$constname,$constvalue,$type[$consttype],0,$constnote,$conf->entity);
    
    if (! $res > 0) $error++;
    
    if (! $error)
    {
        setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
    }
    else
    {
        setEventMessages($langs->trans("Error"), null, 'errors');
    }
}

// Action activation d'un sous module du module adherent
if ($action == 'set')
{
    $result=dolibarr_set_const($db, GETPOST('name','alpha'),GETPOST('value'),'',0,'',$conf->entity);
    if ($result < 0)
    {
        print $db->error();
    }
}

// Action desactivation d'un sous module du module adherent
if ($action == 'unset')
{
    $result=dolibarr_del_const($db,GETPOST('name','alpha'),$conf->entity);
    if ($result < 0)
    {
        print $db->error();
    }
}
*/


/*
 * View
 */
// Libraries
$h = 0;
$head = array();

$head[$h][0] = dol_buildpath("/custom/versapdf/parameters1_page.php", 1);
$head[$h][1] = $langs->trans("Parameters 1");
$head[$h][2] = 'settings';
$h++;
$head[$h][0] = dol_buildpath("/custom/versapdf/parameters2_page.php", 1);
$head[$h][1] = $langs->trans("Parameters 2");
$head[$h][2] = 'about';
$h++;


require_once DOL_DOCUMENT_ROOT  . "/core/lib/admin.lib.php";
require_once './lib/versapdf.lib.php';

$form = new Form($db);
$formfile = new FormFile($db);

llxHeader("",$langs->trans("versapdfArea"));
dol_fiche_head(
    $head,
    'Parameters 1',
    $langs->trans("versapdf"),
    0,
    "versapdf@versapdf"
    );

print load_fiche_titre($langs->trans("versapdfArea"),'','versapdf.png@versapdf');

print '<div class="fichecenter"><div class="fichethirdleft">';





/**/

print '</div></div></div>';
print '<br>';



// Setup page goes here
echo $langs->trans("versapdfPage");

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

print '</div></div></div>';


llxFooter();

$db->close();
