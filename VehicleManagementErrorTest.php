<?php
class VehicleManagementErrorTest extends UpdateTestCase {

  public static function setUpBeforeClass() {
    parent::setUpBeforeClass();
    Test::setData();
  }
  
  public function testCreateDublicateTruckError() {

    $manager = TestData::EMPLOYEES[TestData::MANAGER1000];
    $ticket = Test::postLogin($this, $manager['email'], $manager['password']);
    
    // Create first truck
    $post = Vehicles::TRUCK_TRC120_6x2;
    $response = Test::post("equipment/truck", $ticket, $post);
    $truck = Test::assertResponse1($this, $response, "Truck found");

    // post dublicate number
    $post = Vehicles::TRUCK_TRC120_6x2;
    $post['vin'] = '1XXXXP8X2XX3XXXX0'; // change and skip VIN dublication
    
    // Create second truck with duplicated number
    $response = Test::post("equipment/truck", $ticket, $post);
    // $this->assertEquals(400, $response['status'], "Truck number already exist" . Test::printr($response));
    $getTruck = Test::assertRequestErrorResponse($this, $response, "Truck not created" . Test::print($response));
    $this->assertArrayHasKey('errors', $response['result']);
    $this->assertArrayHasKey('number', $response['result']['errors']);

    // post dublicate VIN
    $post = Vehicles::TRUCK_TRC120_6x2;
    $post['number'] = 'TRC1230'; // change and skip number dublication
    
    // Create third truck with same vin
    $response = Test::post("equipment/truck", $ticket, $post);
    // $this->assertEquals(400, $response['status'], "Truck VIN already exist" . Test::printr($response));
    Test::assertRequestErrorResponse($this, $response, "Truck VIN already exist" . Test::printr($response));
    $this->assertArrayHasKey('errors', $response['result']);
    // $this->assertArrayHasKey('number', $response['result']['errors']);
    $this->assertArrayHasKey('vin', $response['result']['errors']);

    return ['ticket' => $ticket];
  }

  /**
   * @depends testCreateDublicateTruckError
   */
  public function testGetNonExistentTruckError ($test) {
    $ticket = $test['ticket'];
    $truckId = 999; //Random truck ID that should not exist

    $response = Test::get("equipment/truck/$truckId", $ticket);
    // $this->assertEquals(400, $response['status'], "Truck found " . Test::printr($response));
    Test::assertRequestErrorResponse($this, $response, "Truck found " . Test::printr($response));

    // return['ticket' => $ticket, 'truckId' => $truckId];
    return $test;
  }

  /**
   * @depends testGetNonExistentTruckError
   */
  public function testSaveNonExistentTruckError($test) {
    $ticket = $test['ticket'];
    $truckId = 999; //Random truck ID that should not exist
    $truck = Vehicles::TRUCK_TRCO20_6x2;

    $response = Test::put("equipment/truck/$truckId", $ticket, $truck);
    Test::assertRequestErrorResponse($this, $response, "Truck not Saved " . Test::printr($response));
    $this->assertArrayNotHasKey('errors', $response['result'], Test::printr($response));
    // $responseErrors = $response['result']['errors'];
    // $this->assertTrue(!empty($responseErrors), "Errors array not empty");
    // $this->assertArrayHasKey('number', $responseErrors);
    // $this->assertArrayHasKey('make', $responseErrors);
    // $this->assertArrayHasKey('type', $responseErrors);
    // $this->assertArrayHasKey('axle', $responseErrors);

    // return ['ticket' => $ticket, 'truckId' => $truckId, 'truck' => $truck];
    return $test;
  }

  /**
   * @depends testSaveNonExistentTruckError
   */
  public function testSaveWithEmptyData($test) {
    $ticket = $test['ticket'];
    // $truckId = $test['truckId'];
    $truckId = 999; //Truck number not checked because empty data 
    $truck = [];

    $response = Test::put("equipment/truck/$truckId", $ticket, $truck);
    $this->assertArrayHasKey('errors', $response['result'], Test::printr($response));
    $errors = Test::assertRequestErrorResponse($this, $response, "Truck not Saved " . Test::printr($response));
    
    $this->assertTrue(!empty($errors), "Errors array not empty");
    $this->assertArrayHasKey('number', $errors);
    $this->assertArrayHasKey('make', $errors);
    $this->assertArrayHasKey('type', $errors);
    $this->assertArrayHasKey('axle', $errors);

    // return ['ticket' => $ticket, 'truckId' => $truckId, 'truck' => $truck];
    return $test;
  }

