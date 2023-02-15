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

//global variable for counting instructions
$order = 1;

/**
 * function that divide string by the first apperence of certain character
 * @char character by which to split the string
 * @string string to be splitted
 * @ret new array with string before char on pos 0 and string after char in pos 1 or script ends with error code 23
 */
function explode_first($char, $string)
{
    $pos = strpos($string, $char);
    if ($pos === false) {
        //char not found, syntax error -> abort
        exit(ER_OTHER);
    } 
    else {
        //char found, split the string into two substrings
        $before = substr($string, 0, $pos);
        $after = substr($string, $pos + 1);
        $new_arr[0] = $before;
        $new_arr[1] = $after;
        return $new_arr;
    }
}

//function for printing and checking arg2 symbol and arg3 symbol
function symb1_symb2_shortcut(&$arr)
{
    //arg2: symbol expected //TODO: funguje, jen je treba osetrit escape seq ve strs
    //dividing symbol to type and it's value
    $symb = explode_first("@", $arr[2]);
    if(preg_match_all("/(LF|TF|GF)@[$&\-_A-Za-z!?*][0-9$&\-_A-Za-z!?*]*/", $arr[2])){
        //symbol is a variable
        echo("      <arg2 type=\"var\">").$arr[2]."</arg2>\n";
    }
    else if($symb[0] == "int"){
        //TODO: kontrola, zda se opravdu jedná o int, podobně kontrolvoat i ostatní
        echo("      <arg2 type=\"int\">").$symb[1]."</arg2>\n";
    }
    else if($symb[0] == "bool"){
        echo("      <arg2 type=\"bool\">").$symb[1]."</arg2>\n";
    }
    else if($symb[0] == "string"){
        echo("      <arg2 type=\"string\">").$symb[1]."</arg2>\n";
    }
    else if($symb[0] == "nil"){
        echo("      <arg2 type=\"nil\">").$symb[1]."</arg2>\n";
    }
    else{
        fprintf(STDERR, "Wrong format of arg2!\n");
        exit(ER_OTHER);
    }

    //arg3: symbol expected //TODO: funguje, jen je treba osetrit escape seq ve strs
    //dividing symbol to type and it's value
    $symb = explode_first("@", $arr[3]);
    if(preg_match_all("/(LF|TF|GF)@[$&\-_A-Za-z!?*][0-9$&\-_A-Za-z!?*]*/", $arr[3])){
        //symbol is a variable
        echo("      <arg3 type=\"var\">").$arr[3]."</arg3>\n";
    }
    else if($symb[0] == "int"){
        //TODO: kontrola, zda se opravdu jedná o int, podobně kontrolvoat i ostatní
        echo("      <arg3 type=\"int\">").$symb[1]."</arg3>\n";
    }
    else if($symb[0] == "bool"){
        echo("      <arg3 type=\"bool\">").$symb[1]."</arg3>\n";
    }
    else if($symb[0] == "string"){
        echo("      <arg3 type=\"string\">").$symb[1]."</arg3>\n";
    }
    else if($symb[0] == "nil"){
        echo("      <arg3 type=\"nil\">").$symb[1]."</arg3>\n";
    }
    else{
        fprintf(STDERR, "Wrong format of arg2!\n");
        exit(ER_OTHER);
    }

    return;
}

//functions for printing xml code TODO: nepovolene znaky za koncem povolenych argumentu krom komentaru
function no_args_opc(&$arr)
{
    global $order;
    echo("  <instruction order=\"").$order."\" opcode=\"".$arr[0]."\">\n";
    echo("  </instruction>\n");
    $order++;
    return;
}

