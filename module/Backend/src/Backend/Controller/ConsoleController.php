<?php

namespace Backend\Controller;

use My\General,
    My\Controller\MyController,
    Sunra\PhpSimple\HtmlDomParser,
    Zend\Dom\Query;

class ConsoleController extends MyController
{
    protected static $_start_date = '2017-03-07';

    protected static $_arr_worker = [
        'content',
        'logs',
        'category',
        'user',
        'general',
        'keyword',
        'group',
        'permission',
        'crawler',
        'tag',
        'admin-process',
        'content-view'
    ];

    public function __construct()
    {
        if (PHP_SAPI !== 'cli') {
            die('Only use this controller from command line!');
        }
        ini_set('default_socket_timeout', -1);
        ini_set('max_execution_time', -1);
        ini_set('mysql.connect_timeout', -1);
        ini_set('memory_limit', -1);
        ini_set('output_buffering', 0);
        ini_set('zlib.output_compression', 0);
        ini_set('implicit_flush', 1);
    }

    public function indexAction()
    {
        die();
    }

    private function flush()
    {
        ob_end_flush();
        ob_flush();
        flush();
    }

    public function migrateAction()
    {
        $params = $this->request->getParams();
        $intIsCreateIndex = (int)$params['createindex'];

        if (empty($params['type'])) {
            return General::getColoredString("Unknown type \n", 'light_cyan', 'red');
        }

        switch ($params['type']) {
            case 'logs':
                $this->__migrateLogs($intIsCreateIndex);
                break;
            case 'content':
                $this->__migrateContent($intIsCreateIndex);
                break;
            case 'category' :
                $this->__migrateCategory($intIsCreateIndex);
                break;
            case 'user' :
                $this->__migrateUser($intIsCreateIndex);
                break;
            case 'general' :
                $this->__migrateGeneral($intIsCreateIndex);
                break;
            case 'keyword' :
                $this->__migrateKeyword($intIsCreateIndex);
                break;
            case 'group' :
                $this->__migrateGroup($intIsCreateIndex);
                break;
            case 'permission' :
                $this->__migratePermission($intIsCreateIndex);
                break;
            case 'tag' :
                $this->__migrateTag($intIsCreateIndex);
                break;
            case 'content-view' :
                $this->__migrateContentView($intIsCreateIndex);
                break;
            case 'all-table' :
                $instanceSearch = new \My\Search\Logs();
                $instanceSearch->createIndex();
                $instanceSearch = new \My\Search\Content();
                $instanceSearch->createIndex();
                $instanceSearch = new \My\Search\Category();
                $instanceSearch->createIndex();
                $instanceSearch = new \My\Search\User();
                $instanceSearch->createIndex();
                $instanceSearch = new \My\Search\Keyword();
                $instanceSearch->createIndex();
                $instanceSearch = new \My\Search\GeneralSearch();
                $instanceSearch->createIndex();
                $instanceSearch = new \My\Search\Group();
                $instanceSearch->createIndex();
                $instanceSearch = new \My\Search\Permission();
                $instanceSearch->createIndex();
                $instanceSearch = new \My\Search\Tag();
                $instanceSearch->createIndex();
                $instanceSearch = new \My\Search\ContentView();
                $instanceSearch->createIndex();
                break;
        }
        echo General::getColoredString("Index ES sucess", 'light_cyan', 'yellow');
        return true;
    }

    public function __migrateContentView($intIsCreateIndex)
    {
        $service = $this->serviceLocator->get('My\Models\ContentView');
        $intLimit = 1000;
        $instanceSearch = new \My\Search\ContentView();
//        $instanceSearch->createIndex();
//        die();

        for ($intPage = 1; $intPage < 10000; $intPage++) {
            $arrList = $service->getListLimit([], $intPage, $intLimit, 'id ASC');

            if (empty($arrList)) {
                break;
            }

            if ($intPage == 1) {
                if ($intIsCreateIndex) {
                    $instanceSearch->createIndex();
                } else {
                    $result = $instanceSearch->removeAllDoc();
                    if (empty($result)) {
                        $this->flush();
                        return General::getColoredString("Cannot delete old search index \n", 'light_cyan', 'red');
                    }
                }
            }
            $arrDocument = [];
            foreach ($arrList as $arr) {
                $id = (int)$arr['id'];
                $arrDocument[] = new \Elastica\Document($id, $arr);
                echo General::getColoredString("Created new document with id = " . $id . " Successfully", 'cyan');

                $this->flush();
            }
            echo '<pre>';
            print_r($arrDocument);
            echo '</pre>';
            die();

            unset($arrList); //release memory
            echo General::getColoredString("Migrating " . count($arrDocument) . " documents, please wait...", 'yellow');
            $this->flush();

            $instanceSearch->add($arrDocument);
            echo General::getColoredString("Migrated " . count($arrDocument) . " documents successfully", 'blue', 'cyan');

            unset($arrDocument);
            $this->flush();
        }

        die('done');
    }

    public function __migrateTag($intIsCreateIndex)
    {
        $service = $this->serviceLocator->get('My\Models\Tags');
        $intLimit = 1000;
        $instanceSearch = new \My\Search\Tag();

        for ($intPage = 1; $intPage < 10000; $intPage++) {
            $arrList = $service->getListLimit([], $intPage, $intLimit, 'tag_id ASC');

            if (empty($arrList)) {
                break;
            }

            if ($intPage == 1) {
                if ($intIsCreateIndex) {
                    $instanceSearch->createIndex();
                } else {
                    $result = $instanceSearch->removeAllDoc();
                    if (empty($result)) {
                        $this->flush();
                        return General::getColoredString("Cannot delete old search index \n", 'light_cyan', 'red');
                    }
                }
            }
            $arrDocument = [];
            foreach ($arrList as $arr) {
                $id = (int)$arr['tag_id'];

                $arrDocument[] = new \Elastica\Document($id, $arr);
                echo General::getColoredString("Created new document with id = " . $id . " Successfully", 'cyan');

                $this->flush();
            }

            unset($arrList); //release memory
            echo General::getColoredString("Migrating " . count($arrDocument) . " documents, please wait...", 'yellow');
            $this->flush();

            $instanceSearch->add($arrDocument);
            echo General::getColoredString("Migrated " . count($arrDocument) . " documents successfully", 'blue', 'cyan');

            unset($arrDocument);
            $this->flush();
        }

        die('done');
    }

    public function __migratePermission($intIsCreateIndex)
    {
        $service = $this->serviceLocator->get('My\Models\Permission');
        $intLimit = 1000;
        $instanceSearch = new \My\Search\Permission();

        for ($intPage = 1; $intPage < 10000; $intPage++) {
            $arrList = $service->getListLimit([], $intPage, $intLimit, 'perm_id ASC');

            if (empty($arrList)) {
                break;
            }

            if ($intPage == 1) {
                if ($intIsCreateIndex) {
                    $instanceSearch->createIndex();
                } else {
                    $result = $instanceSearch->removeAllDoc();
                    if (empty($result)) {
                        $this->flush();
                        return General::getColoredString("Cannot delete old search index \n", 'light_cyan', 'red');
                    }
                }
            }
            $arrDocument = [];
            foreach ($arrList as $arr) {
                $id = (int)$arr['perm_id'];

                $arrDocument[] = new \Elastica\Document($id, $arr);
                echo General::getColoredString("Created new document with id = " . $id . " Successfully", 'cyan');

                $this->flush();
            }

            unset($arrList); //release memory
            echo General::getColoredString("Migrating " . count($arrDocument) . " documents, please wait...", 'yellow');
            $this->flush();

            $instanceSearch->add($arrDocument);
            echo General::getColoredString("Migrated " . count($arrDocument) . " documents successfully", 'blue', 'cyan');

            unset($arrDocument);
            $this->flush();
        }

        die('done');
    }

    public function __migrateGroup($intIsCreateIndex)
    {
        $service = $this->serviceLocator->get('My\Models\Group');
        $intLimit = 1000;
        $instanceSearch = new \My\Search\Group();

        for ($intPage = 1; $intPage < 10000; $intPage++) {
            $arrList = $service->getListLimit([], $intPage, $intLimit, 'group_id ASC');

            if (empty($arrList)) {
                break;
            }

            if ($intPage == 1) {
                if ($intIsCreateIndex) {
                    $instanceSearch->createIndex();
                } else {
                    $result = $instanceSearch->removeAllDoc();
                    if (empty($result)) {
                        $this->flush();
                        return General::getColoredString("Cannot delete old search index \n", 'light_cyan', 'red');
                    }
                }
            }
            $arrDocument = [];
            foreach ($arrList as $arr) {
                $id = (int)$arr['group_id'];

                $arrDocument[] = new \Elastica\Document($id, $arr);
                echo General::getColoredString("Created new document with id = " . $id . " Successfully", 'cyan');

                $this->flush();
            }

            unset($arrList); //release memory
            echo General::getColoredString("Migrating " . count($arrDocument) . " documents, please wait...", 'yellow');
            $this->flush();

            $instanceSearch->add($arrDocument);
            echo General::getColoredString("Migrated " . count($arrDocument) . " documents successfully", 'blue', 'cyan');

            unset($arrDocument);
            $this->flush();
        }

        die('done');
    }

    public function __migrateGeneral($intIsCreateIndex)
    {
        $service = $this->serviceLocator->get('My\Models\GeneralBqn');
        $intLimit = 1000;
        $instanceSearch = new \My\Search\GeneralSearch();

        for ($intPage = 1; $intPage < 10000; $intPage++) {
            $arrList = $service->getListLimit([], $intPage, $intLimit, 'gene_id ASC');

            if (empty($arrList)) {
                break;
            }

            if ($intPage == 1) {
                if ($intIsCreateIndex) {
                    $instanceSearch->createIndex();
                } else {
                    $result = $instanceSearch->removeAllDoc();
                    if (empty($result)) {
                        $this->flush();
                        return General::getColoredString("Cannot delete old search index \n", 'light_cyan', 'red');
                    }
                }
            }
            $arrDocument = [];
            foreach ($arrList as $arr) {
                $id = (int)$arr['gene_id'];

                $arrDocument[] = new \Elastica\Document($id, $arr);
                echo General::getColoredString("Created new document with id = " . $id . " Successfully", 'cyan');

                $this->flush();
            }

            unset($arrList); //release memory
            echo General::getColoredString("Migrating " . count($arrDocument) . " documents, please wait...", 'yellow');
            $this->flush();

            $instanceSearch->add($arrDocument);
            echo General::getColoredString("Migrated " . count($arrDocument) . " documents successfully", 'blue', 'cyan');

            unset($arrDocument);
            $this->flush();
        }

        die('done');
    }

