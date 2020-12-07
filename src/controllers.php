<?php

/**
 * PHP version 7.4
 * ResultsDoctrine - controllers.php
 *
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://www.etsisi.upm.es/ ETS de Ingeniería de Sistemas Informáticos
 */

use MiW\Results\Entity\Result;
use MiW\Results\Entity\User;
use MiW\Results\Utility\Utils;

function funcionHomePage() {
    global $routes;

    $rutaListado = $routes->get('ruta_user_list')->getPath();
    echo <<< ____MARCA_FIN
    <ul>
        <li><a href="$rutaListado">Listado Usuarios</a></li>
    </ul>
    ____MARCA_FIN;
}

function users(): void {
    try {
        if($_SERVER['REQUEST_METHOD']==='POST') {
            createOrUpdate($_POST);
        }
        $entityManager = Utils::getEntityManager();

        $userRepository = $entityManager->getRepository(User::class);
        $users = $userRepository->findAll();
        $table_content = "";
        foreach ($users as $user) {
            $isAdmin = $user->isAdmin() ? "Si" : "No";
            $isEnabled = $user->isEnabled() ? "Si" : "No";
            $linkToUser = createLink("/users/".$user->getId(), "Editar");
            $linkToResults = createLink("/results/".$user->getId()."/user", "Resultados");
            $table_content .= "<tr>
                            <td>".$user->getUsername()."</td>
                            <td>".$user->getEmail()."</td>
                            <td>".$isAdmin."</td>
                            <td>".$isEnabled."</td>
                            <td>$linkToUser&nbsp;&nbsp;&nbsp;$linkToResults</td>
                          </tr>";
        }
        echo <<< ____MARCA_FIN
        <div>
            <h1>Listado Usuarios</h1>
            <a href="/create">Crear nuevo usuario</a>
            <br><br>
           <table style="border: 1px solid black;">
              <tr>
                <th>User</th>
                <th>Email</th>
                <th>Admin</th>
                <th>Activo</th>
                <th>Opciones</th>
              </tr>
              $table_content
              </table>
        </div>
        ____MARCA_FIN;
    } catch (Throwable $exception) {
        echo $exception->getMessage() . PHP_EOL;
    }
}

function user(string $name) {
    try {
        $entityManager = Utils::getEntityManager();
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['id' => $name]);
        if(is_null($user)) {
            echo load404();
            return;
        }

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                echo get($user);
                break;
            case 'DELETE':
                echo delete();
                break;
            default: echo load404(); break;
        }
        return;
    } catch (Throwable $exception) {
        echo $exception->getMessage() . PHP_EOL;
    }
}

function createOrUpdate($data) {
    try {
        $isEnabled = array_key_exists('enabled', $data) && ($data['enabled'] === 'on');
        $isAdmin = array_key_exists('admin', $data) && ($data['admin'] === 'on');

        $entityManager = Utils::getEntityManager();
        $userRepository = $entityManager->getRepository(User::class);

        if(array_key_exists('id', $data) && strlen($data['id']) > 0 ) {
            $user = $userRepository->findOneBy(['id' => $data['id']]);
        } else {
            $user = new User();
        }
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        // $user->setPassword($data['password']);
        $user->setEnabled($isEnabled);
        $user->setIsAdmin($isAdmin);
        $entityManager->persist($user);
        $entityManager->flush();

        echo 'Salvado';

    } catch (Throwable $exception) {
        echo $exception->getMessage() . PHP_EOL;
    }
}

function createLink($route = "/", $title = "Index") {
    return "<a href='$route'>$title</a>";
}

