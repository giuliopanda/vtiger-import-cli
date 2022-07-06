<?php
/**
 * PHP COMMAND LINE SCRIPT PER Vtiger7:
 * Sistema di importazione automatico dei moduli in CLI.
 * 
 * #Istruzioni
 * inserisci il file nella directory principale di vtiger7, 
 * poi carica sempre nella directory principale i vari moduli in formato zip da installare.
 * esegui
 * ```
 * php -f import-zip.php exec
 * ```
 * exec per installare
 * help per la guida
 * list per l'elenco dei moduli che verranno aggiornati e/o installati
 */

require_once 'vtlib/Vtiger/Module.php';
require_once 'vtlib/Vtiger/Package.php';

/**
 * @var Array $cmd_list L'elenco dei comandi 
 */
$cmd_list = ['help'=>'help', 'list'=>'list of modules to install', 'exec'=>'install all modules'];
/**
 * @var String $command il primo parametro passato in cli dalla funzione
 */
$command = gp_argv($argv);

/**
 * @var Array $zip_list la lista degli zip da installare
 */
$zip_list = gp_scan_dir();
/**
 * @var Vtiger_Package $package 
 */
$package = new Vtiger_Package();
/**
 * @var Boolean $Vtiger_Utils_Log 
 */
$Vtiger_Utils_Log = true;

// Controller
if (!count($zip_list)) {
    gp_die("Trasferisci uno o più zip di moduli da installare all'interno della directori principale di vtiger7.");
}
switch ($command) {
    case 'exec' :
        gp_import_list($zip_list);
        break;
    case 'list' :
        gp_echo_list($zip_list);
        break;
    case 'help' :
        gp_die("Help:");
        break;
    default :
        gp_die("Devi inserire uno dei seguenti comandi:");
        break;
} 


/**************
 *  FUNCTIONS *
 **************/

/**
 * Ritorna il primo parametro passato in cli.
 * @return string
 */
function gp_argv($argv) {
    array_shift($argv);
    return strtolower(trim(reset($argv)));
}

/**
 * Restituisce l'elenco degli zip da installare
 * @return array 
 */
function gp_scan_dir() {
    $list = [];
    $files = scandir(__DIR__);
    foreach ($files as $file_name) {
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        if ($ext == "zip") {
            $list[] = $file_name;
        }
    }
    return $list;
}


/**
 * Stampa un messaggio, l'help e interrompe lo script
 * @param string $output_msg
 * @return void
 */
function gp_die($output_msg) {
    global $cmd_list;
    echo $output_msg."\n\n";
    foreach ($cmd_list as $key=>$value) {
        echo "\t".$key."\t\t=".$value."\n";
    }
    die();
}

/**
 * Stampa la lista dei moduli da installlare
 */
function gp_echo_list($file_list) {
    echo  "\n========= Verranno installati i seguenti moduli =========\n";
    foreach ($file_list as $file_name) {
        echo "  - ".$file_name."\n";
    }
}
/**
 * Importa l'elenco dei plugin
 * @param Array $file_list
 */
function gp_import_list($file_list) {
    global $package;
    foreach ($file_list as $file_name) {
        echo "\n========= ".$file_name." =========\n";
        $package->import(__DIR__."/".$file_name); 
    }
}