    public function __migrateUser($intIsCreateIndex)
    {
        $service = $this->serviceLocator->get('My\Models\User');
        $intLimit = 1000;
        $instanceSearch = new \My\Search\User();

        for ($intPage = 1; $intPage < 10000; $intPage++) {
            $arrList = $service->getListLimit([], $intPage, $intLimit, 'user_id ASC');

            if (empty($arrList)) {
                break;
            }

            if ($intPage == 1) {
                if ($intIsCreateIndex) {
                    $instanceSearch->createIndex();
                } else {
                    $result = $instanceSearch->removeAllDoc();
                    if (empty($result)) {
                        $this->flush();
                        return General::getColoredString("Cannot delete old search index \n", 'light_cyan', 'red');
                    }
                }
            }
            $arrDocument = [];
            foreach ($arrList as $arr) {
                $id = (int)$arr['user_id'];

                $arrDocument[] = new \Elastica\Document($id, $arr);
                echo General::getColoredString("Created new document with id = " . $id . " Successfully", 'cyan');

                $this->flush();
            }

            unset($arrList); //release memory
            echo General::getColoredString("Migrating " . count($arrDocument) . " documents, please wait...", 'yellow');
            $this->flush();

            $instanceSearch->add($arrDocument);
            echo General::getColoredString("Migrated " . count($arrDocument) . " documents successfully", 'blue', 'cyan');

            unset($arrDocument);
            $this->flush();
        }

        die('done');
    }

    public function __migrateCategory($intIsCreateIndex)
    {
        $service = $this->serviceLocator->get('My\Models\Category');
        $intLimit = 1000;
        $instanceSearch = new \My\Search\Category();
//        $instanceSearch->createIndex();
//        die();
        for ($intPage = 1; $intPage < 10000; $intPage++) {
            $arrList = $service->getListLimit([], $intPage, $intLimit, 'cate_id ASC');
            if (empty($arrList)) {
                break;
            }

            if ($intPage == 1) {
                if ($intIsCreateIndex) {
                    $instanceSearch->createIndex();
                } else {
                    $result = $instanceSearch->removeAllDoc();
                    if (empty($result)) {
                        $this->flush();
                        return General::getColoredString("Cannot delete old search index \n", 'light_cyan', 'red');
                    }
                }
            }
            $arrDocument = [];
            foreach ($arrList as $arr) {
                $id = (int)$arr['cate_id'];

                $arrDocument[] = new \Elastica\Document($id, $arr);
                echo General::getColoredString("Created new document with id = " . $id . " Successfully", 'cyan');

                $this->flush();
            }

            unset($arrList); //release memory
            echo General::getColoredString("Migrating " . count($arrDocument) . " documents, please wait...", 'yellow');
            $this->flush();

            $instanceSearch->add($arrDocument);
            echo General::getColoredString("Migrated " . count($arrDocument) . " documents successfully", 'blue', 'cyan');

            unset($arrDocument);
            $this->flush();
        }

        die('done');
    }

    public function __migrateLogs($intIsCreateIndex)
    {
        $serviceLogs = $this->serviceLocator->get('My\Models\Logs');
        $intLimit = 1000;
        $instanceSearchLogs = new \My\Search\Logs();
        for ($intPage = 1; $intPage < 10000; $intPage++) {
            $arrLogsList = $serviceLogs->getListLimit([], $intPage, $intLimit, 'log_id ASC');
            if (empty($arrLogsList)) {
                break;
            }

            if ($intPage == 1) {
                if ($intIsCreateIndex) {
                    $instanceSearchLogs->createIndex();
                } else {
                    $result = $instanceSearchLogs->removeAllDoc();
                    if (empty($result)) {
                        $this->flush();
                        return General::getColoredString("Cannot delete old search index \n", 'light_cyan', 'red');
                    }
                }
            }
            $arrDocument = [];
            foreach ($arrLogsList as $arrLogs) {
                $logId = (int)$arrLogs['log_id'];

                $arrDocument[] = new \Elastica\Document($logId, $arrLogs);
                echo General::getColoredString("Created new document with log_id = " . $logId . " Successfully", 'cyan');

                $this->flush();
            }

            unset($arrLogsList); //release memory
            echo General::getColoredString("Migrating " . count($arrDocument) . " documents, please wait...", 'yellow');
            $this->flush();

            $instanceSearchLogs->add($arrDocument);
            echo General::getColoredString("Migrated " . count($arrDocument) . " documents successfully", 'blue', 'cyan');

            unset($arrDocument);
            $this->flush();
        }

        die('done');
    }

    public function __migrateContent($intIsCreateIndex)
    {
        $serviceContent = $this->serviceLocator->get('My\Models\Content');
        $intLimit = 200;
        $instanceSearchContent = new \My\Search\Content();
//        $instanceSearchContent->createIndex();
//        die();

        for ($intPage = 1; $intPage < 10000; $intPage++) {
            $arrContentList = $serviceContent->getListLimit([], $intPage, $intLimit, 'cont_id ASC');
            if (empty($arrContentList)) {
                break;
            }

            if ($intPage == 1) {
                if ($intIsCreateIndex) {
                    $instanceSearchContent->createIndex();
                } else {
                    $result = $instanceSearchContent->removeAllDoc();
                    if (empty($result)) {
                        $this->flush();
                        return General::getColoredString("Cannot delete old search index \n", 'light_cyan', 'red');
                    }
                }
            }
            $arrDocument = [];
            foreach ($arrContentList as $arrContent) {
                $id = (int)$arrContent['cont_id'];

                $arrDocument[] = new \Elastica\Document($id, $arrContent);
                echo General::getColoredString("Created new document with cont_id = " . $id . " Successfully", 'cyan');

                $this->flush();
            }

            unset($arrContentList); //release memory
            echo General::getColoredString("Migrating " . count($arrDocument) . " documents, please wait...", 'yellow');
            $this->flush();

            $instanceSearchContent->add($arrDocument);
            echo General::getColoredString("Migrated " . count($arrDocument) . " documents successfully", 'blue', 'cyan');

            unset($arrDocument);
            $this->flush();
        }

        die('done');
    }

    public function __migrateKeyword($intIsCreateIndex)
    {
        $serviceKeyword = $this->serviceLocator->get('My\Models\Keyword');
        $intLimit = 2000;
        $instanceSearchKeyword = new \My\Search\Keyword();
        for ($intPage = 1; $intPage < 10000; $intPage++) {
            $arrList = $serviceKeyword->getListLimit([], $intPage, $intLimit, 'key_id ASC');
            if (empty($arrList)) {
                break;
            }

            if ($intPage == 1) {
                if ($intIsCreateIndex) {
                    $instanceSearchKeyword->createIndex();
                } else {
                    $result = $instanceSearchKeyword->removeAllDoc();
                    if (empty($result)) {
                        $this->flush();
                        return General::getColoredString("Cannot delete old search index \n", 'light_cyan', 'red');
                    }
                }
            }
            $arrDocument = [];
            foreach ($arrList as $arr) {
                $id = (int)$arr['key_id'];
                $arrDocument[] = new \Elastica\Document($id, $arr);
                echo General::getColoredString("Created new document with cont_id = " . $id . " Successfully", 'cyan');

                $this->flush();
            }

            unset($arrList); //release memory
            echo General::getColoredString("Migrating " . count($arrDocument) . " documents, please wait...", 'yellow');
            $this->flush();

            $instanceSearchKeyword->add($arrDocument);
            echo General::getColoredString("Migrated " . count($arrDocument) . " documents successfully", 'blue', 'cyan');

            unset($arrDocument);
            $this->flush();
        }

        die('done');
//        $instanceSearchCategory = new \My\Search\Category();
//        $arr_category = $instanceSearchCategory->getList(['cate_status' => 1]);
//        $instanceSearchKeyword = new \My\Search\Keyword();
//        $instanceSearchKeyword->createIndex();
//        die();
//        $content = file_get_contents(PUBLIC_PATH . '/keyword.txt');
//        $content = explode("\n", $content);

        foreach ($arr_category as $category) {

            $isexist = $instanceSearchKeyword->getDetail(['key_slug' => General::getSlug($category['cate_name'])]);

            if ($isexist) {
                continue;
            }

            $arr_data = [
                'key_name' => $category['cate_name'],
                'key_slug' => General::getSlug($category['cate_name'])
            ];

            $serviceKeyword = $this->serviceLocator->get('My\Models\Keyword');
            $int_result = $serviceKeyword->add($arr_data);
            unset($serviceKeyword);
            if ($int_result) {
                echo General::getColoredString("add keyword : {$category['cate_name']} success", 'green');
            } else {
                echo General::getColoredString("add keyword : {$category['cate_name']} error", 'red');
            }
            $this->flush();
        }
        echo General::getColoredString("add keyword complete", 'yellow', 'cyan');
        return true;
    }