  /**
   * @depends testSaveNonExistentTruckError
   */
  public function testUpdateNonExistentTruckError ($test) {
    $ticket = $test['ticket'];
    $truckId = 999;
    $truck = Vehicles::TRUCK_TRC1O0_6x2;
    $truck['number'] = "TRK1234";

    $response = Test::put("equipment/truck/$truckId", $ticket, $truck);
    // $this->assertEquals(400, $response['status'], "Truck updated " . Test::printr($response));
    Test::assertRequestErrorResponse($this, $response, "Truck updated " . Test::printr($response));
    // $this->assertArrayHasKey('errors', $response['result']); // ?
    $this->assertArrayNotHasKey('errors', $response['result']); 
    // $responseErrors = $response['result']['errors'];
    // $this->assertTrue(!empty($responseErrors), "Errors array not empty");
    // $this->assertArrayHasKey('make', $responseErrors);
    // $this->assertArrayHasKey('type', $responseErrors);
    // $this->assertArrayHasKey('axle', $responseErrors);

    // return ['ticket' => $ticket, 'truckId' => $truckId];
    return $test;
  }

  /**
   * @depends testUpdateNonExistentTruckError
   */
  public function testDeletedNonExistentTruckError($test) {
    $ticket = $test['ticket'];
    // $truckId = $test['truckId']; 
    $truckId = 998; 

    $response = Test::delete("equipment/truck/$truckId", $ticket);
    // $this->assertEquals(400, $response['status'], "Truck deleted " . Test::printr($response)); 
    Test::assertRequestErrorResponse($this, $response, "Truck deleted " . Test::printr($response));

    return $test;
  }

  /**
   *
   * @depends testCreateDublicateTruckError
   */
  public function testCreateDublicateTrailerError($test) {

    $ticket = $test['ticket'];

    $post = [
      'id' => null,
      'vin' => '1TTTXP2120JXXXXX1',
      'number' => 'TRL212P',
      'type' => 'dvan',
      'makeId' => '12',
      'axle' => '2',
      'trailerLength' => '53',
    ];

    $response = Test::post("equipment/trailer", $ticket, $post);
    Test::assertResponse1($this, $response, "Trailer created");
    //$trailer = $response['result']['data'];


    $post = [
      'id' => null,
      'vin' => '1TTTXP2120JXXXXXX',
      'number' => 'TRL212P',
      'type' => 'dvan',
      'makeId' => '12',
      'axle' => '2',
      'trailerLength' => '53',
    ];

    // post dublicate number
    $response = Test::post("equipment/trailer", $ticket, $post);
    // $this->assertEquals(400, $response['status'], "Trailer already exist" . Test::printr($response)); 
    Test::assertRequestErrorResponse($this, $response, "Trailer already exist" . Test::printr($response)); 
    $this->assertArrayHasKey('errors', $response['result']); 
    $this->assertArrayHasKey('number', $response['result']['errors']);
    //$this->assertArrayHasKey('vin', $response['result']['errors']);

    $post = [
      'id' => null,
      'vin' => '1TTTXP212XJXXXXX1',
      'number' => 'TRL2120M',
      'type' => 'dvan',
      'makeId' => '12',
      'axle' => '2',
      'trailerLength' => '53',
    ];

    // post dublicate VIN
    $response = Test::post("equipment/trailer", $ticket, $post);
    // $this->assertEquals(400, $response['status'], "Trailer already exist" . Test::printr($response));
    Test::assertRequestErrorResponse($this, $response, "Trailer already exist" . Test::printr($response));
    $this->assertArrayHasKey('errors', $response['result']);
    //$this->assertArrayHasKey('number', $response['result']['errors']);
    $this->assertArrayHasKey('vin', $response['result']['errors']);

    // return ['ticket' => $ticket];
    return $test;
  }

  /**
   * @depends testCreateDublicateTrailerError
   */
  public function testGetNonExistentTrailerError($test) {
    $ticket = $test['ticket'];
    $trailerId = 999; //Such ID should not exist.

    $response = Test::get("equipment/trailer/$trailerId", $ticket);
    // $this->assertEquals(400, $response['status'], "Truck found " . Test::printr($response));
    Test::assertRequestErrorResponse($this, $response, "Truck found " . Test::printr($response));
    // return ['ticket' => $ticket, 'trailerId' => $trailerId];
    return $test;
  }

