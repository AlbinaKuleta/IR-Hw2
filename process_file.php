<?php
session_start();

$index = [];
$totalWords = 0;

if (isset($_FILES["filesToUpload"])) {
    foreach ($_FILES["filesToUpload"]["tmp_name"] as $key => $tmpName) {
        if ($_FILES["filesToUpload"]["error"][$key] == 0) {
            $fileContent = file_get_contents($tmpName);
            $words = preg_split('/[\s,.;]+/', strtolower($fileContent), -1, PREG_SPLIT_NO_EMPTY);

            foreach ($words as $position => $word) {
                $word = trim($word, ".,!?\"'");
                if (!isset($index[$word])) {
                    $index[$word] = [];
                }
                $index[$word][] = $totalWords + $position + 1;
            }
            $totalWords += count($words);
        }
    }

    $evenWords = [];
    $oddWords = [];
    foreach ($index as $word => $positions) {
        if (count($positions) % 2 === 0) {
            $evenWords[$word] = $positions;
        } else {
            $oddWords[$word] = $positions;
        }
    }

    $outputFileName = 'output.txt';
    $outputFile = fopen($outputFileName, 'w');
    foreach ($oddWords as $word => $positions) {
        fwrite($outputFile, "{$word}: " . implode(', ', $positions) . "\n");
    }
    fclose($outputFile);

    $_SESSION['index'] = $index;
    $_SESSION['totalWords'] = $totalWords;

    $resultBoxContent = "<div class='result-box'><h3>Grupet Çifte</h3>";
    foreach ($evenWords as $word => $positions) {
        $resultBoxContent .= "<p>{$word}: " . implode(', ', $positions) . "</p>";
    }
    $resultBoxContent .= "</div>";

    $exportMessage = "<p>Grupet Tekë janë eksportuar në <strong>{$outputFileName}</strong>.</p>";
    $linkMessage = "<p><a href='output.php'>Shiko TF, IDF dhe TF-IDF</a></p>";
} else {
    $errorMessage = "<p>Error uploading file.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inverted Index</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            margin: 50px auto;
            width: 80%;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .result-box {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f8f9fa;
        }
        .search-bar input[type="text"] {
            padding: 10px;
            width: 80%;
            max-width: 500px;
            font-size: 16px;
        }
        .highlight {
            background-color: yellow;
        }
        .add-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .add-button:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        function searchWord() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const resultBox = document.querySelector('.result-box');
            const paragraphs = resultBox.getElementsByTagName('p');

            for (let paragraph of paragraphs) {
                paragraph.innerHTML = paragraph.innerHTML.replace(/<span class="highlight">([^<]*)<\/span>/gi, '$1');
            }

            if (searchInput) {
                for (let paragraph of paragraphs) {
                    const regex = new RegExp(`(${searchInput})`, 'gi');
                    paragraph.innerHTML = paragraph.innerHTML.replace(regex, '<span class="highlight">$1</span>');
                }
            }
        }

        function addWord() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase().trim();
            if (searchInput) {
                const resultBox = document.querySelector('.result-box');
                const paragraphs = resultBox.getElementsByTagName('p');
                let wordExists = false;
                for (let paragraph of paragraphs) {
                    if (paragraph.textContent.toLowerCase().includes(searchInput)) {
                        wordExists = true;
                        break;
                    }
                }
                if (!wordExists) {
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'export_word.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            alert(xhr.responseText);
                        }
                    };
                    xhr.send('word=' + encodeURIComponent(searchInput));
                } else {
                    alert('Word already exists in the file.');
                }
            } else {
                alert('Please enter a word to add.');
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Inverted Index</h2>
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search for a word..." oninput="searchWord()">
            <button class="add-button" onclick="addWord()">Add</button>
        </div>
        <?php
        if (isset($errorMessage)) {
            echo $errorMessage;
        } else {
            echo $resultBoxContent;
            echo $exportMessage;
            echo $linkMessage;
        }
        ?>     
    </div>
</body>
</html>