    public function workerAction()
    {
        $params = $this->request->getParams();

        //stop all job
        if ($params['stop'] === 'all') {
            if ($params['type'] || $params['background']) {
                return General::getColoredString("Invalid params \n", 'light_cyan', 'red');
            }
            exec("ps -ef | grep -v grep | grep 'type=" . WORKER_PREFIX . "-*' | awk '{ print $2 }'", $PID);

            if (empty($PID)) {
                return General::getColoredString("Cannot found PID \n", 'light_cyan', 'red');
            }

            foreach ($PID as $worker) {
                shell_exec("kill " . $worker);
                echo General::getColoredString("Kill worker with PID = {$worker} stopped running in background \n", 'green');
            }

            return true;
        }

        $arr_worker = self::$_arr_worker;

        if (in_array(trim($params['stop']), $arr_worker)) {
            if ($params['type'] || $params['background']) {
                return General::getColoredString("Invalid params \n", 'light_cyan', 'red');
            }
            $stopWorkerName = WORKER_PREFIX . '-' . trim($params['stop']);
            exec("ps -ef | grep -v grep | grep 'type={$stopWorkerName}' | awk '{ print $2 }'", $PID);
            $PID = current($PID);
            if ($PID) {
                shell_exec("kill " . $PID);
                return General::getColoredString("Job {$stopWorkerName} is stopped running in background \n", 'green');
            } else {
                return General::getColoredString("Cannot found PID \n", 'light_cyan', 'red');
            }
        }

        $worker = General::getWorkerConfig();
        switch ($params['type']) {
            case WORKER_PREFIX . '-logs':
                //start job in background
                if ($params['background'] === 'true') {
                    $PID = shell_exec("nohup php " . PUBLIC_PATH . "/index.php worker --type=" . WORKER_PREFIX . "-logs >/dev/null & echo 2>&1 & echo $!");
                    if (empty($PID)) {
                        echo General::getColoredString("Cannot deamon PHP process to run job " . WORKER_PREFIX . "-logs in background. \n", 'light_cyan', 'red');
                        return;
                    } else {
                        echo General::getColoredString("Job " . WORKER_PREFIX . "-logs is running in background ... \n", 'green');
                    }
                }

                $funcName1 = SEARCH_PREFIX . 'writeLog';
                $methodHandler1 = '\My\Job\JobLog::writeLog';
                $worker->addFunction($funcName1, $methodHandler1, $this->serviceLocator);

                break;

            case WORKER_PREFIX . '-content':
                //start job in background
                if ($params['background'] === 'true') {
                    $PID = shell_exec("nohup php " . PUBLIC_PATH . "/index.php worker --type=" . WORKER_PREFIX . "-content >/dev/null & echo 2>&1 & echo $!");
                    if (empty($PID)) {
                        echo General::getColoredString("Cannot deamon PHP process to run job " . WORKER_PREFIX . "-content in background. \n", 'light_cyan', 'red');
                        return;
                    }
                    echo General::getColoredString("Job " . WORKER_PREFIX . "-content is running in background ... \n", 'green');
                }

                $funcName1 = SEARCH_PREFIX . 'writeContent';
                $methodHandler1 = '\My\Job\JobContent::writeContent';
                $worker->addFunction($funcName1, $methodHandler1, $this->serviceLocator);

                $funcName2 = SEARCH_PREFIX . 'editContent';
                $methodHandler2 = '\My\Job\JobContent::editContent';
                $worker->addFunction($funcName2, $methodHandler2, $this->serviceLocator);

                $funcName3 = SEARCH_PREFIX . 'multiEditContent';
                $methodHandler3 = '\My\Job\JobContent::multiEditContent';
                $worker->addFunction($funcName3, $methodHandler3, $this->serviceLocator);

                break;

            case WORKER_PREFIX . '-category':
                //start job in background
                if ($params['background'] === 'true') {
                    $PID = shell_exec("nohup php " . PUBLIC_PATH . "/index.php worker --type=" . WORKER_PREFIX . "-category >/dev/null & echo 2>&1 & echo $!");
                    if (empty($PID)) {
                        echo General::getColoredString("Cannot deamon PHP process to run job " . WORKER_PREFIX . "-category in background. \n", 'light_cyan', 'red');
                        return;
                    }
                    echo General::getColoredString("Job " . WORKER_PREFIX . "-category is running in background ... \n", 'green');
                }

                $funcName1 = SEARCH_PREFIX . 'writeCategory';
                $methodHandler1 = '\My\Job\JobCategory::writeCategory';
                $worker->addFunction($funcName1, $methodHandler1, $this->serviceLocator);

                $funcName2 = SEARCH_PREFIX . 'editCategory';
                $methodHandler2 = '\My\Job\JobCategory::editCategory';
                $worker->addFunction($funcName2, $methodHandler2, $this->serviceLocator);

                $funcName3 = SEARCH_PREFIX . 'multiEditCategory';
                $methodHandler3 = '\My\Job\JobCategory::multiEditCategory';
                $worker->addFunction($funcName3, $methodHandler3, $this->serviceLocator);

                break;

            case WORKER_PREFIX . '-user':
                //start job in background
                if ($params['background'] === 'true') {
                    $PID = shell_exec("nohup php " . PUBLIC_PATH . "/index.php worker --type=" . WORKER_PREFIX . "-user >/dev/null & echo 2>&1 & echo $!");
                    if (empty($PID)) {
                        echo General::getColoredString("Cannot deamon PHP process to run job " . WORKER_PREFIX . "-user in background. \n", 'light_cyan', 'red');
                        return;
                    }
                    echo General::getColoredString("Job " . WORKER_PREFIX . "-user is running in background ... \n", 'green');
                }

                $funcName1 = SEARCH_PREFIX . 'writeUser';
                $methodHandler1 = '\My\Job\JobUser::writeUser';
                $worker->addFunction($funcName1, $methodHandler1, $this->serviceLocator);

                $funcName2 = SEARCH_PREFIX . 'editUser';
                $methodHandler2 = '\My\Job\JobUser::editUser';
                $worker->addFunction($funcName2, $methodHandler2, $this->serviceLocator);

                $funcName3 = SEARCH_PREFIX . 'multiEditUser';
                $methodHandler3 = '\My\Job\JobUser::multiEditUser';
                $worker->addFunction($funcName3, $methodHandler3, $this->serviceLocator);

                break;

            case WORKER_PREFIX . '-general':
                //start job in background
                if ($params['background'] === 'true') {
                    $PID = shell_exec("nohup php " . PUBLIC_PATH . "/index.php worker --type=" . WORKER_PREFIX . "-general >/dev/null & echo 2>&1 & echo $!");
                    if (empty($PID)) {
                        echo General::getColoredString("Cannot deamon PHP process to run job " . WORKER_PREFIX . "-general in background. \n", 'light_cyan', 'red');
                        return;
                    }
                    echo General::getColoredString("Job " . WORKER_PREFIX . "-general is running in background ... \n", 'green');
                }

                $funcName1 = SEARCH_PREFIX . 'writeGeneral';
                $methodHandler1 = '\My\Job\JobGeneral::writeGeneral';
                $worker->addFunction($funcName1, $methodHandler1, $this->serviceLocator);

                $funcName2 = SEARCH_PREFIX . 'editGeneral';
                $methodHandler2 = '\My\Job\JobGeneral::editGeneral';
                $worker->addFunction($funcName2, $methodHandler2, $this->serviceLocator);

                break;

            case WORKER_PREFIX . '-keyword':
                //start job in background
                if ($params['background'] === 'true') {
                    $PID = shell_exec("nohup php " . PUBLIC_PATH . "/index.php worker --type=" . WORKER_PREFIX . "-keyword >/dev/null & echo 2>&1 & echo $!");
                    if (empty($PID)) {
                        echo General::getColoredString("Cannot deamon PHP process to run job " . WORKER_PREFIX . "-keyword in background. \n", 'light_cyan', 'red');
                        return;
                    }
                    echo General::getColoredString("Job " . WORKER_PREFIX . "-keyword is running in background ... \n", 'green');
                }

                $funcName1 = SEARCH_PREFIX . 'writeKeyword';
                $methodHandler1 = '\My\Job\JobKeyword::writeKeyword';
                $worker->addFunction($funcName1, $methodHandler1, $this->serviceLocator);

                $funcName2 = SEARCH_PREFIX . 'editKeyword';
                $methodHandler2 = '\My\Job\JobKeyword::editKeyword';
                $worker->addFunction($funcName2, $methodHandler2, $this->serviceLocator);

                break;

            case WORKER_PREFIX . '-group':
                //start job group in background
                if ($params['background'] === 'true') {
                    $PID = shell_exec("nohup php " . PUBLIC_PATH . "/index.php worker --type=" . WORKER_PREFIX . "-group >/dev/null & echo 2>&1 & echo $!");
                    if (empty($PID)) {
                        echo General::getColoredString("Cannot deamon PHP process to run job " . WORKER_PREFIX . "-group in background. \n", 'light_cyan', 'red');
                        return;
                    }
                    echo General::getColoredString("Job " . WORKER_PREFIX . "-group is running in background ... \n", 'green');
                }

                $funcName1 = SEARCH_PREFIX . 'writeGroup';
                $methodHandler1 = '\My\Job\JobGroup::writeGroup';
                $worker->addFunction($funcName1, $methodHandler1, $this->serviceLocator);

                $funcName2 = SEARCH_PREFIX . 'editGroup';
                $methodHandler2 = '\My\Job\JobGroup::editGroup';
                $worker->addFunction($funcName2, $methodHandler2, $this->serviceLocator);

                break;

            case WORKER_PREFIX . '-permission':
                //start job group in background
                if ($params['background'] === 'true') {
                    $PID = shell_exec("nohup php " . PUBLIC_PATH . "/index.php worker --type=" . WORKER_PREFIX . "-permission >/dev/null & echo 2>&1 & echo $!");
                    if (empty($PID)) {
                        echo General::getColoredString("Cannot deamon PHP process to run job " . WORKER_PREFIX . "-permission in background. \n", 'light_cyan', 'red');
                        return;
                    }
                    echo General::getColoredString("Job " . WORKER_PREFIX . "-permission is running in background ... \n", 'green');
                }

                $funcName1 = SEARCH_PREFIX . 'writePermission';
                $methodHandler1 = '\My\Job\JobPermission::writePermission';
                $worker->addFunction($funcName1, $methodHandler1, $this->serviceLocator);

                $funcName2 = SEARCH_PREFIX . 'editPermission';
                $methodHandler2 = '\My\Job\JobPermission::editPermission';
                $worker->addFunction($funcName2, $methodHandler2, $this->serviceLocator);

                break;

            case WORKER_PREFIX . '-tag':
                //start job group in background
                if ($params['background'] === 'true') {
                    $PID = shell_exec("nohup php " . PUBLIC_PATH . "/index.php worker --type=" . WORKER_PREFIX . "-tag >/dev/null & echo 2>&1 & echo $!");
                    if (empty($PID)) {
                        echo General::getColoredString("Cannot deamon PHP process to run job " . WORKER_PREFIX . "-tag in background. \n", 'light_cyan', 'red');
                        return;
                    }
                    echo General::getColoredString("Job " . WORKER_PREFIX . "-tag is running in background ... \n", 'green');
                }

                $funcName1 = SEARCH_PREFIX . 'writeTag';
                $methodHandler1 = '\My\Job\JobTag::writeTag';
                $worker->addFunction($funcName1, $methodHandler1, $this->serviceLocator);

                $funcName2 = SEARCH_PREFIX . 'editTag';
                $methodHandler2 = '\My\Job\JobTag::editTag';
                $worker->addFunction($funcName2, $methodHandler2, $this->serviceLocator);

                break;

            case WORKER_PREFIX . '-admin-process':
                //start job group in background
                if ($params['background'] === 'true') {
                    $PID = shell_exec("nohup php " . PUBLIC_PATH . "/index.php worker --type=" . WORKER_PREFIX . "-admin-process >/dev/null & echo 2>&1 & echo $!");
                    if (empty($PID)) {
                        echo General::getColoredString("Cannot deamon PHP process to run job " . WORKER_PREFIX . "-admin-process in background. \n", 'light_cyan', 'red');
                        return;
                    }
                    echo General::getColoredString("Job " . WORKER_PREFIX . "-admin-process is running in background ... \n", 'green');
                }

                $funcName1 = SEARCH_PREFIX . 'updateDataDB';
                $methodHandler1 = '\My\Job\JobAdminProcess::updateDataDB';
                $worker->addFunction($funcName1, $methodHandler1, $this->serviceLocator);
                break;

            case WORKER_PREFIX . '-content-view':
                //start job group in background
                if ($params['background'] === 'true') {
                    $PID = shell_exec("nohup php " . PUBLIC_PATH . "/index.php worker --type=" . WORKER_PREFIX . "-content-view >/dev/null & echo 2>&1 & echo $!");
                    if (empty($PID)) {
                        echo General::getColoredString("Cannot deamon PHP process to run job " . WORKER_PREFIX . "-content-view in background. \n", 'light_cyan', 'red');
                        return;
                    }
                    echo General::getColoredString("Job " . WORKER_PREFIX . "-content-view is running in background ... \n", 'green');
                }

                $funcName1 = SEARCH_PREFIX . 'writeContentView';
                $methodHandler1 = '\My\Job\JobContentView::writeContentView';
                $worker->addFunction($funcName1, $methodHandler1, $this->serviceLocator);

                $funcName2 = SEARCH_PREFIX . 'editContentView';
                $methodHandler2 = '\My\Job\JobContentView::editContentView';
                $worker->addFunction($funcName2, $methodHandler2, $this->serviceLocator);
                break;
            default:
                return General::getColoredString("Invalid or not found function \n", 'light_cyan', 'red');
        }

        if (empty($params['background'])) {
            echo General::getColoredString("Waiting for job...\n", 'green');
        } else {
            return;
        }
        $this->flush();
        while (@$worker->work() || ($worker->returnCode() == GEARMAN_IO_WAIT) || ($worker->returnCode() == GEARMAN_NO_JOBS)) {
            if ($worker->returnCode() != GEARMAN_SUCCESS) {
                echo "return_code: " . $worker->returnCode() . "\n";
                break;
            }
        }
    }

