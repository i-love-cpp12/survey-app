<?php
declare(strict_types=1);

function setToken(PDO $pdo, int $maxAmountOfTryes = 20): string
{
    if(isset($_COOKIE["token"])) return $_COOKIE["token"];

    $tokenList = $pdo->query("SELECT DISTINCT user_token FROM vote;")->fetchAll(PDO::FETCH_COLUMN);
    $generatedToken = bin2hex(random_bytes(32));

    for($i = 0; in_array($generatedToken, $tokenList); ++$i)
    {
        if($i > $maxAmountOfTryes)
            return "";
        $generatedToken = bin2hex(random_bytes(32));
    }
    if(setcookie("token", $generatedToken, time() + 60 * 60 * 24 * 365 * 20, "../backend"))
        return $generatedToken;
    return "";
}