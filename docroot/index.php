<?php

    require_once __DIR__ . '/libs/silex/vendor/autoload.php';
    $silex = new Silex\Application();
    require_once __DIR__ . '/libs/twig/lib/Twig/Autoloader.php';
    Twig_Autoloader::register();
    $twig = new Twig_Environment(new Twig_Loader_Filesystem(__DIR__ . '/templates'));

    session_start();

    $context = array();
    require_once(__DIR__ . '/inc/config.php');
    require_once(__DIR__ . '/inc/db_connect.php');
    require_once(__DIR__ . '/inc/utilities.php');

    require_once(__DIR__ . '/inc/views.php');


    // FRONTSITE
    $silex->get("/", $ytOld);
    $silex->get("/n/", $ytLatest);
    $silex->get("/latest/", $ytLatest);
    $silex->get("/f/{key}/{value}/", $ytFilter);
    $silex->get("/s/{value}/", $ytSearch);
    $silex->get("/video/{code}/", $ytVideo);

    // ADMIN
    $silex->get("/admin/", $admin);
    $silex->get("/admin/profile/", $adminProfile);
    $silex->post("/admin/profile/", $adminProfileSave);
    $silex->post("/admin/login/", $adminLogin);
    $silex->get("/admin/logout/", $adminLogout);

    // API
    $silex->get('/api/{resource}', $apiList);
    $silex->get('/api/{resource}/{id}', $apiGet);
    $silex->post('/api/{resource}', $apiPost);
    $silex->put('/api/{resource}/{id}', $apiPut);
    $silex->delete('/api/{resource}/{id}', $apiDelete);


    $silex->run();