    public function checkWorkerRunningAction()
    {
        $arr_worker = self::$_arr_worker;
        foreach ($arr_worker as $worker) {
            $worker_name = WORKER_PREFIX . '-' . $worker;
            exec("ps -ef | grep -v grep | grep 'type={$worker_name}' | awk '{ print $2 }'", $PID);
            $PID = current($PID);

            if (empty($PID)) {
                $command = 'nohup php ' . PUBLIC_PATH . '/index.php worker --type=' . $worker_name . ' >/dev/null & echo 2>&1 & echo $!';
                $PID = shell_exec($command);
                if (empty($PID)) {
                    echo General::getColoredString("Cannot deamon PHP process to run job {$worker_name} in background. \n", 'light_cyan', 'red');
                } else {
                    echo General::getColoredString("PHP process run job {$worker_name} in background with PID : {$PID}. \n", 'green');
                }
            }
        }
    }

    public function crontabAction()
    {
        $params = $this->request->getParams();

        if (empty($params['type'])) {
            return General::getColoredString("Unknown type or id \n", 'light_cyan', 'red');
        }

        switch ($params['type']) {
        }

        return true;
    }

    public function crawlerKeywordAction()
    {
        $params = $this->request->getParams();
        $pid = $params['pid'];
        if (!empty($pid)) {
            shell_exec('kill -9 ' . $pid);
        }

        $instanceSearchKeyWord = new \My\Search\Keyword();
        $arr_keyword = current($instanceSearchKeyWord->getListLimit(['is_crawler' => 0], 1, 1, ['key_id' => ['order' => 'asc']]));

        if (empty($arr_keyword)) {
            return;
        }

        $serviceKeyword = $this->serviceLocator->get('My\Models\Keyword');
        $serviceKeyword->edit(['is_crawler' => 1, 'updated_date' => time()], $arr_keyword['key_id']);

        $keyword = $arr_keyword['key_name'];
        $url = 'http://www.google.com/complete/search?output=search&client=chrome&q=' . rawurlencode($keyword) . '&hl=en&gl=us';
        $resp = General::crawler($url);
        $arr_resp = json_decode($resp, true)[1];

        exec("ps -ef | grep -v grep | grep crawlerkeyword | awk '{ print $2 }'", $PID);

        if (empty($arr_resp)) {
            return shell_exec('nohup php ' . PUBLIC_PATH . '/index.php crawlerkeyword --pid=' . current($PID) . ' >/dev/null & echo 2>&1 & echo $!');
        }

        foreach ($arr_resp as $key) {
            $is_exits = $instanceSearchKeyWord->getDetail(['key_slug' => General::getSlug($key)]);

            if ($is_exits) {
                echo \My\General::getColoredString("exist {$key}", 'red') . '\n';
                continue;
            }

            sleep(rand(4, 10));
            $url_gg = 'https://www.google.com/search?sclient=psy-ab&biw=1366&bih=315&espv=2&q=' . rawurlencode(General::getSlug($key)) . '&oq=' . rawurlencode(General::getSlug($key));

            $gg_rp = General::crawler($url_gg);
            $gg_rp_dom = HtmlDomParser::str_get_html($gg_rp);
            $key_description = '';
            foreach ($gg_rp_dom->find('.srg .st') as $item) {
                empty($key_description) ?
                    $key_description .= '<p><strong>' . strip_tags($item->outertext) . '</strong></p>' :
                    $key_description .= '<p>' . strip_tags($item->outertext) . '</p>';
            }

            $id_key = $serviceKeyword->add([
                'key_name' => $key,
                'key_slug' => General::getSlug($key),
                'is_crawler' => 0,
                'created_date' => time(),
                'key_description' => $key_description
            ]);

            if ($id_key) {
                echo \My\General::getColoredString("Insert to tbl_keyword success key_name =  {$key} \n", 'green');
            } else {
                echo \My\General::getColoredString("Insert to tbl_keyword ERROR key_name =  {$key} \n", 'red');
            }
            unset($gg_rp, $gg_rp_dom, $key_description, $id_key);
            $this->flush();
        }
        unset($instanceSearchKeyWord);

        return shell_exec('nohup php ' . PUBLIC_PATH . '/index.php crawlerkeyword --pid=' . current($PID) . ' >/dev/null');
    }

    public function getKeyword()
    {
        $instanceSearchKeyWord = new \My\Search\Keyword();
        $arr_keyword = current($instanceSearchKeyWord->getListLimit(['is_crawler' => 0], 1, 1, ['key_id' => ['order' => 'asc']]));

        unset($instanceSearchKeyWord);
        if (empty($arr_keyword)) {
            return;
        }

        $serviceKeyword = $this->serviceLocator->get('My\Models\Keyword');
        $serviceKeyword->edit(['is_crawler' => 1, 'updated_date' => time()], $arr_keyword['key_id']);
        unset($serviceKeyword);

        $keyword = $arr_keyword['key_name'];

        foreach ($match as $key => $value) {
            if ($key == 0) {
                $key_match = $keyword . $value;
                $url = 'http://www.google.com/complete/search?output=search&client=chrome&q=' . rawurlencode($key_match) . '&hl=vi&gl=vn';
                $return = General::crawler($url);
                $this->add_keyword(json_decode($return)[1]);
                continue;
            } else {
                for ($i = 0; $i < 2; $i++) {
                    if ($i == 0) {
                        $key_match = $keyword . ' ' . $value;
                    } else {
                        $key_match = $value . ' ' . $keyword;
                    }
                    $url = 'http://www.google.com/complete/search?output=search&client=chrome&q=' . rawurlencode($key_match) . '&hl=vi&gl=vn';
                    $return = General::crawler($url);
                    $this->add_keyword(json_decode($return)[1]);
                    continue;
                }
            }
            $this->flush();
        };
        $this->flush();
        sleep(3);
        $this->getKeyword();
    }

    public function add_keyword($arr_key)
    {
        if (empty($arr_key)) {
            return false;
        }

        $instanceSearchKeyWord = new \My\Search\Keyword();
        foreach ($arr_key as $key_word) {
            $is_exsit = $instanceSearchKeyWord->getDetail(['key_slug' => trim(General::getSlug($key_word))]);

            if ($is_exsit) {
                continue;
            }

            $arr_data = [
                'key_name' => $key_word,
                'key_slug' => trim(General::getSlug($key_word)),
                'created_date' => time(),
                'is_crawler' => 0
            ];

            $serviceKeyword = $this->serviceLocator->get('My\Models\Keyword');
            $int_result = $serviceKeyword->add($arr_data);
            unset($serviceKeyword);
            if ($int_result) {
                echo \My\General::getColoredString("Insert success 1 row with id = {$int_result}", 'yellow');
            }
            $this->flush();
        }
        unset($instanceSearchKeyWord);
        return true;
    }

