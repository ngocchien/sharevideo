<?php

namespace My\Job;

use My\General;

class JobViewContent extends JobAbstract
{
    /*
     * save Content
     */

    public function writeContentView($params)
    {
        if ($params->workload()) {
            $arrParams = unserialize($params->workload());
        }

        if (empty($arrParams)) {
            echo General::getColoredString("ERROR: Params is incorrent or empty ", 'light_cyan', 'red');
            return false;
        }

        $id = $arrParams['id'];
        $instanceSearch = new \My\Search\ContentView();
        $arrDocument = new \Elastica\Document($id, $arrParams);
        $intResult = $instanceSearch->add($arrDocument);

        if (!$intResult) {
            echo General::getColoredString("ERROR: Cannot add content ID = {$id} to Search \n", 'light_cyan', 'red');
            return false;
        }

        echo General::getColoredString("Add content ID: {$id} to Search Success", 'green');

        return true;
    }

    /* edit */

    public function editContentView($params)
    {

        if ($params->workload()) {
            $arrParams = unserialize($params->workload());
        }

        if (empty($arrParams)) {
            echo General::getColoredString("ERROR: Params is incorrent or empty ", 'light_cyan', 'red');
            return false;
        }

        $id = $arrParams['id'];
        $updateData = new \Elastica\Document();
        $updateData->setData($arrParams);
        $document = new \Elastica\Document($id, $arrParams);
        $document->setUpsert($updateData);

        $instanceSearch = new \My\Search\ContentView();
        $result = $instanceSearch->edit($document);

        if (!$result) {
            echo General::getColoredString("ERROR: Cannot Edit id = {$id} to Search \n", 'light_cyan', 'red');
            return false;
        }

        echo General::getColoredString("Edit id: {$id} to Search Success", 'green');

        return true;
    }

}
