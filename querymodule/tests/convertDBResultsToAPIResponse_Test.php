<?php

require_once dirname(__FILE__) . '/../app/convertDBResultsToNestedStructure.php';
require_once dirname(__FILE__) . '/../app/entitySpecs.php';

class convertDBResultsToAPIResponse_Test extends PHPUnit_Framework_TestCase
{
    public function testConversion()
    {
        global $PATENT_FIELD_SPECS;
        global $PATENT_ENTITY_SPECS;
//        $dbResults = array(array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677650', 'inventor_last_name' => 'Watterodt', 'patent_title' => 'Methods and devices for drying coated stents', 'assignee_last_name' => '', 'patent_id' => '8677650', 'inventor_id' => '8677650-1', 'assignee_id' => '3429326e7bb965b6108a4a2e6c61c426'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677650', 'inventor_last_name' => 'Watterodt', 'patent_title' => 'Methods and devices for drying coated stents', 'assignee_last_name' => '', 'patent_id' => '8677650', 'inventor_id' => '8677650-1', 'assignee_id' => 'afd04c6baba838ae9578fa8092e2eeec'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677650', 'inventor_last_name' => 'Gillick', 'patent_title' => 'Methods and devices for drying coated stents', 'assignee_last_name' => '', 'patent_id' => '8677650', 'inventor_id' => '8677650-10', 'assignee_id' => '3429326e7bb965b6108a4a2e6c61c426'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677650', 'inventor_last_name' => 'Gillick', 'patent_title' => 'Methods and devices for drying coated stents', 'assignee_last_name' => '', 'patent_id' => '8677650', 'inventor_id' => '8677650-10', 'assignee_id' => 'afd04c6baba838ae9578fa8092e2eeec'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677650', 'inventor_last_name' => 'Papp', 'patent_title' => 'Methods and devices for drying coated stents', 'assignee_last_name' => '', 'patent_id' => '8677650', 'inventor_id' => '8677650-11', 'assignee_id' => '3429326e7bb965b6108a4a2e6c61c426'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677650', 'inventor_last_name' => 'Papp', 'patent_title' => 'Methods and devices for drying coated stents', 'assignee_last_name' => '', 'patent_id' => '8677650', 'inventor_id' => '8677650-11', 'assignee_id' => 'afd04c6baba838ae9578fa8092e2eeec'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677650', 'inventor_last_name' => 'Park', 'patent_title' => 'Methods and devices for drying coated stents', 'assignee_last_name' => '', 'patent_id' => '8677650', 'inventor_id' => '8677650-2', 'assignee_id' => '3429326e7bb965b6108a4a2e6c61c426'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677650', 'inventor_last_name' => 'Park', 'patent_title' => 'Methods and devices for drying coated stents', 'assignee_last_name' => '', 'patent_id' => '8677650', 'inventor_id' => '8677650-2', 'assignee_id' => 'afd04c6baba838ae9578fa8092e2eeec'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677650', 'inventor_last_name' => 'Rego', 'patent_title' => 'Methods and devices for drying coated stents', 'assignee_last_name' => '', 'patent_id' => '8677650', 'inventor_id' => '8677650-3', 'assignee_id' => '3429326e7bb965b6108a4a2e6c61c426'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677650', 'inventor_last_name' => 'Rego', 'patent_title' => 'Methods and devices for drying coated stents', 'assignee_last_name' => '', 'patent_id' => '8677650', 'inventor_id' => '8677650-3', 'assignee_id' => 'afd04c6baba838ae9578fa8092e2eeec'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677650', 'inventor_last_name' => 'Andreacchi', 'patent_title' => 'Methods and devices for drying coated stents', 'assignee_last_name' => '', 'patent_id' => '8677650', 'inventor_id' => '8677650-4', 'assignee_id' => '3429326e7bb965b6108a4a2e6c61c426'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677650', 'inventor_last_name' => 'Andreacchi', 'patent_title' => 'Methods and devices for drying coated stents', 'assignee_last_name' => '', 'patent_id' => '8677650', 'inventor_id' => '8677650-4', 'assignee_id' => 'afd04c6baba838ae9578fa8092e2eeec'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677650', 'inventor_last_name' => 'Chen', 'patent_title' => 'Methods and devices for drying coated stents', 'assignee_last_name' => '', 'patent_id' => '8677650', 'inventor_id' => '8677650-5', 'assignee_id' => '3429326e7bb965b6108a4a2e6c61c426'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677650', 'inventor_last_name' => 'Chen', 'patent_title' => 'Methods and devices for drying coated stents', 'assignee_last_name' => '', 'patent_id' => '8677650', 'inventor_id' => '8677650-5', 'assignee_id' => 'afd04c6baba838ae9578fa8092e2eeec'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677650', 'inventor_last_name' => 'Currlin', 'patent_title' => 'Methods and devices for drying coated stents', 'assignee_last_name' => '', 'patent_id' => '8677650', 'inventor_id' => '8677650-6', 'assignee_id' => '3429326e7bb965b6108a4a2e6c61c426'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677650', 'inventor_last_name' => 'Currlin', 'patent_title' => 'Methods and devices for drying coated stents', 'assignee_last_name' => '', 'patent_id' => '8677650', 'inventor_id' => '8677650-6', 'assignee_id' => 'afd04c6baba838ae9578fa8092e2eeec'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677650', 'inventor_last_name' => 'Garcia', 'patent_title' => 'Methods and devices for drying coated stents', 'assignee_last_name' => '', 'patent_id' => '8677650', 'inventor_id' => '8677650-7', 'assignee_id' => '3429326e7bb965b6108a4a2e6c61c426'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677650', 'inventor_last_name' => 'Garcia', 'patent_title' => 'Methods and devices for drying coated stents', 'assignee_last_name' => '', 'patent_id' => '8677650', 'inventor_id' => '8677650-7', 'assignee_id' => 'afd04c6baba838ae9578fa8092e2eeec'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677650', 'inventor_last_name' => 'Sciver', 'patent_title' => 'Methods and devices for drying coated stents', 'assignee_last_name' => '', 'patent_id' => '8677650', 'inventor_id' => '8677650-8', 'assignee_id' => '3429326e7bb965b6108a4a2e6c61c426'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677650', 'inventor_last_name' => 'Sciver', 'patent_title' => 'Methods and devices for drying coated stents', 'assignee_last_name' => '', 'patent_id' => '8677650', 'inventor_id' => '8677650-8', 'assignee_id' => 'afd04c6baba838ae9578fa8092e2eeec'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677650', 'inventor_last_name' => 'Glenn', 'patent_title' => 'Methods and devices for drying coated stents', 'assignee_last_name' => '', 'patent_id' => '8677650', 'inventor_id' => '8677650-9', 'assignee_id' => '3429326e7bb965b6108a4a2e6c61c426'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677650', 'inventor_last_name' => 'Glenn', 'patent_title' => 'Methods and devices for drying coated stents', 'assignee_last_name' => '', 'patent_id' => '8677650', 'inventor_id' => '8677650-9', 'assignee_id' => 'afd04c6baba838ae9578fa8092e2eeec'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677690', 'inventor_last_name' => 'Lee', 'patent_title' => 'Fuel door opening/closing apparatus for vehicle', 'assignee_last_name' => '', 'patent_id' => '8677690', 'inventor_id' => '8677690-1', 'assignee_id' => '6940c2025c37e80b9ad46852399a97f9'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677690', 'inventor_last_name' => 'Lee', 'patent_title' => 'Fuel door opening/closing apparatus for vehicle', 'assignee_last_name' => '', 'patent_id' => '8677690', 'inventor_id' => '8677690-1', 'assignee_id' => 'e04f205b05e4a7ea46d3096867c3dd5b'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677690', 'inventor_last_name' => 'Kim', 'patent_title' => 'Fuel door opening/closing apparatus for vehicle', 'assignee_last_name' => '', 'patent_id' => '8677690', 'inventor_id' => '8677690-2', 'assignee_id' => '6940c2025c37e80b9ad46852399a97f9'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677690', 'inventor_last_name' => 'Kim', 'patent_title' => 'Fuel door opening/closing apparatus for vehicle', 'assignee_last_name' => '', 'patent_id' => '8677690', 'inventor_id' => '8677690-2', 'assignee_id' => 'e04f205b05e4a7ea46d3096867c3dd5b'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677690', 'inventor_last_name' => 'Oh', 'patent_title' => 'Fuel door opening/closing apparatus for vehicle', 'assignee_last_name' => '', 'patent_id' => '8677690', 'inventor_id' => '8677690-3', 'assignee_id' => '6940c2025c37e80b9ad46852399a97f9'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677690', 'inventor_last_name' => 'Oh', 'patent_title' => 'Fuel door opening/closing apparatus for vehicle', 'assignee_last_name' => '', 'patent_id' => '8677690', 'inventor_id' => '8677690-3', 'assignee_id' => 'e04f205b05e4a7ea46d3096867c3dd5b'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677740', 'inventor_last_name' => 'Lee', 'patent_title' => 'Method for predicting regeneration of DeNOx catalyst and exhaust system using the same', 'assignee_last_name' => '', 'patent_id' => '8677740', 'inventor_id' => '8677740-1', 'assignee_id' => '6940c2025c37e80b9ad46852399a97f9'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677740', 'inventor_last_name' => 'Lee', 'patent_title' => 'Method for predicting regeneration of DeNOx catalyst and exhaust system using the same', 'assignee_last_name' => '', 'patent_id' => '8677740', 'inventor_id' => '8677740-1', 'assignee_id' => 'e04f205b05e4a7ea46d3096867c3dd5b'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677740', 'inventor_last_name' => 'Lee', 'patent_title' => 'Method for predicting regeneration of DeNOx catalyst and exhaust system using the same', 'assignee_last_name' => '', 'patent_id' => '8677740', 'inventor_id' => '8677740-1', 'assignee_id' => 'e24d52962caf94a7cc05a85a6741251d'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677740', 'inventor_last_name' => 'Park', 'patent_title' => 'Method for predicting regeneration of DeNOx catalyst and exhaust system using the same', 'assignee_last_name' => '', 'patent_id' => '8677740', 'inventor_id' => '8677740-2', 'assignee_id' => '6940c2025c37e80b9ad46852399a97f9'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677740', 'inventor_last_name' => 'Park', 'patent_title' => 'Method for predicting regeneration of DeNOx catalyst and exhaust system using the same', 'assignee_last_name' => '', 'patent_id' => '8677740', 'inventor_id' => '8677740-2', 'assignee_id' => 'e04f205b05e4a7ea46d3096867c3dd5b'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677740', 'inventor_last_name' => 'Park', 'patent_title' => 'Method for predicting regeneration of DeNOx catalyst and exhaust system using the same', 'assignee_last_name' => '', 'patent_id' => '8677740', 'inventor_id' => '8677740-2', 'assignee_id' => 'e24d52962caf94a7cc05a85a6741251d'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677740', 'inventor_last_name' => 'Severin', 'patent_title' => 'Method for predicting regeneration of DeNOx catalyst and exhaust system using the same', 'assignee_last_name' => '', 'patent_id' => '8677740', 'inventor_id' => '8677740-3', 'assignee_id' => '6940c2025c37e80b9ad46852399a97f9'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677740', 'inventor_last_name' => 'Severin', 'patent_title' => 'Method for predicting regeneration of DeNOx catalyst and exhaust system using the same', 'assignee_last_name' => '', 'patent_id' => '8677740', 'inventor_id' => '8677740-3', 'assignee_id' => 'e04f205b05e4a7ea46d3096867c3dd5b'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677740', 'inventor_last_name' => 'Severin', 'patent_title' => 'Method for predicting regeneration of DeNOx catalyst and exhaust system using the same', 'assignee_last_name' => '', 'patent_id' => '8677740', 'inventor_id' => '8677740-3', 'assignee_id' => 'e24d52962caf94a7cc05a85a6741251d'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677740', 'inventor_last_name' => 'Wittka', 'patent_title' => 'Method for predicting regeneration of DeNOx catalyst and exhaust system using the same', 'assignee_last_name' => '', 'patent_id' => '8677740', 'inventor_id' => '8677740-4', 'assignee_id' => '6940c2025c37e80b9ad46852399a97f9'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677740', 'inventor_last_name' => 'Wittka', 'patent_title' => 'Method for predicting regeneration of DeNOx catalyst and exhaust system using the same', 'assignee_last_name' => '', 'patent_id' => '8677740', 'inventor_id' => '8677740-4', 'assignee_id' => 'e04f205b05e4a7ea46d3096867c3dd5b'), array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677740', 'inventor_last_name' => 'Wittka', 'patent_title' => 'Method for predicting regeneration of DeNOx catalyst and exhaust system using the same', 'assignee_last_name' => '', 'patent_id' => '8677740', 'inventor_id' => '8677740-4', 'assignee_id' => 'e24d52962caf94a7cc05a85a6741251d'));
        $dbResults['patents'][] = array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677650', 'patent_id' => '8677650', 'patent_title' => 'Methods and devices for drying coated stents');
        $dbResults['patents'][] = array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677690', 'patent_id' => '8677690', 'patent_title' => 'Fuel door opening/closing apparatus for vehicle');
        $dbResults['patents'][] = array('patent_country' => 'US', 'patent_type' => 'utility', 'patent_number' => '8677740', 'patent_id' => '8677740', 'patent_title' => 'Method for predicting regeneration of DeNOx catalyst and exhaust system using the same');
        $dbResults['inventors'][] = array('inventor_last_name' => 'Watterodt', 'patent_id' => '8677650', 'inventor_id' => '8677650-1');
        $dbResults['inventors'][] = array('inventor_last_name' => 'Gillick', 'patent_id' => '8677650', 'inventor_id' => '8677650-10');
        $dbResults['inventors'][] = array('inventor_last_name' => 'Papp', 'patent_id' => '8677650', 'inventor_id' => '8677650-11');
        $dbResults['inventors'][] = array('inventor_last_name' => 'Park', 'patent_id' => '8677650', 'inventor_id' => '8677650-2');
        $dbResults['inventors'][] = array('inventor_last_name' => 'Rego', 'patent_id' => '8677650', 'inventor_id' => '8677650-3');
        $dbResults['inventors'][] = array('inventor_last_name' => 'Andreacchi', 'patent_id' => '8677650', 'inventor_id' => '8677650-4');
        $dbResults['inventors'][] = array('inventor_last_name' => 'Chen', 'patent_id' => '8677650', 'inventor_id' => '8677650-5');
        $dbResults['inventors'][] = array('inventor_last_name' => 'Currlin', 'patent_id' => '8677650', 'inventor_id' => '8677650-6');
        $dbResults['inventors'][] = array('inventor_last_name' => 'Garcia', 'patent_id' => '8677650', 'inventor_id' => '8677650-7');
        $dbResults['inventors'][] = array('inventor_last_name' => 'Sciver', 'patent_id' => '8677650', 'inventor_id' => '8677650-8');
        $dbResults['inventors'][] = array('inventor_last_name' => 'Glenn', 'patent_id' => '8677650', 'inventor_id' => '8677650-9');
        $dbResults['inventors'][] = array('inventor_last_name' => 'Lee', 'patent_id' => '8677690', 'inventor_id' => '8677690-1');
        $dbResults['inventors'][] = array('inventor_last_name' => 'Kim', 'patent_id' => '8677690', 'inventor_id' => '8677690-2');
        $dbResults['inventors'][] = array('inventor_last_name' => 'Oh', 'patent_id' => '8677690', 'inventor_id' => '8677690-3');
        $dbResults['inventors'][] = array('inventor_last_name' => 'Lee', 'patent_id' => '8677740', 'inventor_id' => '8677740-1');
        $dbResults['inventors'][] = array('inventor_last_name' => 'Park', 'patent_id' => '8677740', 'inventor_id' => '8677740-2');
        $dbResults['inventors'][] = array('inventor_last_name' => 'Severin', 'patent_id' => '8677740', 'inventor_id' => '8677740-3');
        $dbResults['inventors'][] = array('inventor_last_name' => 'Wittka', 'patent_id' => '8677740', 'inventor_id' => '8677740-4');
        $dbResults['assignees'][] = array('assignee_last_name' => '', 'patent_id' => '8677650', 'assignee_id' => '3429326e7bb965b6108a4a2e6c61c426');
        $dbResults['assignees'][] = array('assignee_last_name' => '', 'patent_id' => '8677650', 'assignee_id' => 'afd04c6baba838ae9578fa8092e2eeec');
        $dbResults['assignees'][] = array('assignee_last_name' => '', 'patent_id' => '8677690', 'assignee_id' => '6940c2025c37e80b9ad46852399a97f9');
        $dbResults['assignees'][] = array('assignee_last_name' => '', 'patent_id' => '8677690', 'assignee_id' => 'e04f205b05e4a7ea46d3096867c3dd5b');
        $dbResults['assignees'][] = array('assignee_last_name' => '', 'patent_id' => '8677740', 'assignee_id' => '6940c2025c37e80b9ad46852399a97f9');
        $dbResults['assignees'][] = array('assignee_last_name' => '', 'patent_id' => '8677740', 'assignee_id' => 'e04f205b05e4a7ea46d3096867c3dd5b');
        $dbResults['assignees'][] = array('assignee_last_name' => '', 'patent_id' => '8677740', 'assignee_id' => 'e24d52962caf94a7cc05a85a6741251d');
        $selectFieldSpecs['patent_type'] = $PATENT_FIELD_SPECS['patent_type'];
        $selectFieldSpecs['patent_number'] = $PATENT_FIELD_SPECS['patent_number'];
        $selectFieldSpecs['inventor_last_name'] = $PATENT_FIELD_SPECS['inventor_last_name'];
        $selectFieldSpecs['patent_title'] = $PATENT_FIELD_SPECS['patent_title'];
        $selectFieldSpecs['assignee_last_name'] = $PATENT_FIELD_SPECS['assignee_last_name'];
        $results = convertDBResultsToNestedStructure($PATENT_ENTITY_SPECS, $dbResults, $selectFieldSpecs);
        $expected = array('count' => 3, 'patents' => array(array('patent_type' => 'utility', 'patent_number' => '8677650', 'inventors' => array(array('inventor_last_name' => 'Watterodt'), array('inventor_last_name' => 'Gillick'), array('inventor_last_name' => 'Papp'), array('inventor_last_name' => 'Park'), array('inventor_last_name' => 'Rego'), array('inventor_last_name' => 'Andreacchi'), array('inventor_last_name' => 'Chen'), array('inventor_last_name' => 'Currlin'), array('inventor_last_name' => 'Garcia'), array('inventor_last_name' => 'Sciver'), array('inventor_last_name' => 'Glenn')), 'patent_title' => 'Methods and devices for drying coated stents', 'assignees' => array(array('assignee_last_name' => ''), array('assignee_last_name' => ''))), array('patent_type' => 'utility', 'patent_number' => '8677690', 'inventors' => array(array('inventor_last_name' => 'Lee'), array('inventor_last_name' => 'Kim'), array('inventor_last_name' => 'Oh')), 'patent_title' => 'Fuel door opening/closing apparatus for vehicle', 'assignees' => array(array('assignee_last_name' => ''), array('assignee_last_name' => ''))), array('patent_type' => 'utility', 'patent_number' => '8677740', 'inventors' => array(array('inventor_last_name' => 'Lee'), array('inventor_last_name' => 'Park'), array('inventor_last_name' => 'Severin'), array('inventor_last_name' => 'Wittka')), 'patent_title' => 'Method for predicting regeneration of DeNOx catalyst and exhaust system using the same', 'assignees' => array(array('assignee_last_name' => ''), array('assignee_last_name' => ''), array('assignee_last_name' => '')))));
        $this->assertEquals($expected, $results);
    }
}
 