    public function sitemapAction()
    {
        unlink(PUBLIC_PATH . '/maps/sitemap.xml');
        $this->sitemapOther();
        $this->siteMapCategory();
        $this->siteMapContent();
        $this->siteMapSearch();
        $this->siteMapTag();

        $xml = '<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>';
        $xml = new \SimpleXMLElement($xml);

        $all_file = scandir(PUBLIC_PATH . '/maps/');
        sort($all_file, SORT_NATURAL | SORT_FLAG_CASE);

        foreach ($all_file as $file_name) {
            if (strpos($file_name, 'xml') !== false) {
                $sitemap = $xml->addChild('sitemap', '');
                $sitemap->addChild('loc', General::SITE_DOMAIN_FULL . '/maps/' . $file_name);
            }
        }

        $result = file_put_contents(PUBLIC_PATH . '/maps/sitemap.xml', $xml->asXML());
        if ($result) {
            echo General::getColoredString("Create sitemap.xml completed!", 'blue', 'cyan');
            $this->flush();
        }
        echo General::getColoredString("DONE!", 'blue', 'cyan');
        return true;
    }

    public function siteMapCategory()
    {
        $doc = '<?xml version="1.0" encoding="UTF-8"?>';
        $doc .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $doc .= '</urlset>';
        $xml = new \SimpleXMLElement($doc);
        $this->flush();
        $instanceSearchCategory = new \My\Search\Category();
        $arrCategoryList = $instanceSearchCategory->getList(['cate_status' => 1], [], ['cate_id' => ['order' => 'asc']]);

        foreach ($arrCategoryList as $value) {
            $strCategoryURL = General::SITE_DOMAIN_FULL . '/cate/' . $value['cate_slug'] . '-' . $value['cate_id'] . '.html';
            $url = $xml->addChild('url');
            $url->addChild('loc', $strCategoryURL);
            $url->addChild('changefreq', 'daily');
        }

        unlink(PUBLIC_PATH . '/maps/category.xml');
        $result = file_put_contents(PUBLIC_PATH . '/maps/category.xml', $xml->asXML());
        if ($result) {
            echo General::getColoredString("Sitemap category done", 'blue', 'cyan');
            $this->flush();
        }

        $this->flush();
        return true;
    }

    public function siteMapContent()
    {
        $instanceSearchContent = new \My\Search\Content();
        $intLimit = 4000;

        $start_date = date_create(self::$_start_date);
        $current_date = date_create(date('Y-m-d'));
        $diff = date_diff($start_date, $current_date);
        $lte_id = $diff->format('%a') * 500;

        for ($intPage = 1; $intPage < 10000; $intPage++) {
            $file = PUBLIC_PATH . '/maps/post-' . $intPage . '.xml';
            $arrContentList = $instanceSearchContent->getListLimit(
                [
                    'not_cont_status' => -1,
                    'lte_cont_id' => $lte_id
                ],
                $intPage,
                $intLimit,
                ['cont_id' => ['order' => 'desc']],
                [
                    'cont_id',
                    'cont_slug'
                ]
            );

            if (empty($arrContentList)) {
                break;
            }

            $doc = '<?xml version="1.0" encoding="UTF-8"?>';
            $doc .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            $doc .= '</urlset>';
            $xml = new \SimpleXMLElement($doc);
            $this->flush();

            foreach ($arrContentList as $arr) {
                $href = General::SITE_DOMAIN_FULL . '/post/' . $arr['cont_slug'] . '-' . $arr['cont_id'] . '.html';
                $url = $xml->addChild('url');
                $url->addChild('loc', $href);
                $url->addChild('changefreq', 'daily');
            }

            unlink($file);
            $result = file_put_contents($file, $xml->asXML());

            if ($result) {
                echo General::getColoredString("Site map complete content page {$intPage}", 'yellow', 'cyan');
                $this->flush();
            }
        }

        return true;
    }

    public function siteMapSearch()
    {
        $instanceSearchKeyword = new \My\Search\Keyword();
        $intLimit = 4000;
        $start_date = date_create(self::$_start_date);
        $current_date = date_create(date('Y-m-d'));
        $diff = date_diff($start_date, $current_date);
        $lte_id = $diff->format('%a') * 500;

        for ($intPage = 1; $intPage < 10000; $intPage++) {
            $file = PUBLIC_PATH . '/maps/keyword-' . $intPage . '.xml';
            $arrKeyList = $instanceSearchKeyword->getListLimit(
                [
                    'full' => 1,
                    'lte_key_id' => $lte_id
                ],
                $intPage,
                $intLimit,
                ['key_id' => ['order' => 'desc']],
                [
                    'key_id',
                    'key_slug'
                ]
            );

            if (empty($arrKeyList)) {
                break;
            }

            $doc = '<?xml version="1.0" encoding="UTF-8"?>';
            $doc .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            $doc .= '</urlset>';
            $xml = new \SimpleXMLElement($doc);
            $this->flush();

            foreach ($arrKeyList as $arr) {
                $href = General::SITE_DOMAIN_FULL . '/keyword/' . $arr['key_slug'] . '-' . $arr['key_id'] . '.html';
                $url = $xml->addChild('url');
                $url->addChild('loc', $href);
                $url->addChild('changefreq', 'daily');
            }

            unlink($file);
            $result = file_put_contents($file, $xml->asXML());

            if ($result) {
                echo General::getColoredString("Site map complete keyword page {$intPage}", 'yellow', 'cyan');
                $this->flush();
            }
            $this->flush();
        }
        return true;
    }

    public function siteMapTag()
    {
        $instanceSearch = new \My\Search\Tag();
        $intLimit = 4000;
        $start_date = date_create(self::$_start_date);
        $current_date = date_create(date('Y-m-d'));
        $diff = date_diff($start_date, $current_date);
        $lte_id = $diff->format('%a') * 1000;

        for ($intPage = 1; $intPage < 10000; $intPage++) {
            $file = PUBLIC_PATH . '/maps/tag-' . $intPage . '.xml';
            $arrTag = $instanceSearch->getListLimit(
                [
                    'full' => 1,
                    'lte_tag_id' => $lte_id
                ],
                $intPage,
                $intLimit,
                ['tag_id' => ['order' => 'desc']],
                [
                    'tag_id',
                    'tag_slug'
                ]
            );

            if (empty($arrTag)) {
                break;
            }

            $doc = '<?xml version="1.0" encoding="UTF-8"?>';
            $doc .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            $doc .= '</urlset>';
            $xml = new \SimpleXMLElement($doc);
            $this->flush();

            foreach ($arrTag as $arr) {
                $href = General::SITE_DOMAIN_FULL . '/tag/' . $arr['tag_slug'] . '-' . $arr['tag_id'] . '.html';
                $url = $xml->addChild('url');
                $url->addChild('loc', $href);
                $url->addChild('changefreq', 'daily');
            }

            unlink($file);
            $result = file_put_contents($file, $xml->asXML());

            if ($result) {
                echo General::getColoredString("Site map complete tag page {$intPage}", 'yellow', 'cyan');
                $this->flush();
            }
            $this->flush();
        }
        return true;
    }

    private function sitemapOther()
    {
        $doc = '<?xml version="1.0" encoding="UTF-8"?>';
        $doc .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $doc .= '</urlset>';
        $xml = new \SimpleXMLElement($doc);
        $this->flush();
        $arrData = ['http://sharevideoclip.com/'];
        foreach ($arrData as $value) {
            $href = $value;
            $url = $xml->addChild('url');
            $url->addChild('loc', $href);
            $url->addChild('changefreq', 'daily');
        }

        unlink(PUBLIC_PATH . '/maps/other.xml');
        $result = file_put_contents(PUBLIC_PATH . '/maps/other.xml', $xml->asXML());
        if ($result) {
            echo General::getColoredString("Sitemap orther done", 'blue', 'cyan');
            $this->flush();
        }
    }

    public function crawlerAction()
    {
        $params = $this->request->getParams();
        $type = $params['type'];
        return true;
    }

    public function keywordHotAction()
    {
        $arr_key = [
            'share video',
            'video',
            'clip',
            'beautyful girl',
            'film',
            'film hot',
            'music',
            'discovery',
            'youtube',
            'share clip',
            'share youtube',
            'video',
            'sport',
            'trends',
            'award',
            'got talent',
            'the face',
            'the voice',
            'best sign',
            'song',
            'sing my song',
            'the voice UK',
            'the voice us',
            'vimeo',
            'c1',
            'next top model',
            'next top model uk',
            'next top model us',
            'justin bieber',
            'gomez',
            'gangnam style',
            'style',
            'hot model',
            'hot youtube',
            'live youtube',
            'discovery',
            'legion',
            'top',
            'top videos',
            'top youtube',
            'discovery',
            'happy new year',
            'share',
            'facebook',
            'viber',
            'linked',
            'top music',
            'top 100',
            'kpop',
            'showbiz'
        ];

        $instanceSearchKeyWord = new \My\Search\Keyword();
        foreach ($arr_key as $name) {
            $isexist = $instanceSearchKeyWord->getDetail(['key_slug' => General::getSlug($name)]);

            if ($isexist) {
                continue;
            }

            sleep(rand(4, 10));
            $url_gg = 'https://www.google.com.vn/search?sclient=psy-ab&biw=1366&bih=315&espv=2&q=' . rawurlencode($name) . '&oq=' . rawurlencode($val);

            $gg_rp = General::crawler($url_gg);
            $gg_rp_dom = HtmlDomParser::str_get_html($gg_rp);
            $key_description = '';
            foreach ($gg_rp_dom->find('.srg .st') as $item) {
                empty($key_description) ?
                    $key_description .= '<p><strong>' . strip_tags($item->outertext) . '</strong></p>' :
                    $key_description .= '<p>' . strip_tags($item->outertext) . '</p>';
            }

            $serviceKeyword = $this->serviceLocator->get('My\Models\Keyword');
            $id_key = $serviceKeyword->add([
                'key_name' => $name,
                'key_slug' => General::getSlug($name),
                'is_crawler' => 0,
                'created_date' => time(),
                'key_description' => $key_description
            ]);
            if ($id_key) {
                echo \My\General::getColoredString("Insert to tbl_keyword success key_name =  {$name} \n", 'green');
            } else {
                echo \My\General::getColoredString("Insert to tbl_keyword ERROR key_name =  {$name} \n", 'red');
            }
            unset($serviceKeyword, $gg_rp, $gg_rp_dom, $key_description, $id_key);
            $this->flush();
        }
        echo General::getColoredString("add keyword complete", 'yellow', 'cyan');
        return true;
    }

