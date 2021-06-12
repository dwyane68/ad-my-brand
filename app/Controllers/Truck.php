<?php

namespace App\Controllers;

Header('Access-Control-Allow-Origin: *'); //for allow any domain, insecure
Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE'); //method allowed

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Config\Database;

class Truck extends ResourceController
{
    use ResponseTrait;
    const RADIUS      = 50;
    const FETCH_LIMIT = 20;

    public function index()
    {
        $latitude   = (double) $this->request->getVar('latitude');
        $longitude  = (double) $this->request->getVar('longitude');
        $radius     = self::RADIUS;
        $fetchLimit = self::FETCH_LIMIT;

//        $latitude = 37.7509316476402;
//        $longitude= -122.4114199662057;

        if (!$latitude) {
            return $this->respond(['data' => [], 'message' => 'Latitude is required']);
        }
        if (!$longitude) {
            return $this->respond(['data' => [], 'message' => 'Longitude is required']);
        }

        $db = Database::connect();

        $sql = "SELECT * FROM (
                    SELECT *,
                        (
                            (
                                (
                                acos(
                                    sin(( $latitude * pi() / 180))
                                    *
                                    sin(( `latitude` * pi() / 180)) + cos(( $latitude * pi() /180 ))
                                                                               *
                                                                               cos(( `latitude` * pi() / 180)) * cos((( $longitude - `longitude`) * pi()/180)))
                                ) * 180/pi()
                            ) * 60 * 1.1515 * 1.609344
                        )
                    as distance FROM `truck_locations`
                ) myTable
                WHERE distance <= $radius
                ORDER BY distance ASC 
                LIMIT $fetchLimit";

        $query   = $db->query($sql);
        $results = $query->getResult();

        $payload = [
            'payload' => [
                'data' => $results,
            ],
            'message' => 'Truck locations fetched'
        ];

        return $this->respond($payload);
    }
}
