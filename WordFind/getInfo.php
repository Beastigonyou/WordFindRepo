<html>
<head>
    <title>Word Puzzle Maker</title>
    <style type = "text/css">
        body{
            background: tan;
            color: black;
        }
    </style>
</head>
<body>
<?php
session_start();
$_SESSION['puzzlename'] = $_GET['puzzlename'];

// WORD PUZZLE MAKER
// Generates a word search puzzle based on a word list
// entered by user. User can also specify the size of
// the puzzle and print out an answer key if desired
global $wordList;
$wordList=$_GET["puzzlewords"];
$wordList = strtoupper($wordList);
//get puzzle data from HTML form
$word = explode("\n", $wordList);

$rawWordList = array();

foreach ($word as $currentWord){
    //take out trailing newline characters
    $currentWord = rtrim($currentWord);
    $currentWord = strtoupper($currentWord);
    array_push($rawWordList,$currentWord);
}


global $currentWord;
$currentWord = $rawWordList;

//check for a word list
if (empty($currentWord)){
    //make default puzzle
    print "Sorry, no data found";
} else {
    $width = $_GET["width"];
    $height = $_GET["height"];
    $boardData = array(
        "width" => $width,
        "height" => $height,
        "puzzlewords" => $currentWord,

    );
}


//try to get a word list from user input
if (parseList() == TRUE){
    $legalBoard = FALSE;

    //keep trying to build a board until you get a legal result
    while ($legalBoard == FALSE){
        clearBoard();
        $legalBoard = fillBoard();
    } // end while

    //make the answer key
    $key = $board;
    $keyPuzzle = makeBoard($key);
    $_SESSION['keypuzzle'] = $keyPuzzle;

    //make the final puzzle
    addFoils();
    $puzzle = makeBoard($board);

    //print out the result page
    printPuzzle();

} // end parsed list if


function parseList(){
    //gets word list, creates array of words from it
    //or return false if impossible

    global $currentWord, $boardData;

    $itWorked = TRUE;

    //convert word list entirely to upper case
    //$wordList = strtoupper($wordList);

    //split word list into array
    //$word = explode("\n", $wordList);

    //foreach ($word as $currentWord){
    //take out trailing newline characters
    //$currentWord = rtrim($currentWord);
    foreach($currentWord as $wordIndex) {
        //stop if any words are too long to fit in puzzle
        if ((strLen($wordIndex) > $boardData["width"]) &&
            (strLen($wordIndex) > $boardData["height"])
        ) {
            print "$wordIndex is too long for puzzle";
            print "Please increase the grid size in previous page and try again";
            $itWorked = FALSE;
        } // end if
    }

    //} // end foreach
    return $itWorked;
} // end parseList

function clearBoard(){
    //initialize board with a . in each cell
    global $board, $boardData;

    for ($row = 0; $row < $boardData["height"]; $row++){
        for ($col = 0; $col < $boardData["width"]; $col++){
            $board[$row][$col] = ".";
        } // end col for loop
    } // end row for loop
} // end clearBoard

function fillBoard(){
    //fill board with list by calling addWord() for each word
    //or return false if failed

    global $word;
    $direction = array("N", "S", "E", "W");
    $itWorked = TRUE;
    $counter = 0;
    $keepGoing = TRUE;
    while($keepGoing){
        $dir = rand(0, 3);
        $result = addWord($word[$counter], $direction[$dir]);
        if ($result == FALSE){
            //print "failed to place $word[$counter]";
            $keepGoing = FALSE;
            $itWorked = FALSE;
        } // end if
        $counter++;
        if ($counter >= count($word)){
            $keepGoing = FALSE;
        } // end if
    } // end while
    return $itWorked;

} // end fillBoard