    public function hotTrendAction()
    {
        $current_date = date('Y-m-d');
        $instanceSearchKeyWord = new \My\Search\Keyword();
        for ($i = 0; $i <= 2; $i++) {
            $date = strtotime('-' . $i . ' days', strtotime($current_date));
            $date = date('Ymd', $date);
            echo \My\General::getColoredString("Date = {$date}", 'cyan');
            $href = 'https://www.google.com/trends/hottrends/hotItems?ajax=1&pn=p1&htd=' . $date . '&htv=l';

            $responseCurl = General::crawler($href);
            $arrData = json_decode($responseCurl, true);

            foreach ($arrData['trendsByDateList'] as $data) {
                foreach ($data['trendsList'] as $data1) {
                    $arr_key[] = $data1['title'];
                    if (!empty($data1['relatedSearchesList'])) {
                        foreach ($data1['relatedSearchesList'] as $arr_temp) {
                            if (!empty($arr_temp['query'])) {
                                array_push($arr_key, $arr_temp['query']);
                            }
                        }
                    }

                    foreach ($arr_key as $val) {
                        $is_exits = $instanceSearchKeyWord->getDetail(['key_slug' => trim(General::getSlug($val))]);

                        if ($is_exits) {
                            echo \My\General::getColoredString("exist {$val}", 'red') . '<br/>';
                            continue;
                        }

                        sleep(rand(4, 10));
                        //$url_gg = 'https://www.google.com.vn/search?sclient=psy-ab&biw=1366&bih=212&espv=2&q=' . rawurlencode($val) . '&oq=' . rawurlencode($val);
                        $url_gg = 'https://www.google.com.vn/search?sclient=psy-ab&biw=1366&bih=315&espv=2&q=' . rawurlencode($val) . '&oq=' . rawurlencode($val);

                        $gg_rp = General::crawler($url_gg);
                        $gg_rp_dom = HtmlDomParser::str_get_html($gg_rp);
                        $key_description = '';
                        foreach ($gg_rp_dom->find('.srg .st') as $item) {
                            empty($key_description) ?
                                $key_description .= '<p><strong>' . strip_tags($item->outertext) . '</strong></p>' :
                                $key_description .= '<p>' . strip_tags($item->outertext) . '</p>';
                        }

                        $serviceKeyword = $this->serviceLocator->get('My\Models\Keyword');
                        $id_key = $serviceKeyword->add([
                            'key_name' => $val,
                            'key_slug' => General::getSlug($val),
                            'is_crawler' => 0,
                            'created_date' => time(),
                            'key_description' => $key_description
                        ]);
                        if ($id_key) {
                            echo \My\General::getColoredString("Insert to tbl_keyword success key_name =  {$val} \n", 'green');
                        } else {
                            echo \My\General::getColoredString("Insert to tbl_keyword ERROR key_name =  {$val} \n", 'red');
                        }
                        unset($serviceKeyword, $gg_rp, $gg_rp_dom, $key_description, $id);
                        $this->flush();
                    }
                    $this->flush();
                }
                $this->flush();
            }
            $this->flush();
        }
        die('done');
    }

    public function hotTrendVideoAction()
    {
        $current_date = date('Y-m-d');
        $instanceSearchKeyWord = new \My\Search\Keyword();
        for ($i = 10; $i >= 0; $i--) {
            $date = strtotime('-' . $i . ' days', strtotime($current_date));
            $date = date('Ymd', $date);
            echo \My\General::getColoredString("Date = {$date}", 'cyan');
            $href = 'https://trends.google.com/trends/hotvideos/hotItems?hvd=&geo=US&mob=0&=htd' . $date . '&hvsm=l';
            echo '<pre>';
            print_r($href);
            echo '</pre>';
            die();
            $responseCurl = General::crawler($href);
            echo '<pre>';
            print_r($responseCurl);
            echo '</pre>';
            die();
            $arrData = json_decode($responseCurl, true);
            echo '<pre>';
            print_r($arrData);
            echo '</pre>';
            die();

            foreach ($arrData['trendsByDateList'] as $data) {
                foreach ($data['trendsList'] as $data1) {
                    $arr_key[] = $data1['title'];
                    if (!empty($data1['relatedSearchesList'])) {
                        foreach ($data1['relatedSearchesList'] as $arr_temp) {
                            if (!empty($arr_temp['query'])) {
                                array_push($arr_key, $arr_temp['query']);
                            }
                        }
                    }

                    foreach ($arr_key as $val) {
                        $is_exits = $instanceSearchKeyWord->getDetail(['key_slug' => trim(General::getSlug($val))]);

                        if ($is_exits) {
                            echo \My\General::getColoredString("exist {$val}", 'red') . '<br/>';
                            continue;
                        }

                        $url_gg = 'https://www.google.com.vn/search?sclient=psy-ab&biw=1366&bih=212&espv=2&q=' . rawurlencode($val) . '&oq=' . rawurlencode($val);

                        $gg_rp = General::crawler($url_gg);
                        $gg_rp_dom = HtmlDomParser::str_get_html($gg_rp);
                        $key_description = '';
                        foreach ($gg_rp_dom->find('.srg .st') as $item) {
                            empty($key_description) ?
                                $key_description .= '<p><strong>' . strip_tags($item->outertext) . '</strong></p>' :
                                $key_description .= '<p>' . strip_tags($item->outertext) . '</p>';
                        }

                        $serviceKeyword = $this->serviceLocator->get('My\Models\Keyword');
                        $id_key = $serviceKeyword->add([
                            'key_name' => $val,
                            'key_slug' => General::getSlug($val),
                            'is_crawler' => 0,
                            'created_date' => time(),
                            'key_description' => $key_description
                        ]);
                        if ($id_key) {
                            echo \My\General::getColoredString("Insert to tbl_keyword success key_name =  {$val} \n", 'green');
                        } else {
                            echo \My\General::getColoredString("Insert to tbl_keyword ERROR key_name =  {$val} \n", 'red');
                        }
                        unset($serviceKeyword, $gg_rp, $gg_rp_dom, $key_description, $id);
                        $this->flush();

                        //random sleep
                        sleep(rand(4, 10));
                    }
                    $this->flush();
                }
                $this->flush();
            }
            $this->flush();
        }
        die('done');
    }