function var_symb_opc(&$arr)
{
    global $order;
    echo("  <instruction order=\"").$order."\" opcode=\"".$arr[0]."\">\n";

    //arg1: variable expected
    if(preg_match_all("/(LF|TF|GF)@[$&\-_A-Za-z!?*][0-9$&\-_A-Za-z!?*]*/", $arr[1])){
        echo("      <arg1 type=\"var\">").$arr[1]."</arg1>\n";
    }
    else{
        fprintf(STDERR, "Wrong format of arg1!\n");
        exit(ER_OTHER);
    }

    //arg2: symbol expected //TODO: funguje, jen je treba osetrit escape seq ve strs
    //dividing symbol to type and it's value
    $symb = explode_first("@", $arr[2]);
    if(preg_match_all("/(LF|TF|GF)@[$&\-_A-Za-z!?*][0-9$&\-_A-Za-z!?*]*/", $arr[2])){
        //symbol is a variable
        echo("      <arg2 type=\"var\">").$arr[2]."</arg2>\n";
    }
    else if($symb[0] == "int"){
        //TODO: kontrola, zda se opravdu jedná o int, podobně kontrolvoat i ostatní
        echo("      <arg2 type=\"int\">").$symb[1]."</arg2>\n";
    }
    else if($symb[0] == "bool"){
        echo("      <arg2 type=\"bool\">").$symb[1]."</arg2>\n";
    }
    else if($symb[0] == "string"){
        echo("      <arg2 type=\"string\">").$symb[1]."</arg2>\n";
    }
    else if($symb[0] == "nil"){
        echo("      <arg2 type=\"nil\">").$symb[1]."</arg2>\n";
    }
    else{
        fprintf(STDERR, "Wrong format of arg2!\n");
        exit(ER_OTHER);
    }

    echo("  </instruction>\n");
    $order++;
    return;
}

function var_opc(&$arr)
{
    global $order;
    echo("  <instruction order=\"").$order."\" opcode=\"".$arr[0]."\">\n";

    //arg1: variable expected
    if(preg_match_all("/(LF|TF|GF)@[$&\-_A-Za-z!?*][0-9$&\-_A-Za-z!?*]*/", $arr[1])){
        echo("      <arg1 type=\"var\">").$arr[1]."</arg1>\n";
    }
    else{
        fprintf(STDERR, "Wrong format of arg1!\n");
        exit(ER_OTHER);
    }

    echo("  </instruction>\n");
    $order++;
    return;
}

function label_opc(&$arr)
{
    global $order;
    echo("  <instruction order=\"").$order."\" opcode=\"".$arr[0]."\">\n";

    //arg1: label is expected
    if(preg_match_all("/[$&\-_A-Za-z!?*][0-9$&\-_A-Za-z!?*]*/", $arr[1])){
        echo("      <arg1 type=\"label\">").$arr[1]."</arg1>\n";
    }
    else{
        fprintf(STDERR, "Wrong format of arg1!\n");
        exit(ER_OTHER);
    }

    echo("  </instruction>\n");
    $order++;
    return;

}

function symb_opc(&$arr)
{
    global $order;

    echo("  <instruction order=\"").$order."\" opcode=\"".$arr[0]."\">\n";

    //arg1: symbol expected //TODO: funguje, jen je treba osetrit escape seq ve strs
    //dividing symbol to type and it's value
    $symb = explode_first("@", $arr[1]);
    if(preg_match_all("/^(LF|TF|GF)@[$&\-_A-Za-z!?*][0-9$&\-_A-Za-z!?*]*/", $arr[1])){
        //symbol is a variable
        echo("      <arg1 type=\"var\">").$arr[1]."</arg1>\n";
    }
    else if($symb[0] == "int"){
        //TODO: kontrola, zda se opravdu jedná o int, podobně kontrolvoat i ostatní
        echo("      <arg1 type=\"int\">").$symb[1]."</arg1>\n";
    }
    else if($symb[0] == "bool"){
        echo("      <arg1 type=\"bool\">").$symb[1]."</arg1>\n";
    }
    else if($symb[0] == "string"){
        echo("      <arg1 type=\"string\">").$symb[1]."</arg1>\n";
    }
    else if($symb[0] == "nil"){
        echo("      <arg1 type=\"nil\">").$symb[1]."</arg1>\n";
    }
    else{
        fprintf(STDERR, "Wrong format of arg2!\n");
        exit(ER_OTHER);
    }

    echo("  </instruction>\n");
    $order++;
    return;

}

