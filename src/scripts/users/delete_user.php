<?php

/**
 * PHP version 7.4
 * src/scripts/list_users.php
 *
 * @category Scripts
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://www.etsisi.upm.es/ ETS de Ingeniería de Sistemas Informáticos
 */

require dirname(__DIR__, 3) . '/vendor/autoload.php';

use MiW\Results\Entity\User;
use MiW\Results\Utility\Utils;

// Carga las variables de entorno
Utils::loadEnv(dirname(__DIR__, 3));

$entityManager = Utils::getEntityManager();

$userRepository = $entityManager->getRepository(User::class);

if($argc < 2) {
    echo 'Por favor ingresa el ID del usuario a buscar como parametro '.PHP_EOL;
    return;
}

$user = $userRepository->findOneBy(['id' => $argv[1]]);
if(is_null($user)) {
    echo "El usuario con el ID: $argv[1], no se ha encontrado, verifica el ID. ".PHP_EOL;
    return;
}

if (in_array('--json', $argv, true)) {
    echo json_encode($user, JSON_PRETTY_PRINT);
} else {
    echo 'Se eliminará el usuario: ' .PHP_EOL;
    echo PHP_EOL . sprintf(
            '  %2s: %20s %30s %7s' . PHP_EOL,
            'Id', 'Username:', 'Email:', 'Enabled:'
        );

    echo sprintf(
        '- %2d: %20s %30s %7s',
        $user->getId(),
        $user->getUsername(),
        $user->getEmail(),
        ($user->isEnabled()) ? 'true' : 'false'
    ),
    PHP_EOL;
}

try {
    $entityManager->remove($user);
    $entityManager->flush();
    echo 'Usuario eliminado!' .PHP_EOL;
} catch (Throwable $exception) {
    echo $exception->getMessage() . PHP_EOL;
}