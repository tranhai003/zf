<?php
    date_default_timezone_set('Asia/Bangkok');
	
    set_include_path(get_include_path() . PATH_SEPARATOR . "./include");
    
    require_once 'Zend/Loader/Autoloader.php'; 
    $loader = Zend_Loader_Autoloader::getInstance();
    $loader->registerNamespace(	get_include_path() . PATH_SEPARATOR . "./include" );
    $loader->setFallbackAutoloader(true);

    // load the application configuration
    $config = new Zend_Config_Ini('settings.ini', 'production');
    Zend_Registry::set('config', $config);
    

    // create the application logger
    $logger = new Zend_Log(new Zend_Log_Writer_Stream($config->logging->file));
    Zend_Registry::set('logger', $logger);


    // connect to the database
    $params = array('host'     => $config->database->hostname,
                    'username' => $config->database->username,
                    'password' => $config->database->password,
                    'dbname'   => $config->database->database);

    $db = Zend_Db::factory($config->database->type, $params);
    Zend_Registry::set('db', $db);


    // handle the user request
    $controller = Zend_Controller_Front::getInstance();
    $controller->setControllerDirectory($config->paths->base .
                                        '/include/Controllers');

    // setup the view renderer
    $vr = new Zend_Controller_Action_Helper_ViewRenderer();
    $vr->setView(new Templater());
    $vr->setViewSuffix('tpl');
    Zend_Controller_Action_HelperBroker::addHelper($vr);

    $controller->dispatch();
?>