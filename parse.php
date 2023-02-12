<?php
/**
 * @file parse.php
 * @author Jakub Čoček
 * @brief the first part of IPP project
 */

//debug to stderr
ini_set('display_errors', 'stderr');

//macros for error codes
define("ER_NONE", 0);
define("ER_PARAMS", 10);
define("ER_INPUT", 11);
define("ER_OUTPUT", 12);
define("ER_INTERNAL", 99);
define("ER_HEADER", 21);
define("ER_OPCODE", 22);
define("ER_OTHER", 23);

//functions for printing xml code
function no_args_opc(&$arr)
{

}

function var_symb_opc(&$arr)
{

}

function var_opc(&$arr)
{

}

function label_opc(&$arr)
{

}

function symb_opc(&$arr)
{

}

function var_symb1_symb2_opc(&$arr)
{

}

function var_type_opc(&$arr)
{

}

function label_symb1_symb2_opc(&$arr)
{

}

//taking care of argument --help (-help)
if($argc > 1){
    if(($argv[1] == "--help" && $argc > 2) || ($argv[1] == "-help" && $argc > 2)){
        fprintf(STDERR, "S parametrem --help nelze kombinovat dalsi parametry!\n");
        exit(ER_PARAMS);
    }
    else if($argv[1] == "--help" || $argv[1] == "-help"){
        echo("Pouziti: php8.1 parse.php [--help]\n");
        exit(ER_NONE);
    }
    else{
        fprintf(STDERR, "Jediny podporovany parametr: --help\n");
        exit(ER_PARAMS);
    }
}


$header_present = false;
$order = 1;
$opcode = "";

//main loop
while(($line = fgets(STDIN)) != NULL)
{
    trim($line, "\n");
    $arr = explode(" ", $line);

    //check for header
    if(!$header_present){
        if(strtoupper($arr[0]) == ".IPPCODE23"){
            $header_present = true;

            //printing xml header and root element
            echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
            echo("<program language=\"IPPcode23\">\n");

            continue;
        }
        else{
            fprintf(STDERR,"Chybna nebo chybejici hlavicka ve zdrojovem kodu!\n");
            exit(ER_HEADER);
        }
    }
    
    switch(strtoupper($arr[0]))
    {
        /**** no args ****/
        case "CREATEFRAME":
        case "PUSHFRAME":
        case "POPFRAME":
        case "RETURN":
        case "BREAK":
            no_args_opc($arr);
            break;
        /**** <var> <symb> ****/
        case "MOVE":
        case "INT2CHAR":
        case "STRLEN":
        case "TYPE":
            var_symb_opc($arr);
            break;
        /**** <var> ****/
        case "DEFVAR":
        case "POPS":
            var_opc($arr);
            break;
        /**** <label> ****/
        case "CALL":
        case "LABEL":
        case "JUMP":
            label_opc($arr);
            break;
        /**** <symb> ****/
        case "PUSHS":
        case "WRITE":
        case "EXIT":
        case "DPRINT":
            symb_opc($arr);
            break;
        /**** <var> <symb1> <symb2> ****/
        case "ADD":
        case "SUB":
        case "IDIV":
        case "LT":
        case "GT":
        case "EQ":
        case "AND":
        case "OR":
        case "NOT":
        case "STRI2INT":
        case "CONCAT":
        case "GETCHAR":
        case "SETCHAR":
            var_symb1_symb2_opc($arr);
            break;
        /**** <var> <type> ****/
        case "READ":
            var_type_opc($arr);
            break;
        /**** <label> <symb1> <symb2> ****/
        case "JUMPIFEQ":
        case "JUMPIFNEQ":
            label_symb1_symb2_opc($arr);
            break;
        default:
            fprintf(STDERR, "Neznamy nebo chybny operacni kod ve zdrojovem kodu!\n");
            exit(ER_OPCODE);
    }
    
    #preg_match pro regex

}

//closing root element
echo("</program>\n");
exit(ER_NONE);
?>