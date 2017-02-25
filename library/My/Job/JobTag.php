<?php

namespace My\Job;

use My\General;

class JobTag extends JobAbstract
{
    public function writeTag($params)
    {
        if ($params->workload()) {
            $arrParams = unserialize($params->workload());
        }

        if (empty($arrParams)) {
            echo General::getColoredString("ERROR: Params is incorrent or empty ", 'light_cyan', 'red');
            return false;
        }

        $id = $arrParams['tag_id'];
        $instanceSearch = new \My\Search\Tag();
        $arrDocument = new \Elastica\Document($id, $arrParams);
        $intResult = $instanceSearch->add($arrDocument);

        if (!$intResult) {
            echo General::getColoredString("ERROR: Cannot add Tag ID = {$id} to Search \n", 'light_cyan', 'red');
            return false;
        }

        echo General::getColoredString("Add Tag ID: {$id} to Search Success", 'green');

        return true;
    }

    public function editTag($params)
    {

        if ($params->workload()) {
            $arrParams = unserialize($params->workload());
        }

        if (empty($arrParams)) {
            echo General::getColoredString("ERROR: Params is incorrent or empty ", 'light_cyan', 'red');
            return false;
        }

        $id = $arrParams['tag_id'];
        $updateData = new \Elastica\Document();
        $updateData->setData($arrParams);
        $document = new \Elastica\Document($id, $arrParams);
        $document->setUpsert($updateData);

        $instanceSearch = new \My\Search\Tag();
        $result = $instanceSearch->edit($document);

        if (!$result) {
            echo General::getColoredString("ERROR: Cannot Edit tag id = {$id} to Search \n", 'light_cyan', 'red');
            return false;
        }

        echo General::getColoredString("Edit tag id: {$id} to Search Success", 'green');

        return true;
    }

}