function get($user) {
    $isAdmin = boolval($user->isAdmin()) ? 'checked' : '';
    $isEnabled = boolval($user->isEnabled()) ? 'checked' : '';

    return "<div>
                <h1>".$user->getUsername()."</h1>
                <form method='post' action='/users'>
                    <label for='username'>Username</label>
                    <input
                        type='text'
                        name='username'
                        id='username'
                        placeholder='Username'
                        value='".$user->getUsername()."'
                        required
                    ><br>
                    <label for='email'>Email</label>
                    <input
                        type='text'
                        name='email'
                        id='email'
                        placeholder='email'
                        value='".$user->getEmail()."'
                        required
                    ><br>
                    <label for='admin'>Admnistrador</label>
                    <input
                        type='checkbox'
                        id='admin'
                        name='admin'
                        ".$isAdmin."
                    ><br>
                    <label for='enabled'>Activo</label>
                    <input
                        type='checkbox'
                        id='enabled'
                        name='enabled'
                        ".$isEnabled."
                     ><br>
                    <input
                        type='hidden'
                        name='id'
                        id='id'
                        value='".$user->getId()."'
                    >
                    <input type='submit' value='Aceptar'>
                </form>
                <a href='/users'>Regresar a Usuarios</a>
            </div>";
}

function create() {
    echo "<div>
                <h1>Crear nuevo usuario</h1>
                <form method='post' action='/users'>
                    <label for='username'>Username</label>
                    <input
                        type='text'
                        name='username'
                        id='username'
                        placeholder='Username'
                        required
                    ><br>
                    <label for='email'>Email</label>
                    <input
                        type='text'
                        name='email'
                        id='email'
                        placeholder='email'
                        required
                    ><br>
                    <label for='password'>Password</label>
                    <input
                        type='password'
                        name='password'
                        id='password'
                        placeholder='password'
                        required
                    ><br>
                    <label for='admin'>Admnistrador</label>
                    <input
                        type='checkbox'
                        id='admin'
                        name='admin'
                    ><br>
                    <label for='enabled'>Activo</label>
                    <input
                        type='checkbox'
                        id='enabled'
                        name='enabled'
                        checked
                     ><br>
                    <input type='submit' value='Aceptar'>
                </form>
                <a href='/users'>Regresar a Usuarios</a>
            </div>";
}

function delete() {
    return '<div></div>';
}


function resultsByUser($name) {

    try {
        $entityManager = Utils::getEntityManager();

        $userRepository = $entityManager->getRepository(Result::class);
        $results = $userRepository->findBy(['user' => $name]);

        $table_content = "";
        foreach ($results as $result) {
            $table_content .= "<tr>
                            <td>".$result->getResult()."</td>
                            <td>".$result->getFormattedTime()."</td>
                          </tr>";
        }
        echo <<< ____MARCA_FIN
        <div>
            <h1>Resultados por usuario</h1>
            <a href="/results/$name">Crear nuevo resultado para este usuario</a>
            <br><br>
           <table style="border: 1px solid black;">
              <tr>
                <th>Resultado</th>
                <th>Tiempo</th>
              </tr>
              $table_content
           </table>
           <a href="/users">Regresar a Usuarios</a>
        </div>
        ____MARCA_FIN;
    } catch (Throwable $exception) {
        echo $exception->getMessage() . PHP_EOL;
    }
}

function formResult($name) {
    echo "<div>
                <h1>Crear nuevo usuario</h1>
                <form method='post' action='/results'>
                    <label for='result'>Resultado</label>
                    <input
                        type='number'
                        name='result'
                        id='result'
                        placeholder='Resultado'
                        required
                    ><br>
                    <input
                        type='hidden'
                        name='user'
                        id='user    '
                        value='".$name."'
                    >
                    <input type='submit' value='Aceptar'>
                </form>
                <a href='/results/$name/user'>Regresar a Resultados</a>
            </div>";
}

function results() {
    $result = $_POST['result'];
    $userId = $_POST['user'];
    $time = new DateTime('now');

    $entityManager = Utils::getEntityManager();

    /** @var User $user */
    $user = $entityManager
        ->getRepository(User::class)
        ->findOneBy(['id' => $userId    ]);
    if (is_null($user)) {
        echo "Usuario $userId no encontrado" . PHP_EOL;
        exit(0);
    }

    $result = new Result($result, $user, $time);

    try {
        $entityManager->persist($result);
        $entityManager->flush();
        resultsByUser($userId);
        echo 'Resultado creado con ID ' . $result->getId()
            . ' USER: ' . $user->getUsername() . PHP_EOL;
    } catch (Throwable $exception) {
        echo $exception->getMessage();
    }

}

function load404() {
    return '<div>
                <p>Página no encontrada</p>
            </div>';
}