    public function videosYoutubeAction()
    {
        $file_success = __CLASS__ . '_' . __FUNCTION__ . '_' . 'Success';
        $file_error = __CLASS__ . '_' . __FUNCTION__ . '_' . 'Error';
        try {
            //        $arr_list_channel = include WEB_ROOT. '/data/list-channel.php';
            $instanceSearchContent = new \My\Search\Content();
            $google_config = General::$google_config;
            $client = new \Google_Client();
            $client->setDeveloperKey($google_config['key']);

            // Define an object that will be used to make all API requests.
            $youtube = new \Google_Service_YouTube($client);

            $arr_cate_channel = include_once(WEB_ROOT . '/data/list-channel.php');
            $arr_channel_cate = [];
            foreach ($arr_cate_channel as $cate => $arr_channel) {
                foreach ($arr_channel as $channel) {
                    $arr_channel_cate[$channel] = $cate;
                }
            }
            unset($arr_cate_channel);
            $arr_channel_cate = $this->custom_shuffle($arr_channel_cate);

            foreach ($arr_channel_cate as $channel_id => $cate_id) {
                $pageToken = '';
                for ($i = 0; $i <= 100; $i++) {
                    if ($i != 0 && $pageToken == '') {
                        break;
                    }

                    //sleep request
                    sleep(5);

                    $searchResponse = $youtube->search->listSearch(
                        'snippet', array(
                            'channelId' => $channel_id,
                            'maxResults' => 50,
                            'pageToken' => $pageToken
                        )
                    );

                    if (empty($searchResponse) || empty($searchResponse->getItems())) {
                        break;
                    }

                    $pageToken = $searchResponse->getNextPageToken() ? $searchResponse->getNextPageToken() : '';

                    $videoIds = [];
                    foreach ($searchResponse->getItems() as $item) {
                        if (empty($item) || empty($item->getSnippet()) || empty($item->getId()->getVideoId())) {
                            continue;
                        }
                        array_push($videoIds, $item->getId()->getVideoId());
                    }

                    if (empty($videoIds)) {
                        continue;
                    }

                    //chỉ lấy info của những video chưa có trong DB
                    $arrContentList = $instanceSearchContent->getList(
                        [
                            'in_from_source' => $videoIds
                        ],
                        [],
                        [
                            'from_source'
                        ]
                    );

                    if (!empty($arrContentList)) {
                        foreach ($arrContentList as $content) {
                            if (($key = array_search($content['from_source'], $videoIds)) !== false) {
                                unset($videoIds[$key]);
                            }
                        }
                    }

                    unset($arrContentList, $searchResponse);

                    if (empty($videoIds)) {
                        continue;
                    }

                    $videoIds = join(',', $videoIds);

                    # Call the videos.list method to retrieve location details for each video.
                    $searchResponse = $youtube->videos->listVideos('snippet, recordingDetails, contentDetails', array(
                        'id' => $videoIds,
                    ));

                    foreach ($searchResponse->getItems() as $item) {

                        if (empty($item) || empty($item->getSnippet())) {
                            continue;
                        }
                        $id = $item->getId();

                        if (empty($id)) {
                            continue;
                        }

                        $title = $item->getSnippet()->getTitle();

                        if (empty($title)) {
                            continue;
                        }

                        $is_exits = $instanceSearchContent->getDetail([
                            'cont_slug' => General::getSlug($title),
                            'status' => 1
                        ]);

                        if (!empty($is_exits)) {
                            echo \My\General::getColoredString("content title = {$title} is exits \n", 'red');
                            continue;
                        }

                        //begin insert tags
                        $arr_tags = $item->getSnippet()->getTags();
                        $arr_tag_id = [];
                        if (!empty($arr_tags)) {
                            $instanceSearchTag = new \My\Search\Tag();

                            foreach ($arr_tags as $tag) {
                                $condition['in_tag_slug'][] = General::getSlug($tag);
                            }
                            $arr_tag_list = $instanceSearchTag->getList($condition, ['tag_id' => ['order' => 'asc']]);
                            $arr_tag_exits = [];
                            if ($arr_tag_list) {
                                foreach ($arr_tag_list as $arr) {
                                    $arr_tag_id[] = $arr['tag_id'];
                                    $arr_tag_exits[] = $arr['tag_slug'];
                                }
                            }
                            $serviceTag = $this->serviceLocator->get('My\Models\Tags');

                            foreach ($arr_tags as $tag) {
                                if (in_array(General::getSlug($tag), $arr_tag_exits)) {
                                    continue;
                                }
                                $arr_data_tag = [
                                    'tag_name' => $tag,
                                    'tag_slug' => General::getSlug($tag),
                                    'user_created' => 1,
                                    'created_date' => time(),
                                    'tag_status' => 1
                                ];
                                $tag_id = $serviceTag->add($arr_data_tag);
                                if ($tag_id > 0) {
                                    $arr_tag_id[] = $tag_id;
                                }
                                continue;
                            }
                        }

                        $description = $item->getSnippet()->getDescription();
                        $arr_image = [];
                        $arr_image_yb = empty($item->getSnippet()->getThumbnails()['modelData']) ? [] : $item->getSnippet()->getThumbnails()['modelData'];

                        foreach ($arr_image_yb as $thumbnail) {
                            $size = $thumbnail['width'] . 'x' . $thumbnail['height'];
                            $arr_image[$size] = \My\General::crawlerImage($thumbnail['url'], $title, $size);
                        }

                        $arr_data_content = [
                            'cont_title' => $title,
                            'cont_slug' => \My\General::getSlug($title),
                            'cont_image' => json_encode($arr_image),
                            'cont_detail' => html_entity_decode($description),
                            'created_date' => time(),
                            'user_created' => 1,
                            'cate_id' => $cate_id,
                            'cont_description' => $description ? $description : $title,
                            'cont_status' => 1,
                            'cont_views' => 0,
                            'method' => 'crawler',
                            'from_source' => $id,
                            'meta_keyword' => str_replace(' ', ',', $title),
                            'updated_date' => time(),
                            'cont_duration' => \My\General::formatDurationLength($item->getContentDetails()->getDuration()),
                            'tag_id' => empty($arr_tag_id) ? '' : ',' . implode(',', $arr_tag_id) . ','
                        ];

                        $serviceContent = $this->serviceLocator->get('My\Models\Content');
                        $id = $serviceContent->add($arr_data_content);
                        if ($id) {
                            //$arr_data_content['cont_id'] = $id;

                            //giảm lượng chia sẻ lên facebook
//                                if ($id % 20 == 0) {
//                                    $this->postToFb($arr_data_content);
//                                }
                            echo \My\General::getColoredString("Crawler success 1 post id = {$id} \n", 'green');
                        } else {
                            echo \My\General::getColoredString("Can not insert content db", 'red');
                        }

                        unset($serviceContent, $arr_data_content, $instanceSearchTag, $serviceTag);
                        $this->flush();
                        continue;
                    }
                    unset($searchResponse);
                }
            }
            return true;
        } catch (\Exception $exc) {
            if (APPLICATION_ENV !== 'production') {
                echo '<pre>';
                print_r([
                    'code' => $exc->getCode(),
                    'messages' => $exc->getMessage()
                ]);
                echo '</pre>';
                die();
            }
            \My\General::writeLog($file_error, []);
        }
    }

    public function videosYoutubeNewAction()
    {
        $params = $this->request->getParams();

        $pid = $params['pid'];

        $file_success = __CLASS__ . '_' . __FUNCTION__ . '_' . 'Success';
        $file_error = __CLASS__ . '_' . __FUNCTION__ . '_' . 'Error';
        $path_file_name = WEB_ROOT . '/data/channel-continue.txt';
        try {
            if (!empty($pid)) {
                shell_exec('kill -9 ' . $pid);
            }

            if (file_exists($path_file_name)) {
                $arr_channel_cate = file_get_contents($path_file_name);
                $arr_channel_cate = unserialize($arr_channel_cate);
            } else {
                $arr_cate_channel = include_once(WEB_ROOT . '/data/list-channel.php');
                $arr_channel_cate = [];
                foreach ($arr_cate_channel as $cate => $arr_channel) {
                    foreach ($arr_channel as $channel) {
                        $arr_channel_cate[$channel] = $cate;
                    }
                }
                unset($arr_cate_channel);
                $arr_channel_cate = $this->custom_shuffle($arr_channel_cate);
            }

            if (empty($arr_channel_cate)) {
                return true;
            }

            $channel_id = key($arr_channel_cate);
            $cate_id = $arr_channel_cate[$channel_id];

            $google_config = General::$google_config;
            $client = new \Google_Client();
            $client->setDeveloperKey($google_config['key']);

            // Define an object that will be used to make all API requests.
            $youtube = new \Google_Service_YouTube($client);

            $pageToken = '';
            for ($i = 0; $i <= 100; $i++) {
                if ($i != 0 && $pageToken == '') {
                    break;
                }

                //sleep request
                sleep(5);

                $searchResponse = $youtube->search->listSearch(
                    'snippet', array(
                        'channelId' => $channel_id,
                        'maxResults' => 50,
                        'pageToken' => $pageToken
                    )
                );

                if (empty($searchResponse) || empty($searchResponse->getItems())) {
                    break;
                }

                $pageToken = $searchResponse->getNextPageToken() ? $searchResponse->getNextPageToken() : '';

                $videoIds = [];
                foreach ($searchResponse->getItems() as $item) {
                    if (empty($item) || empty($item->getSnippet()) || empty($item->getId()->getVideoId())) {
                        continue;
                    }
                    array_push($videoIds, $item->getId()->getVideoId());
                }

                if (empty($videoIds)) {
                    continue;
                }

                //chỉ lấy info của những video chưa có trong DB
                $instanceSearchContent = new \My\Search\Content();

                $arrContentList = $instanceSearchContent->getList(
                    [
                        'in_from_source' => $videoIds
                    ],
                    [],
                    [
                        'from_source'
                    ]
                );

                if (!empty($arrContentList)) {
                    foreach ($arrContentList as $content) {
                        if (($key = array_search($content['from_source'], $videoIds)) !== false) {
                            unset($videoIds[$key]);
                        }
                    }
                }

                unset($arrContentList, $searchResponse);

                if (empty($videoIds)) {
                    continue;
                }

                $videoIds = join(',', $videoIds);

                # Call the videos.list method to retrieve location details for each video.
                $searchResponse = $youtube->videos->listVideos('snippet, recordingDetails, contentDetails', array(
                    'id' => $videoIds,
                ));

                foreach ($searchResponse->getItems() as $item) {

                    if (empty($item) || empty($item->getSnippet())) {
                        continue;
                    }
                    $id = $item->getId();

                    if (empty($id)) {
                        continue;
                    }

                    $title = $item->getSnippet()->getTitle();

                    if (empty($title)) {
                        continue;
                    }

                    $is_exits = $instanceSearchContent->getDetail([
                        'cont_slug' => General::getSlug($title),
                        'status' => 1
                    ]);

                    if (!empty($is_exits)) {
                        echo \My\General::getColoredString("content title = {$title} is exits \n", 'red');
                        continue;
                    }

                    //begin insert tags
                    $arr_tags = $item->getSnippet()->getTags();
                    $arr_tag_id = [];
                    if (!empty($arr_tags)) {
                        $instanceSearchTag = new \My\Search\Tag();

                        foreach ($arr_tags as $tag) {
                            $condition['in_tag_slug'][] = General::getSlug($tag);
                        }
                        $arr_tag_list = $instanceSearchTag->getList($condition, ['tag_id' => ['order' => 'asc']]);
                        $arr_tag_exits = [];
                        if ($arr_tag_list) {
                            foreach ($arr_tag_list as $arr) {
                                $arr_tag_id[] = $arr['tag_id'];
                                $arr_tag_exits[] = $arr['tag_slug'];
                            }
                        }
                        $serviceTag = $this->serviceLocator->get('My\Models\Tags');

                        foreach ($arr_tags as $tag) {
                            if (in_array(General::getSlug($tag), $arr_tag_exits)) {
                                continue;
                            }
                            $arr_data_tag = [
                                'tag_name' => $tag,
                                'tag_slug' => General::getSlug($tag),
                                'user_created' => 1,
                                'created_date' => time(),
                                'tag_status' => 1
                            ];
                            $tag_id = $serviceTag->add($arr_data_tag);
                            if ($tag_id > 0) {
                                $arr_tag_id[] = $tag_id;
                            }
                            continue;
                        }
                    }

                    $description = $item->getSnippet()->getDescription();
                    $arr_image = [];
                    $arr_image_yb = empty($item->getSnippet()->getThumbnails()['modelData']) ? [] : $item->getSnippet()->getThumbnails()['modelData'];

                    foreach ($arr_image_yb as $thumbnail) {
                        $size = $thumbnail['width'] . 'x' . $thumbnail['height'];
                        $arr_image[$size] = \My\General::crawlerImage($thumbnail['url'], $title, $size);
                    }

                    $arr_data_content = [
                        'cont_title' => $title,
                        'cont_slug' => \My\General::getSlug($title),
                        'cont_image' => json_encode($arr_image),
                        'cont_detail' => html_entity_decode($description),
                        'created_date' => time(),
                        'user_created' => 1,
                        'cate_id' => $cate_id,
                        'cont_description' => $description ? $description : $title,
                        'cont_status' => 1,
                        'cont_views' => 0,
                        'method' => 'crawler',
                        'from_source' => $id,
                        'meta_keyword' => str_replace(' ', ',', $title),
                        'updated_date' => time(),
                        'cont_duration' => \My\General::formatDurationLength($item->getContentDetails()->getDuration()),
                        'tag_id' => empty($arr_tag_id) ? '' : ',' . implode(',', $arr_tag_id) . ','
                    ];

                    $serviceContent = $this->serviceLocator->get('My\Models\Content');
                    $id = $serviceContent->add($arr_data_content);
                    if ($id) {
                        //$arr_data_content['cont_id'] = $id;

                        //giảm lượng chia sẻ lên facebook
//                                if ($id % 20 == 0) {
//                                    $this->postToFb($arr_data_content);
//                                }
                        echo \My\General::getColoredString("Crawler success 1 post id = {$id} \n", 'green');
                    } else {
                        echo \My\General::getColoredString("Can not insert content db", 'red');
                    }

                    unset($serviceContent, $arr_data_content, $instanceSearchTag, $serviceTag);
                    $this->flush();
                    continue;
                }
                unset($searchResponse, $instanceSearchContent);
            }

            unset($arr_channel_cate[$channel_id]);

            file_put_contents($path_file_name, serialize($arr_channel_cate));

            exec("ps -ef | grep -v grep | grep videos-youtube | awk '{ print $2 }'", $PID);

            return shell_exec('nohup php ' . PUBLIC_PATH . '/index.php videos-youtube-new --pid=' . current($PID) . ' >/dev/null & echo 2>&1 & echo $!');
        } catch (\Exception $exc) {
            if (APPLICATION_ENV !== 'production') {
                echo '<pre>';
                print_r([
                    'code' => $exc->getCode(),
                    'messages' => $exc->getMessage()
                ]);
                echo '</pre>';
                die();
            }
            \My\General::writeLog($file_error, []);
        }
    }

