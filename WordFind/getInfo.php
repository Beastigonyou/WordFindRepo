<html>
<head>
    <title>Word Puzzle Maker</title>
    <style type = "text/css">
        body{
            background: white;
            color: black;
        }
    </style>
</head>
<body>
<?php
// Incluce the functions to make the board
include("functions.php");

if((empty($_GET['puzzlename']))||(empty($_GET['puzzlewords']))){
    echo "<script type=\"text/javascript\">
            alert('Missing Puzzle Name or Word Bank Please Re-enter -- Redirecting to previous page');
            </script>";
    header("refresh:2;url=index.html");
    die(0);
}
session_start();
$_SESSION['puzzlename'] = $_GET['puzzlename'];
global $answerPuzzle;
// WORD PUZZLE MAKER
// Generates a word search puzzle based on a word list
// entered by user. User can also specify the size of
// the puzzle and print out an answer key if desired
global $wordList;
$wordList= $_GET['puzzlewords'];
$wordList = strtoupper($wordList);
//get puzzle data from HTML form
$currentWord = explode("\n", $wordList);


$rawWordList = array();

foreach ($currentWord as $singleWord){
    //take out trailing newline characters
    $singleWord = rtrim($singleWord);
    $singleWord = strtoupper($singleWord);
    // Remove double instances of words
    if(!in_array($singleWord,$rawWordList)){
        array_push($rawWordList,$singleWord);
    }

}


global $currentWord;

$currentWord = $rawWordList;
//check for a word list
if (empty($currentWord)){
    //make default puzzle
    print "Sorry, no data found";
} else {
    $width = $_GET["width"];
    $_SESSION['width'] = $width;
    $height = $_GET["height"];
    $_SESSION['height'] = $height;

    $boardData = array(
        "width" => $width,
        "height" => $height,
        "puzzlewords" => $currentWord

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

//?>
</body>
</html>

