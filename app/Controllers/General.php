<?php

namespace App\Controllers;

use App\Libraries\DataSf;
use CodeIgniter\API\ResponseTrait;

class General extends BaseController
{
    use ResponseTrait;
	public function index()
	{
	    $dfHelper = new DataSf();
	    $dfHelper->fetch();
	    $message = ['data' => [], 'message' => 'Data imported!'];
        return $this->respond($message);
	}
}