    public function updateKeywordAction()
    {
        try {
            $params = $this->request->getParams();
            $pid = $params['pid'];
            if (!empty($pid)) {
                shell_exec('kill -9 ' . $pid);
            }

            $path_file_name = WEB_ROOT . '/logs/key-tag.txt';

            $begin_tag_id = 1;
            if (file_exists($path_file_name)) {
                $begin_tag_id = file_get_contents($path_file_name);
            }

            //get list tag
            $instanceSearchTag = new \My\Search\Tag();
            $arrTagList = $instanceSearchTag->getListLimit(
                [
                    'gte_tag_id' => $begin_tag_id,
                    'tag_status' => 1
                ],
                1,
                50,
                [
                    'tag_id' => ['order' => 'asc']
                ]
            );

            if (empty($arrTagList)) {
                return true;
            }

            unset($instanceSearchTag);

            $instanceSearchKeyWord = new \My\Search\Keyword();
            $end_tag_id = $arrTagList[49]['tag_id']+1;
            foreach ($arrTagList as $tag) {
                $is_exits = $instanceSearchKeyWord->getDetail(['key_slug' => $tag['tag_slug']]);

                if ($is_exits) {
                    continue;
                }

                $url_gg = 'https://www.google.com/search?sclient=psy-ab&biw=1366&bih=315&espv=2&q=' . rawurlencode($tag['tag_name']) . '&oq=' . rawurlencode($tag['tag_name']);

                $gg_rp = General::crawler($url_gg);
                $gg_rp_dom = new Query($gg_rp);
                $results = $gg_rp_dom->execute('.st');
                if (!count($results)) {
                    continue;
                }

                $key_description = '';

                foreach ($results as $item) {
                    empty($key_description) ?
                        $key_description .= '<p><strong>' . strip_tags($item->textContent) . '</strong></p>' :
                        $key_description .= '<p>' . strip_tags($item->textContent) . '</p>';
                }

                sleep(rand(4, 10));
                $serviceKeyword = $this->serviceLocator->get('My\Models\Keyword');
                $id_key = $serviceKeyword->add([
                    'key_name' => $tag['tag_name'],
                    'key_slug' => $tag['tag_slug'],
                    'is_crawler' => 0,
                    'created_date' => time(),
                    'key_description' => $key_description
                ]);
                if ($id_key) {
                    echo \My\General::getColoredString("Insert to tbl_keyword success key_name =  {$tag['tag_name']} ", 'green');
                } else {
                    echo \My\General::getColoredString("Insert to tbl_keyword ERROR key_name =  {$tag['tag_name']} ", 'red');
                }
                unset($serviceKeyword, $gg_rp, $gg_rp_dom, $key_description, $id);
                $this->flush();
            }

            file_put_contents($path_file_name, $end_tag_id);

            unset($arrTagList);

            exec("ps -ef | grep -v grep | grep update-keyword | awk '{ print $2 }'", $PID);

            return shell_exec('nohup php ' . PUBLIC_PATH . '/index.php update-keyword --pid=' . current($PID) . ' >/dev/null & echo 2>&1 & echo $!');

        } catch (\Exception $exc) {
            return false;
        }
    }

    public function updateNewKeyAction()
    {
        $params = $this->request->getParams();
        $id_begin = $last_id = $params['id'];
        $PID = $params['pid'];
        if (!empty($PID)) {
            shell_exec('kill -9 ' . $PID);
        }

        if (empty($id_begin)) {
            return true;
        }

        $file = '/var/www/khampha/html/logs/updateKW.txt';
        try {
            $instanceSearch = new \My\Search\Keyword();
            $arrKeyword = $instanceSearch->getListLimit(
                [
                    'key_id_greater' => $id_begin
                ],
                1,
                50,
                [
                    'key_id' => [
                        'order' => 'asc'
                    ]
                ],
                [
                    'key_id',
                    'key_name',
                    'key_description'
                ]
            );

            if (empty($arrKeyword)) {
                return true;
            }
            foreach ($arrKeyword as $arr) {
                if (empty($arr['key_id']) || !empty($arr['key_description'])) {
                    continue;
                }
                if ($arr['key_id'] == 79375) {
                    continue;
                }
                $last_id = $arr['key_id'];

                //search vào gg
                //https://www.google.com.vn/search?q=chien+nguyen&rlz=1C1CHBF_enVN720VN720&oq=chien+nguyen&aqs=chrome.0.69i59l2j0l4.5779j0j4&sourceid=chrome&ie=UTF-8
                //https://www.google.com.vn/webhp?sourceid=chrome-instant&rlz=1C1CHBF_enVN720VN720&ion=1&espv=2&ie=UTF-8#q=nguy%E1%BB%85n%20ng%E1%BB%8Dc%20chi%E1%BA%BFn
                //$url_gg = 'https://www.google.com.vn/webhp?sourceid=chrome-instant&rlz=1C1CHBF_enVN720VN720&ion=1&espv=2&ie=UTF-8#q='.rawurlencode($arr['key_name']);
                $url_gg = 'https://www.google.com.vn/search?sclient=psy-ab&biw=1366&bih=212&espv=2&q=' . rawurlencode($arr['key_name']) . '&oq=' . rawurlencode($arr['key_name']);

                $gg_rp = General::crawler($url_gg);

                $gg_rp_dom = new Query($gg_rp);
                $results = $gg_rp_dom->execute('.st');
                if (!count($results)) {
                    continue;
                }

                $key_description = '';

                foreach ($results as $item) {
                    empty($key_description) ?
                        $key_description .= '<p><strong>' . strip_tags($item->textContent) . '</strong></p>' :
                        $key_description .= '<p>' . strip_tags($item->textContent) . '</p>';
                }

                $serviceKeyword = $this->serviceLocator->get('My\Models\Keyword');
                $rs = $serviceKeyword->edit(['key_description' => $key_description], $arr['key_id']);
                if ($rs) {
                    file_put_contents($file, $arr['key_id'] . PHP_EOL, FILE_APPEND);
                } else {
                    file_put_contents($file, 'ERROR ID = ' . $arr['key_id'] . PHP_EOL, FILE_APPEND);
                    continue;
                }
                unset($serviceKeyword, $gg_rp, $gg_rp_dom, $key_description, $id, $url_gg, $results);
                $this->flush();

                //random sleep
                sleep(rand(4, 10));
            }
            $this->flush();
            unset($arrKeyword);
            exec("ps -ef | grep -v grep | grep update-new-key | awk '{ print $2 }'", $PID);

            return shell_exec('php ' . PUBLIC_PATH . '/index.php update-new-key --id=' . $last_id . ' --pid=' . current($PID));

        } catch (\Exception $exc) {
            file_put_contents($file, $exc->getCode() . ' => ' . $exc->getMessage() . PHP_EOL, FILE_APPEND);
            return true;
        }
    }

    public function checkProcessAction()
    {
        $params = $this->request->getParams();
        $process_name = $params['name'];
        if (empty($process_name)) {
            return true;
        }

        exec("ps -ef | grep -v grep | grep '.$process_name.' | awk '{ print $2 }'", $PID);
        exec("ps -ef | grep -v grep | grep update-new-key | awk '{ print $2 }'", $current_PID);

        if (empty($PID)) {
            switch ($process_name) {
                case 'update-new-key':
                    //find last id
                    //$file = '/var/www/khampha/html/logs/updateKW.txt';
                    $last_id = exec('tail -n 1 /var/www/khampha/html/logs/updateKW.txt');

                    if (strstr($last_id, 'Cannot query; no document registered')) {
                        shell_exec("sed -i '$ d' /var/www/khampha/html/logs/updateKW.txt");
                    }

                    $last_id = exec('tail -n 1 /var/www/khampha/html/logs/updateKW.txt');
                    shell_exec('php ' . PUBLIC_PATH . '/index.php update-new-key --id=' . $last_id . ' --pid=' . current($current_PID));
                    break;
            }
        }

        return true;
    }

    public function controlWorkerAction()
    {
        //check crawler from youtube
        exec("ps -ef | grep -v grep | grep videos-youtube | awk '{ print $2 }'", $PID);
        if (empty($PID)) {
            shell_exec('php ' . PUBLIC_PATH . '/index.php videos-youtube');
        }

        return true;
    }

    public function custom_shuffle($my_array = array())
    {
        $copy = array();
        while (count($my_array)) {
            // takes a rand array elements by its key
            $element = array_rand($my_array);
            // assign the array and its value to an another array
            $copy[$element] = $my_array[$element];
            //delete the element from source array
            unset($my_array[$element]);
        }
        return $copy;
    }

}