  /**
   * @depends testGetNonExistentTrailerError
   */
  public function testSaveNonExistentTrailerError($test) {
    $ticket = $test['ticket'];
    $trailerId = 999; //Such ID should not exist.
    // $trailer = [];
    $trailer = Vehicles::TRAILER_TRL2120_DVAN_53;

    $response = Test::put("equipment/trailer/$trailerId", $ticket, $trailer);
    // $this->assertEquals(400, $response['status'], "Trailer saved " . Test::printr($response));
    Test::assertRequestErrorResponse($this, $response, "Trailer saved " . Test::printr($response));
    // $this->assertArrayHasKey('errors', $response['result']);
    $this->assertArrayNotHasKey('errors', $response['result'], Test::printr($response));
    // $responseErrors = $response['result']['errors'];
    // $this->assertTrue(!empty($responseErrors), "Errors array not empty");
    // $this->assertArrayHasKey('number', $responseErrors);
    // $this->assertArrayHasKey('make', $responseErrors);
    // $this->assertArrayHasKey('trailerLength', $responseErrors);
    // $this->assertArrayHasKey('type', $responseErrors);
    // $this->assertArrayHasKey('axle', $responseErrors);

    // return ['ticket' => $ticket, 'trailerId' => $trailerId, 'trailer' => $trailer];
    return $test;
  }

  /**
  * @depends testSaveNonExistentTrailerError
  */
  public function testSaveEmptyTrailerDataError($test) {
    $ticket = $test['ticket'];
    $trailerId = 999; //Trailer number not checked because empty data 
    $trailer = [];

    $response = Test::put("equipment/trailer/$trailerId", $ticket, $trailer);
    $this->assertArrayHasKey('errors', $response['result'], Test::printr($response));
    $errors = Test::assertRequestErrorResponse($this, $response, "Trailer not Saved " . Test::printr($response));
    
    $this->assertTrue(!empty($errors), "Errors array not empty");
    $this->assertArrayHasKey('number', $errors);
    $this->assertArrayHasKey('make', $errors);
    $this->assertArrayHasKey('trailerLength', $errors);
    $this->assertArrayHasKey('type', $errors);
    $this->assertArrayHasKey('axle', $errors);

     // return ['ticket' => $ticket, 'trailerId' => $trailerId, 'trailer' => $trailer];
    return $test;
  }

  /**
   * @depends testSaveNonExistentTrailerError
   */
  public function testUpdateNonExistentTrailerError($test) {
    $ticket = $test['ticket'];
    // $trailerId = $test['trailerId'];
    $trailerId = 999;
    // $trailer = $test['trailer'];
    $trailer = Vehicles::TRAILER_TRL2120_DVAN_53;
    $trailer['number'] = "TRL1234";

    $response = Test::put("equipment/trailer/$trailerId", $ticket, $trailer);
    // $this->assertEquals(400, $response['status'], "Trailer saved " . Test::printr($response));
    Test::assertRequestErrorResponse($this, $response, "Trailer saved " . Test::printr($response));
    // $this->assertArrayHasKey('errors', $response['result']);
    $this->assertArrayNotHasKey('errors', $response['result']);
    // $responseErrors = $response['result']['errors'];
    // $this->assertTrue(!empty($responseErrors), "Errors array not empty");
    // $this->assertArrayHasKey('make', $responseErrors);
    // $this->assertArrayHasKey('trailerLength', $responseErrors);
    // $this->assertArrayHasKey('type', $responseErrors);
    // $this->assertArrayHasKey('axle', $responseErrors);

    // return ['ticket' => $ticket, 'trailerId' => $trailerId];
    return $test;
  }

  /**
   * @depends testUpdateNonExistentTrailerError
   */
  public function testDeletedNonExistentTrailerError($test) {
    $ticket = $test['ticket'];
    // $trailerId = $test['trailerId']; 
    $trailerId = 999;

    $response = Test::delete("equipment/trailer/$trailerId", $ticket);
    // $this->assertEquals(400, $response['status'], "Trailer deleted " . Test::printr($response));
    Test::assertRequestErrorResponse($this, $response, "Trailer deleted " . Test::printr($response));
    
    return $test;
  }
}