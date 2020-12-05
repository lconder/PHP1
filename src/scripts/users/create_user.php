<?php

require dirname(__DIR__, 3) . '/vendor/autoload.php';

use MiW\Results\Entity\User;
use MiW\Results\Utility\Utils;

Utils::loadEnv(dirname(__DIR__, 3));

$entityManager = Utils::getEntityManager();

if($argc < 4 || $argc > 5 || !validate($argv)) {
    $fich = basename(__FILE__);
    echo <<< MARCA_FIN

    Usage: $fich <username> <email> <password> [<enabled>]

    MARCA_FIN;
    exit(0);
}


try {
    $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $argv[2]]);
    if ($user) {
        echo "Este email ya se encuentra registrado ".PHP_EOL;
        return;
    }

    $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $argv[1]]);
    if ($user) {
        echo "Este username ya se encuentra registrado ".PHP_EOL;
        return;
    }

    $user = new User();
    $user->setUsername($argv[1]);
    $user->setEmail($argv[2]);
    $user->setPassword($argv[3]);
    $user->setEnabled($argv[3] ?? false);
    $user->setIsAdmin(false);

    $entityManager->persist($user);
    $entityManager->flush();
    echo 'Usuario creado con ID #' . $user->getId() . PHP_EOL;
} catch (Throwable $exception) {
    echo $exception->getMessage() . PHP_EOL;
}


function validate($argsv) {
    $username = $argsv[1] ?? '';
    $email = $argsv[2] ?? '';
    $password = $argsv[3] ?? '';
    return strlen($username) > 0
        && strlen($password) > 0
        && filter_var($email, FILTER_VALIDATE_EMAIL);
}