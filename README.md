# Implementační dokumentace k 1. úloze do IPP 2022/2023 

## Implementace konečného automatu

Hlavním tělem skriptu je konečný automat, který je implementován pomocí příkazu switch. Automat bere každý řádek a podle operačního kódu předává řízení konkrétní funkci, které dále kontrolují sémantické a syntaktické požadavky. Speciálním případem je kontrola hlavičky .IPPcode23, která není v kontrolována 
v hlavním těle automatu, ale je pro ni vytvořená speciální proměnná typu bool (flag), která v případě její správnosti dovolí zahájit práci automatu, v opačném případě je program ukončen s odpovídajícím chybovým kódem.  

## Kontrolní funkce

Funkce, které mají na starost ověřování syntaktické a sémantické správnosti využívají regulární výrazy pomocí PHP funkce preg\_match\_all. Dále je také ověřováno správné pořadí a typ argumentů, které příslušné operační kódy vyžadují. 

## Ostatní funkce

Mimo tzv. kontrolních funkcí popsaných v bodu 2. jsou implementovány také funkce, které pomáhají text formátovat nebo upravovat, aby vyhovoval formátu XML. O toto se stará např. funkce problem\_chars\_treatment, která kontroluje správnost escape sekvencí, pokud se v řetězci vyskytují, a také hledá a nahrazuje problematické znaky v XML (např.: &, <, >). Dalším příkladem je funkce explode\_first, která modifikuje PHP funkci explode a hledá první výskyt určitého znaku, což je využito například u hledání komentářů. 

## Použití

`php8.1 parse.php [--help]`

## Hodnocení
7.2/8

#### Projekt slouží pouze jako inspirace, kopírovat kód silně nedoporučuji.
