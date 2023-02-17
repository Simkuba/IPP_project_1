<?php
/**
 * @file parse.php
 * @author Jakub Čoček
 * @brief the first part of IPP project
 */

//debug to stderr
ini_set('display_errors', 'stderr');
//TODO: kontrola, zda se nekde nemaj pouzivat jiny error cody, osetrit rozsah esc. seq
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
 * @brief function that divide string by the first apperence of certain character
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
        $new_arr[0] = substr($string, 0, $pos);
        $new_arr[1] = substr($string, $pos + 1);
        return $new_arr;
    }
}

/**
 * @brief function looks for problematic XML characters (<,>,&) and replace them with proper XML form
 * @string string to be searched
 * @ret string with correct characters
 */
function problem_chars_treatment($string)
{
    $string = str_replace("&", "&amp;", $string);
    $string = str_replace("<", "&lt;", $string);
    $string = str_replace(">", "&gt;", $string);

    return $string;
}

//function for printing and checking arg2 symbol and arg3 symbol
function symb1_symb2_shortcut(&$arr)
{
    //arg2: symbol expected
    //dividing symbol to type and it's value
    $symb = explode_first("@", $arr[2]);
    if(preg_match_all("/^(LF|TF|GF)@[$&\-_A-Za-z!?*][0-9$&\-_A-Za-z!?*]*/", $arr[2])){
        //symbol is a variable
        $arr[2] = problem_chars_treatment($arr[2]);
        echo("      <arg2 type=\"var\">").$arr[2]."</arg2>\n";
    }
    else if($symb[0] == "int"){
        if(!preg_match_all("/^[0-9]+/", $symb[1])){
            fprintf(STDERR, "\nŠpatný formát int@$symb[1].\n");
            exit(ER_OTHER);
        }

        echo("      <arg2 type=\"int\">").$symb[1]."</arg2>\n";
    }
    else if($symb[0] == "bool"){
        if($symb[1] != "true" || $symb[1] != "false"){   
            fprintf(STDERR, "\nŠpatný formát bool@$symb[1].\n");
            exit(ER_OTHER);
        }
        echo("      <arg2 type=\"bool\">").$symb[1]."</arg2>\n";
    }
    else if($symb[0] == "string"){
        $symb[1] = problem_chars_treatment($symb[1]);
        echo("      <arg2 type=\"string\">").$symb[1]."</arg2>\n";
    }
    else if($symb[0] == "nil"){
        if($symb[1] != "nil"){
            fprintf(STDERR, "\nŠpatný formát nil@$symb[1].\n");
            exit(ER_OTHER);
        }
        echo("      <arg2 type=\"nil\">").$symb[1]."</arg2>\n";
    }
    else{
        fprintf(STDERR, "Špatný formát arg2!\n");
        exit(ER_OTHER);
    }

    //arg3: symbol expected 
    //dividing symbol to type and it's value
    $symb = explode_first("@", $arr[3]);
    if(preg_match_all("/^(LF|TF|GF)@[$&\-_A-Za-z!?*][0-9$&\-_A-Za-z!?*]*/", $arr[3])){
        //symbol is a variable
        $arr[3] = problem_chars_treatment(($arr[3]));
        echo("      <arg3 type=\"var\">").$arr[3]."</arg3>\n";
    }
    else if($symb[0] == "int"){
        if(!preg_match_all("/^[0-9]+/", $symb[1])){
            fprintf(STDERR, "\n Špatný formát int@$symb[1].\n");
            exit(ER_OTHER);
        }
        
        echo("      <arg3 type=\"int\">").$symb[1]."</arg3>\n";
    }
    else if($symb[0] == "bool"){
        if($symb[1] != "true" || $symb[1] != "false"){   
            fprintf(STDERR, "\nŠpatný formát bool@$symb[1].\n");
            exit(ER_OTHER);
        }

        echo("      <arg3 type=\"bool\">").$symb[1]."</arg3>\n";
    }
    else if($symb[0] == "string"){
        $symb[1] = problem_chars_treatment($symb[1]);
        echo("      <arg3 type=\"string\">").$symb[1]."</arg3>\n";
    }
    else if($symb[0] == "nil"){
        if($symb[1] != "nil"){
            fprintf(STDERR, "\n Špatný formát nil@$symb[1].\n");
            exit(ER_OTHER);
        }

        echo("      <arg3 type=\"nil\">").$symb[1]."</arg3>\n";
    }
    else{
        fprintf(STDERR, "\nŠpatný formát arg2!\n");
        exit(ER_OTHER);
    }

    return;
}

