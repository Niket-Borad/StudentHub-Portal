<?php

function appendCsvRecord($filePath, $headers, $values)
{
    $handle = fopen($filePath, "c+");

    if ($handle === false || !flock($handle, LOCK_EX)) {
        if ($handle !== false) {
            fclose($handle);
        }
        return false;
    }

    fseek($handle, 0, SEEK_END);
    $fileInfo = fstat($handle);
    $saved = true;

    if ($fileInfo["size"] === 0) {
        $saved = fwrite($handle, "\xEF\xBB\xBF") !== false
            && fputcsv($handle, $headers, ",", "\"", "") !== false;
    }

    if ($saved) {
        foreach ($values as &$value) {
            $value = (string) $value;
            if (preg_match('/^[\t\r\n ]*[=+\-@]/', $value)) {
                $value = "'" . $value;
            }
        }
        unset($value);
        $saved = fputcsv($handle, $values, ",", "\"", "") !== false;
    }

    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);

    return $saved;
}

function renderFormResponse($title, $messages, $isSuccess, $backUrl, $backLabel, $statusCode = null)
{
    http_response_code($statusCode ?? ($isSuccess ? 200 : 422));
    $escape = function ($value) {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
    };
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $escape($title); ?></title>
        <link rel="stylesheet" href="../css/style.css">
    </head>
    <body>
        <main>
            <h1><?php echo $escape($title); ?></h1>
            <?php foreach ($messages as $message): ?>
                <p><?php echo $escape($message); ?></p>
            <?php endforeach; ?>
            <p><a href="<?php echo $escape($backUrl); ?>"><?php echo $escape($backLabel); ?></a></p>
        </main>
    </body>
    </html>
    <?php
}