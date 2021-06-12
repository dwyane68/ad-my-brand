<?php
namespace App\Libraries;

use Config\Database;

class DataSf
{
    const API_URL = 'https://data.sfgov.org/api/views/rqzj-sfat/rows.xml?accessType=DOWNLOAD';

    public function fetch()
    {
        $xml = new \SimpleXMLElement(file_get_contents(self::API_URL));
        $xml = (array) $xml->row;
        $insertData = [];

        foreach ($xml['row'] as $truckData) {
            $truckLocation = [
                'objectid' => (string)$truckData->objectid,
                'applicant' => (string)$truckData->applicant,
                'facility_type' => (string)$truckData->facilitytype,
                'location_description' => (string)$truckData->locationdescription,
                'address' => (string)$truckData->address,
                'latitude' => (string)$truckData->latitude,
                'longitude' => (string)$truckData->longitude,
            ];
            $insertData[] = $truckLocation;
        }
        $db = Database::connect();
        $builder = $db->table('truck_locations');
        $builder->truncate();
        $builder->insertBatch($insertData);
    }
}