//functions for printing xml code
function no_args_opc(&$arr)
{
    global $order;

    //only comment is allowed after this opcode
    if((!empty($arr[1])) && (!preg_match_all("/^#/", $arr[1]))){
        fprintf(STDERR, "\nOperační kód $arr[0] nepodporuje žádné další argumenty!\n");
        exit(ER_OTHER);
    }

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
    if(preg_match_all("/^(LF|TF|GF)@[$&\-_A-Za-z!?*][0-9$&\-_A-Za-z!?*]*/", $arr[1])){
        $arr[1] = problem_chars_treatment($arr[1]);
        echo("      <arg1 type=\"var\">").$arr[1]."</arg1>\n";
    }
    else{
        fprintf(STDERR, " Špatný formát arg1!\n");
        exit(ER_OTHER);
    }

    //arg2: symbol expected
    //dividing symbol to type and it's value
    $symb = explode_first("@", $arr[2]);
    if(preg_match_all("/^(LF|TF|GF)@[$&\-_A-Za-z!?*][0-9$&\-_A-Za-z!?*]*/", $arr[2])){
        //symbol is a variable
        $arr[2] = problem_chars_treatment($arr[2]);
        echo("      <arg2 type=\"var\">").$arr[2]."</arg2>\n";
    }
    else if($symb[0] == "int"){
        if(!preg_match_all("/^[0-9]+/", $symb[1])){
            fprintf(STDERR, "\n Špatný formát int@$symb[1].\n");
            exit(ER_OTHER);
        }

        echo("      <arg2 type=\"int\">").$symb[1]."</arg2>\n";
    }
    else if($symb[0] == "bool"){
        if($symb[1] != "true" || $symb[1] != "false"){   
            fprintf(STDERR, "\n Špatný formát bool@$symb[1].\n");
            exit(ER_OTHER);
        }
        echo("      <arg2 type=\"bool\">").$symb[1]."</arg2>\n";
    }
    else if($symb[0] == "string"){
        $symb[1] = problem_chars_treatment($symb[1]);
        echo("      <arg2 type=\"string\">").$symb[1]."</arg2>\n";
    }
    else if($symb[0] == "nil"){
        if($symb[1] != "nil"){
            fprintf(STDERR, "\n Špatný formát nil@$symb[1].\n");
            exit(ER_OTHER);
        }

        echo("      <arg2 type=\"nil\">").$symb[1]."</arg2>\n";
    }
    else{
        fprintf(STDERR, " Špatný formát arg2!\n");
        exit(ER_OTHER);
    }

    //only comment is allowed after arg1 and arg2
    if((!empty($arr[3])) && (!preg_match_all("/^#/", $arr[3]))){
        fprintf(STDERR, "\n Špatný formát opcode $arr[0]. Usage: $arr[0] <var> <symb>\n");
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
    if(preg_match_all("/^(LF|TF|GF)@[$&\-_A-Za-z!?*][0-9$&\-_A-Za-z!?*]*/", $arr[1])){
        $arr[1] = problem_chars_treatment($arr[1]);
        echo("      <arg1 type=\"var\">").$arr[1]."</arg1>\n";
    }
    else{
        fprintf(STDERR, " Špatný formát arg1!\n");
        exit(ER_OTHER);
    }

    //only comment is allowed after arg1
    if((!empty($arr[2])) && (!preg_match_all("/^#/", $arr[2]))){
        fprintf(STDERR, "\n Špatný formát opcode $arr[0]. Usage: $arr[0] <var>\n");
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
    if(preg_match_all("/^[$&\-_A-Za-z!?*][0-9$&\-_A-Za-z!?*]*/", $arr[1])){
        echo("      <arg1 type=\"label\">").$arr[1]."</arg1>\n";
    }
    else{
        fprintf(STDERR, " Špatný formát arg1!\n");
        exit(ER_OTHER);
    }

    //only comment is allowed after arg1
    if((!empty($arr[2])) && (!preg_match_all("/^#/", $arr[2]))){
        fprintf(STDERR, "\n Špatný formát opcode $arr[0]. Usage: $arr[0] <label>\n");
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

    //arg1: symbol expected
    //dividing symbol to type and it's value
    $symb = explode_first("@", $arr[1]);
    if(preg_match_all("/^(LF|TF|GF)@[$&\-_A-Za-z!?*][0-9$&\-_A-Za-z!?*]*/", $arr[1])){
        //symbol is a variable
        $arr[1] = problem_chars_treatment($arr[1]);
        echo("      <arg1 type=\"var\">").$arr[1]."</arg1>\n";
    }
    else if($symb[0] == "int"){
        if(!preg_match_all("/^[0-9]+/", $symb[1])){
            fprintf(STDERR, "\n Špatný formát int@$symb[1].\n");
            exit(ER_OTHER);
        }
        
        echo("      <arg1 type=\"int\">").$symb[1]."</arg1>\n";
    }
    else if($symb[0] == "bool"){
        if(!($symb[1] == "true" || $symb[1] == "false")){   
            fprintf(STDERR, "\n Špatný formát bool@$symb[1].\n");
            exit(ER_OTHER);
        }

        echo("      <arg1 type=\"bool\">").$symb[1]."</arg1>\n";
    }
    else if($symb[0] == "string"){
        $symb[1] = problem_chars_treatment($symb[1]);
        echo("      <arg1 type=\"string\">").$symb[1]."</arg1>\n";
    }
    else if($symb[0] == "nil"){
        if($symb[1] != "nil"){
            fprintf(STDERR, "\n Špatný formát nil@$symb[1].\n");
            exit(ER_OTHER);
        }

        echo("      <arg1 type=\"nil\">").$symb[1]."</arg1>\n";
    }
    else{
        fprintf(STDERR, " Špatný formát arg2!\n");
        exit(ER_OTHER);
    }

    //only comment is allowed after arg1
    if((!empty($arr[2])) && (!preg_match_all("/^#/", $arr[2]))){
        fprintf(STDERR, "\n Špatný formát opcode $arr[0]. Usage: $arr[0] <symb>\n");
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
    if(preg_match_all("/^(LF|TF|GF)@[$&\-_A-Za-z!?*][0-9$&\-_A-Za-z!?*]*/", $arr[1])){
        $arr[1] = problem_chars_treatment($arr[1]);
        echo("      <arg1 type=\"var\">").$arr[1]."</arg1>\n";
    }
    else{
        fprintf(STDERR, " Špatný formát arg1!\n");
        exit(ER_OTHER);
    }

    //symbol expected for arg2 and arg3
    symb1_symb2_shortcut($arr);

    //only comment is allowed after arg3
    if((!empty($arr[4])) && (!preg_match_all("/^#/", $arr[4]))){
        fprintf(STDERR, "\n Špatný formát opcode $arr[0]. Usage: $arr[0] <var> <symb1> <symb2>\n");
        exit(ER_OTHER);
    }

    echo("  </instruction>\n");
    $order++;
    return;
}

function var_type_opc(&$arr)
{
    global $order;

    echo("  <instruction order=\"").$order."\" opcode=\"".$arr[0]."\">\n";

    //arg1: variable expected
    if(preg_match_all("/^(LF|TF|GF)@[$&\-_A-Za-z!?*][0-9$&\-_A-Za-z!?*]*/", $arr[1])){
        $arr[1] = problem_chars_treatment($arr[1]);
        echo("      <arg1 type=\"var\">").$arr[1]."</arg1>\n";
    }
    else{
        fprintf(STDERR, " Špatný formát arg1!\n");
        exit(ER_OTHER);
    }

    //arg2: type expected
    if($arr[2] == "int" || $arr[2] == "string" || $arr[2] == "bool" ){
        echo("      <arg2 type=\"type\">").$arr[2]."</arg2>\n";
    }
    else{
        fprintf(STDERR, "\n Špatný formát arg2!\n");
        exit(ER_OTHER);
    }

    //only comment is allowed after arg1 and arg2
    if((!empty($arr[3])) && (!preg_match_all("/^#/", $arr[3]))){
        fprintf(STDERR, "\n Špatný formát opcode $arr[0]. Usage: $arr[0] <var> <type>\n");
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
    if(preg_match_all("/^[$&\-_A-Za-z!?*][0-9$&\-_A-Za-z!?*]*/", $arr[1])){
        echo("      <arg1 type=\"label\">").$arr[1]."</arg1>\n";
    }
    else{
        fprintf(STDERR, "\n Špatný formát arg1!\n");
        exit(ER_OTHER);
    }

    //symbol expected for arg2 and arg3
    symb1_symb2_shortcut($arr);

    //only comment is allowed after arg3
    if((!empty($arr[4])) && (!preg_match_all("/^#/", $arr[4]))){
        fprintf(STDERR, "\n Špatný formát opcode $arr[0]. Usage: $arr[0] <label> <symb1> <sym2>\n");
        exit(ER_OTHER);
    }

    echo("  </instruction>\n");
    $order++;
    return;
}

//taking care of argument --help (-help)
if($argc > 1){
    if(($argv[1] == "--help" && $argc > 2) || ($argv[1] == "-help" && $argc > 2)){
        fprintf(STDERR, "\nS parametrem --help nelze kombinovat dalsi parametry!\n");
        exit(ER_PARAMS);
    }
    else if($argv[1] == "--help" || $argv[1] == "-help"){
        echo("Pouziti: php8.1 parse.php [--help]\n");
        exit(ER_NONE);
    }
    else{
        fprintf(STDERR, "\nJediny podporovany parametr: --help\n");
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
        else if(empty($line)){
            //empty lines before header is allowed
            continue;
        }
        else if(preg_match_all("/^#/", $arr[0])){
            //comment lines before header is allowed
            continue;
        }
        else{
            fprintf(STDERR,"\nChybna nebo chybejici hlavicka ve zdrojovem kodu!\n");
            exit(ER_HEADER);
        }
        
        continue;
    }
    
    //empty line -> skip
    if(empty($line)){
        continue;
    }

    //comment line -> skip
    if(preg_match_all("/^#/", $arr[0]) || preg_match_all("/^[ ]*#/", $line)){
        continue;
    }

    switch($arr[0])
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
            fprintf(STDERR, "\nNeznamy nebo chybny operacni kod ve zdrojovem kodu!\n");
            exit(ER_OPCODE);
    }

}

//closing root element
echo("</program>\n");
exit(ER_NONE);
?>