function var_symb1_symb2_opc(&$arr)
{
    global $order;

    echo("  <instruction order=\"").$order."\" opcode=\"".$arr[0]."\">\n";

    //arg1: variable expected
    if(preg_match_all("/(LF|TF|GF)@[$&\-_A-Za-z!?*][0-9$&\-_A-Za-z!?*]*/", $arr[1])){
        echo("      <arg1 type=\"var\">").$arr[1]."</arg1>\n";
    }
    else{
        fprintf(STDERR, "Wrong format of arg1!\n");
        exit(ER_OTHER);
    }

    //symbol expected for arg1 and arg2
    symb1_symb2_shortcut($arr);

    echo("  </instruction>\n");
    $order++;
    return;
}

function var_type_opc(&$arr)
{
    global $order;

    echo("  <instruction order=\"").$order."\" opcode=\"".$arr[0]."\">\n";

    //arg1: variable expected
    if(preg_match_all("/(LF|TF|GF)@[$&\-_A-Za-z!?*][0-9$&\-_A-Za-z!?*]*/", $arr[1])){
        echo("      <arg1 type=\"var\">").$arr[1]."</arg1>\n";
    }
    else{
        fprintf(STDERR, "Wrong format of arg1!\n");
        exit(ER_OTHER);
    }

    //arg2: type expected
    $type = explode_first("@", $arr[2]);
    if($type[0] == "int"){
        //TODO: kontrola, zda se opravdu jedná o int, podobně kontrolvoat i ostatní
        echo("      <arg2 type=\"int\">").$type[1]."</arg2>\n";
    }
    else if($type[0] == "bool"){
        echo("      <arg2 type=\"bool\">").$type[1]."</arg2>\n";
    }
    else if($type[0] == "string"){
        echo("      <arg2 type=\"string\">").$type[1]."</arg2>\n";
    }
    else{
        fprintf(STDERR, "Wrong format of arg2!\n");
        exit(ER_OTHER);
    }

    echo("  </instruction>\n");
    $order++;
    return;
}

function label_symb1_symb2_opc(&$arr)
{
    global $order;

    echo("  <instruction order=\"").$order."\" opcode=\"".$arr[0]."\">\n";

    //arg1: label is expected
    if(preg_match_all("/[$&\-_A-Za-z!?*][0-9$&\-_A-Za-z!?*]*/", $arr[1])){
        echo("      <arg1 type=\"label\">").$arr[1]."</arg1>\n";
    }
    else{
        fprintf(STDERR, "Wrong format of arg1!\n");
        exit(ER_OTHER);
    }

    //symbol expected for arg1 and arg2
    symb1_symb2_shortcut($arr);

    echo("  </instruction>\n");
    $order++;
    return;
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

//main loop
while(($line = fgets(STDIN)) != NULL)
{
    //deleting new line, cutting line into array of strings and transfer the first word to uppercase
    $line = rtrim($line);
    $arr = explode(" ", $line);
    $arr[0] = strtoupper($arr[0]);

    //check for header
    if(!($header_present)){
        if($arr[0] == ".IPPCODE23"){
            $header_present = true;

            //printing xml header and root element
            echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
            echo("<program language=\"IPPcode23\">\n");
        }
        else{
            fprintf(STDERR,"Chybna nebo chybejici hlavicka ve zdrojovem kodu!\n");
            exit(ER_HEADER);
        }
        
        continue;
    }
    
    //empty line -> skip
    if(empty($line)){
        continue;
    }

    switch($arr[0])
    {
        /**** comment ****/
        case "#":
            break;
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

}

//closing root element
echo("</program>\n");
exit(ER_NONE);
?>