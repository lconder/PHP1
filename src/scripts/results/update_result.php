<?php

/**
 * PHP version 7.4
 *
 * @category Scripts
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://www.etsisi.upm.es/ ETS de Ingeniería de Sistemas Informáticos
 */

require dirname(__DIR__, 3) . '/vendor/autoload.php';

use MiW\Results\Entity\Result;
use MiW\Results\Utility\Utils;

// Carga las variables de entorno
Utils::loadEnv(dirname(__DIR__, 3));

$entityManager = Utils::getEntityManager();

$resultRepository = $entityManager->getRepository(Result::class);

if($argc < 3 ) {
    $fich = basename(__FILE__);
    echo <<< MARCA_FIN

    Usage: $fich <Id> <Result>

    MARCA_FIN;
    exit(0);
}

$result = $resultRepository->findOneBy(['id' => $argv[1]]);
if(is_null($result)) {
    echo "El resultado con el ID: $argv[1], no se ha encontrado, verifica el ID. ".PHP_EOL;
    return;
}

try {
    $result->setResult($argv[2]);
    $result->setTime(new DateTime('now'));
    $entityManager->flush();
} catch (Throwable $exception) {
    echo $exception->getMessage() . PHP_EOL;
}


if (in_array('--json', $argv, true)) {
    echo json_encode($result, JSON_PRETTY_PRINT);
} else {
    echo PHP_EOL . sprintf(
            '  %2s: %20s %30s' . PHP_EOL,
            'Id', 'Resultado:', 'Tiempo:'
        );

    echo sprintf(
        '- %2d: %20s %30s',
        $result->getId(),
        $result->getResult(),
        $result->getFormattedtime(),
    ),
    PHP_EOL;
}
