<?php
if (isset($_POST['word'])) {
    $word = trim($_POST['word']);
    $outputFileName = 'output.txt'; // Emri i file për output

    // Shkruaj fjalën e re në file të jashtëm
    $outputFile = fopen($outputFileName, 'a');
    fwrite($outputFile, "{$word}\n");
    fclose($outputFile);

    // Kthe një mesazh suksesi
    echo "Word '{$word}' has been added to {$outputFileName}.";
} else {
    echo "No word received.";
}



?>
