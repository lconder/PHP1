<?php

/**
 * PHP version 7.4
 * src/scripts/list_results.php
 *
 * @category Scripts
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://www.etsisi.upm.es/ ETS de Ingeniería de Sistemas Informáticos
 */

require dirname(__DIR__, 3) . '/vendor/autoload.php';

use MiW\Results\Entity\Result;
use MiW\Results\Utility\Utils;

Utils::loadEnv(dirname(__DIR__, 3));

if ($argc == 1) {
    $fich = basename(__FILE__);
    echo <<< MARCA_FIN

    Usage: $fich <UserId>

    MARCA_FIN;
    exit(0);
}

$entityManager = Utils::getEntityManager();

$resultsRepository = $entityManager->getRepository(Result::class);
$results = $resultsRepository->findBy(['user' => $argv[1]]);

if (in_array('--json', $argv, true)) {
    echo json_encode($results, JSON_PRETTY_PRINT);

} else  {
    echo PHP_EOL
        . sprintf('%3s - %3s - %22s - %s', 'Id', 'res', 'username', 'time')
        . PHP_EOL;
    $items = 0;
    /* @var Result $result */
    foreach ($results as $result) {
        echo  $result . PHP_EOL;
        $items++;
    }
    echo PHP_EOL . "Total: $items results.";
}
