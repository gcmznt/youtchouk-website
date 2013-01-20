<?php

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;



    $ytOld = function () use ($silex, $twig, $context, $utils) {
        include 'index_old.php';
        die();
    };

    $ytLatest = function () use ($silex, $twig, $context, $utils) {
        $context['videos'] = $utils['latestVideos'](20);
        $context['latest'] = $utils['latestVideos'](5);
        $context['mostViewed'] = $utils['mostViewedVideos'](5);
        $page = $twig->render("video-list.html", $context);
        return new Response($page, 200);
    };

    $ytFilter = function ($key, $value) use ($silex, $twig, $context, $utils) {
        $value = str_replace('-', ' ', $value);

        $context['title'] = $value;
        $context['videos'] = $utils['filterVideos']($key, $value);
        $context['latest'] = $utils['latestVideos'](5);
        $context['mostViewed'] = $utils['mostViewedVideos'](5);
        $page = $twig->render("video-list.html", $context);
        return new Response($page, 200);
    };

    $ytSearch = function ($value) use ($silex, $twig, $context, $utils) {
        $value = str_replace('-', ' ', $value);

        $context['title'] = '<small>Search results for</small> ' . $value;
        $context['videos'] = $utils['searchVideos']($value);
        $context['latest'] = $utils['latestVideos'](5);
        $context['mostViewed'] = $utils['mostViewedVideos'](5);
        $page = $twig->render("video-list.html", $context);
        return new Response($page, 200);
    };

    $ytVideo = function ($code) use ($silex, $twig, $context, $utils) {
        $context['video'] = $utils['getVideo']($code);
        $context['video']['description'] = nl2br($context['video']['description']);
        $utils['viewVideo']($code);
        $context['latest'] = $utils['latestVideos'](5);
        $context['mostViewed'] = $utils['mostViewedVideos'](5);
        if ($context['video'] === false) {
            $page = $twig->render("video-error.html", $context);
            return new Response($page, 404);
        } else {
            $page = $twig->render("video-page.html", $context);
            return new Response($page, 200);
        }
    };


    $admin = function () use ($silex, $twig, $context) {
        if (isset($_SESSION['user']) && $_SESSION['user']) {
            $context['user'] = $_SESSION['user'];
            $twig->display("admin/admin.html", $context);
        } else {
            $twig->display("admin/login.html", $context);
        }
    };


    $adminLogin = function () use ($silex, $twig, $context) {
        $res = array();
        $res['logged'] = false;

        $email = mysql_real_escape_string($_POST['email']);
        $password = md5($_POST['password']);

        $q = mysql_query("SELECT * FROM users WHERE email = '$email' AND password = '$password'");
        if (mysql_num_rows($q) > 0) {
            $c = mysql_fetch_assoc($q);
            $_SESSION['user'] = $c;
            $_SESSION['user']['gravatar'] = 'http://www.gravatar.com/avatar/' . md5(strtolower(trim($_SESSION['user']['email']))) . '?d=identicon';
            $res['logged'] = true;
        }

        return new Response(json_encode($res), 200, array('Content-Type' => 'application/json'));
    };


    $adminProfile = function () use ($silex, $twig, $context) {
        $context['user'] = $_SESSION['user'];
        $page = $twig->render("admin/profile.html", $context);
        return new Response($page, 200);
    };


    $adminProfileSave = function () use ($silex, $twig, $context, $utils) { 
        $errors = array();

        $id = $_SESSION['user']['id'];
        $name = mysql_real_escape_string($_POST['name']);
        $email = mysql_real_escape_string($_POST['email']);
        $password = md5($_POST['password']);
        $new_password = md5($_POST['new_password']);

        $q = mysql_query("SELECT * FROM users WHERE id = '$id' AND password = '$password'");
        if (mysql_num_rows($q) == 0) $errors[] = 'Wrong password';
        if (!$utils['validateEmail']($email)) $errors[] = 'Invalid email address';
        if ($_POST['new_password'] != $_POST['new_password_retype']) $errors[] = 'New passwords does not match';

        if (count($errors) == 0) {
            if ($_POST['new_password'] != '')
                $q = mysql_query("UPDATE users SET name = '$name', email = '$email', password = '$new_password' WHERE id = '$id' AND password = '$password'");
            else
                $q = mysql_query("UPDATE users SET name = '$name', email = '$email' WHERE id = '$id' AND password = '$password'");
            $context['success'] = true;
        } else {
            $context['errors'] = $errors;
        }

        $_SESSION['user'] = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE id = '$id'"));
        $_SESSION['user']['gravatar'] = 'http://www.gravatar.com/avatar/' . md5(strtolower(trim($_SESSION['user']['email']))) . '?d=identicon';
        $context['user'] = $_SESSION['user'];
        $twig->display("admin/profile.html", $context);
    };



    $adminLogout = function () use ($silex, $twig) {
        $_SESSION['user'] = false;
        return $silex->redirect('/admin/');
    };




    $apiList = function ($resource) use ($silex) {
        if (isset($_SESSION['user']) && $_SESSION['user']) {
            $res = array();
            $q = mysql_query("SELECT * FROM $resource");
            while ($c = mysql_fetch_assoc($q)) {
                if ($resource == 'video' && $c['code'] == '') {
                    mysql_query("DELETE FROM $resource WHERE id = '" . $c['id'] . "'");
                } else {
                    $res[] = $c;
                }
            }
            return new Response(json_encode($res), 200, array('Content-Type' => 'application/json'));
        }
    };


    $apiGet = function ($resource, $id) use ($silex) {
        if (isset($_SESSION['user']) && $_SESSION['user']) {
            $res = array();
            $q = mysql_query("SELECT * FROM $resource WHERE id = '$id'");
            while ($c = mysql_fetch_assoc($q)) { $res = $c; }
            return new Response(json_encode($res), 200, array('Content-Type' => 'application/json'));
        }
    };


    $apiPost = function ($resource, Request $request) use ($silex) {
        if (isset($_SESSION['user']) && $_SESSION['user']) {
            $data = json_decode($request->getContent(), true);

            if ($resource == 'log') $data['who'] = $_SESSION['user'];

            $query = "INSERT INTO $resource (" . implode(', ', array_keys($data)) . ") VALUES ('" . implode("', '", $data) . "')";
            mysql_query($query);
            $q = mysql_query("SELECT * FROM $resource WHERE id = '" . mysql_insert_id() . "'");
            while ($c = mysql_fetch_assoc($q)) { $res = $c; }
            return new Response(json_encode($res), 200, array('Content-Type' => 'application/json'));
        }
    };


    $apiPut = function ($resource, $id, Request $request) use ($silex) {
        if (isset($_SESSION['user']) && $_SESSION['user']) {
            $data = json_decode($request->getContent(), true);
            foreach ($data as $key => $value) { $data_mod[] = "$key = '$value'"; }
            $query = "UPDATE $resource SET " . implode(', ', $data_mod) . " WHERE id = $id";
            mysql_query($query);
            $q = mysql_query("SELECT * FROM $resource WHERE id = '$id'");
            while ($c = mysql_fetch_assoc($q)) { $res = $c; }
            return new Response(json_encode($res), 200, array('Content-Type' => 'application/json'));
        }
    };


    $apiDelete = function ($resource, $id) use ($silex) {
        if (isset($_SESSION['user']) && $_SESSION['user']) {
            $q = mysql_query("DELETE FROM $resource WHERE id = '$id'");
            return new Response(mysql_affected_rows(), 200);
        }
    };