function addWord($theWord, $dir){
    //attempt to add a word to the board or return false if failed
    global $board, $boardData;

    //remove trailing characters if necessary
    $theWord = rtrim($theWord);

    $itWorked = TRUE;

    switch ($dir){
        case "E":
            //col from 0 to board width - word width
            //row from 0 to board height
            $newCol = rand(0, $boardData["width"] - 1 - strlen($theWord));
            $newRow = rand(0, $boardData["height"]-1);

            for ($i = 0; $i < strlen($theWord); $i++){
                //new character same row, initial column + $i
                $boardLetter = $board[$newRow][$newCol + $i];
                $wordLetter = substr($theWord, $i, 1);

                //check for legal values in current space on board
                if (($boardLetter == $wordLetter) ||
                    ($boardLetter == ".")){
                    $board[$newRow][$newCol + $i] = $wordLetter;
                } else {
                    $itWorked = FALSE;
                } // end if
            } // end for loop
            break;

        case "W":
            //col from word width to board width
            //row from 0 to board height
            $newCol = rand(strlen($theWord), $boardData["width"] -1);
            $newRow = rand(0, $boardData["height"]-1);
            //print "west:\tRow: $newRow\tCol: $newCol<br />\n";

            for ($i = 0; $i < strlen($theWord); $i++){
                //check for a legal move
                $boardLetter = $board[$newRow][$newCol - $i];
                $wordLetter = substr($theWord, $i, 1);
                if (($boardLetter == $wordLetter) ||
                    ($boardLetter == ".")){
                    $board[$newRow][$newCol - $i] = $wordLetter;
                } else {
                    $itWorked = FALSE;
                } // end if
            } // end for loop
            break;

        case "S":
            //col from 0 to board width
            //row from 0 to board height - word length
            $newCol = rand(0, $boardData["width"] -1);
            $newRow = rand(0, $boardData["height"]-1 - strlen($theWord));
            //print "south:\tRow: $newRow\tCol: $newCol<br />\n";

            for ($i = 0; $i < strlen($theWord); $i++){
                //check for a legal move
                $boardLetter = $board[$newRow + $i][$newCol];
                $wordLetter = substr($theWord, $i, 1);
                if (($boardLetter == $wordLetter) ||
                    ($boardLetter == ".")){
                    $board[$newRow + $i][$newCol] = $wordLetter;
                } else {
                    $itWorked = FALSE;
                } // end if
            } // end for loop
            break;

        case "N":
            //col from 0 to board width
            //row from word length to board height
            $newCol = rand(0, $boardData["width"] -1);
            $newRow = rand(strlen($theWord), $boardData["height"]-1);

            for ($i = 0; $i < strlen($theWord); $i++){
                //check for a legal move
                $boardLetter = $board[$newRow - $i][$newCol];
                $wordLetter = substr($theWord, $i, 1);
                if (($boardLetter == $wordLetter) ||
                    ($boardLetter == ".")){
                    $board[$newRow - $i][$newCol] = $wordLetter;
                } else {
                    $itWorked = FALSE;
                } // end if
            } // end for loop
            break;

    } // end switch
    return $itWorked;
} // end addWord


function makeBoard($theBoard){
    //given a board array, return an HTML table based on the array
    global $boardData;
    $puzzle = "";
    $puzzle .= "<table border = 0>\n";
    //check logic here
    for ($row = 0; $row < $boardData["height"]; $row++){
        $puzzle .= "<tr>\n";
        for ($col = 0; $col < $boardData["width"]; $col++){
            $puzzle .= "  <td width = 15>{$theBoard[$row][$col]}</td>\n";
        } // end col for loop
        $puzzle .= "</tr>\n";
    } // end row for loop
    $puzzle .= "</table>\n";
    return $puzzle;
} // end printBoard;

function addFoils(){
    //add random dummy characters to board
    global $board, $boardData;
    for ($row = 0; $row < $boardData["height"]; $row++){
        for ($col = 0; $col < $boardData["width"]; $col++){
            if ($board[$row][$col] == "."){
                $newLetter = rand(65, 90);
                $board[$row][$col] = chr($newLetter);
            } // end if
        } // end col for loop
    } // end row for loop
} // end addFoils

function printPuzzle(){
    //print out page to user with puzzle on it

    global $puzzle, $word, $keyPuzzle, $boardData;
    //print puzzle itself

    print <<<HERE
  <center>
  <h1>{$_SESSION['puzzlename']}</h1>
  $puzzle
  <h3>Word List</h3>
  <table border = 0>
HERE;

    //print word list
    foreach ($word as $theWord){
        print "<tr><td>$theWord</td></tr>\n";
    } // end foreach
    print "</table>\n";
    $puzzleName = $_SESSION['puzzlename'];

    //print form for requesting answer key.
    //send answer key to that form (sneaky!)

    echo "<br /><br /><br /><br /><br /><br /><br /><br />";
    echo '<form action = "wordFindKey.php" method = "post">';
    echo '<input type = "hidden" name = "key" value = "$keyPuzzle">';
    echo '<input type = "hidden" name = "puzzleName" value = "$puzzleName">';
    echo '<input type = "submit" value = "Show Answer Key">';
    echo '</form></center>';
} // end printPuzzle

?>
</body>
</html>

