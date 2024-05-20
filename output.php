<?php
session_start();

if (isset($_SESSION['index']) && isset($_SESSION['totalWords'])) {
    $index = $_SESSION['index'];
    $totalWords = $_SESSION['totalWords'];

    $tf = [];
    foreach ($index as $word => $positions) {
        $tf[$word] = count($positions) / $totalWords;
    }

    $idf = [];
    $totalDocuments = 1;
    foreach ($index as $word => $positions) {
        $idf[$word] = log($totalWords / count($index[$word]), 10);
    }

    $tfidf = [];
    foreach ($index as $word => $positions) {
        $tfidf[$word] = $tf[$word] * $idf[$word];
    }

    echo "<div class='result-box'>";
    echo "<h3>Term Frequencies (TF)</h3>";
    foreach ($tf as $word => $value) {
        echo "<p>{$word}: {$value}</p>";
    }

    echo "<h3>Inverse Document Frequencies (IDF)</h3>";
    foreach ($idf as $word => $value) {
        echo "<p>{$word}: {$value}</p>";
    }

    echo "<h3>TF-IDF</h3>";
    foreach ($tfidf as $word => $value) {
        echo "<p>{$word}: {$value}</p>";
    }
    echo "</div>";
} else {
    echo "<p>No data available. Please upload a file first.</p>";
}
?>
