Implementační dokumentace k 1. úloze do IPP 2022/2023 Jméno a příjmení: Jakub Čoček 

Login: xcocek00 

1. **Implementace konečného automatu** 

Hlavním tělem skriptu je konečný automat, který je implementován pomocí příkazu switch. Automat bere každý řádek a podle operačního kódu předává řízení konkrétní funkci, které dále kontrolují sémantické 

a syntaktické požadavky. Speciálním případem je kontrola hlavičky .IPPcode23, která není v kontrolována 

v hlavním těle automatu, ale je pro ni vytvořená speciální proměnná typu bool (flag), která v případě její správnosti dovolí zahájit práci automatu, v opačném případě je program ukončen s odpovídajícím chybovým kódem.  

![](Aspose.Words.aa4c5410-f031-42fd-8d12-80aee7438ee2.001.jpeg)

*Obrázek 1 - konečný automat*

2. **Kontrolní funkce** 

Funkce, které mají na starost ověřování syntaktické a sémantické správnosti využívají regulární výrazy pomocí PHP funkce preg\_match\_all. Dále je také ověřováno správné pořadí a typ argumentů, které příslušné operační kódy vyžadují. 

3. **Ostatní funkce** 

Mimo tzv. kontrolních funkcí popsaných v obrázku 1 a bodu 2. jsou implementovány také funkce, které pomáhají text formátovat nebo upravovat, aby vyhovoval formátu XML. O toto se stará např. funkce problem\_chars\_treatment, která kontroluje správnost escape sekvencí, pokud se v řetězci vyskytují, a také hledá a nahrazuje problematické znaky v XML (např.: &, <, >). Dalším příkladem je funkce explode\_first, která modifikuje PHP funkci explode a hledá první výskyt určitého znaku, což je využito například u hledání komentářů. 
