<?php

class VehicleManagementTest extends UpdateTestCase {

  public static function setUpBeforeClass() {
    parent::setUpBeforeClass();
    Test::setData();
  }

  public function testCreateTruck() {

    $manager = TestData::EMPLOYEES[TestData::MANAGER1000];
    $ticket = Test::postLogin($this, $manager['email'], $manager['password']);

    $post = Vehicles::TRUCK_TRC120_6x2;
    
    $response = Test::post("equipment/truck", $ticket, $post);
    Test::assertResponse1($this, $response, "Truck found");
    $truck = $response['result']['data'];

    $test = ['ticket' => $ticket, 'id' => $truck['id'], 'truck' => $post];
    return $test;
  }

  /**
   *
   * @depends testCreateTruck
   */
  public function testGetTruck($test) {

    $ticket = $test['ticket'];
    $truck = $test['truck'];
    $id = $test['id'];

    $response = Test::get("equipment/truck/$id", $ticket);
    $getTruck = Test::assertResponse1($this, $response, "Truck found");

    //Comparing created truck data matches the one from GET
    $this->assertTrue($getTruck['vin'] === $truck['vin'], "Truck VIN's match");
    $this->assertTrue($getTruck['number'] === $truck['number'], "Truck VIN's match");
    $this->assertTrue($getTruck['type'] === $truck['type'], "Truck TYPES match");
    $this->assertTrue($getTruck['makeId'] == $truck['makeId'], "Truck MAKE ID's match"); //From GET returns a int number, used for creation uses string
    $this->assertTrue($getTruck['axle'] === $truck['axle'], "Truck AXLE's match");

    
    $test['truck1'] = $getTruck;
    return $test;
  }

  /**
   *
   * @depends testGetTruck
   */
  public function testSaveTruck($test) { 

    $ticket = $test['ticket'];
    $truck = $test['truck1'];

    $response = Test::put("equipment/truck/" . $truck['id'], $ticket, $truck);
    Test::assertResponse1($this, $response, "Truck saved");

    return $test;
  }

  /**
   *
   * @depends testGetTruck
   */

  public function testCreateSecondTruck($test) {

    // $manager = TestData::EMPLOYEES[TestData::MANAGER1000];

    $ticket = $test['ticket'];
    $truck1 = $test['truck1'];

    $post = Vehicles::TRUCK_TRC640_6x4;
    
    $response = Test::post("equipment/truck", $ticket, $post);
    Test::assertResponse1($this, $response, "Second Truck found");
    $secondTruck = $response['result']['data'];

    $test = ['ticket' => $ticket, 'id' => $secondTruck['id'], 'truck2' => $post, 'truck1' => $truck1];
    return $test;
  }

  /**
   *
   * @depends testCreateSecondTruck
   */
  public function testGetSecondTruck($test) {

    $ticket = $test['ticket'];
    $truck = $test['truck2'];
    $id = $test['id'];

    $response = Test::get("equipment/truck/$id", $ticket);
    $getTruck = Test::assertResponse1($this, $response, "Truck 2 found"); 

    $this->assertNotEmpty($getTruck['id']);

    //Comparing created truck data matches the one from GET
    $this->assertTrue($getTruck['vin'] === $truck['vin'], "Truck 2 VIN's match"); 
    $this->assertTrue($getTruck['number'] === $truck['number'], "Truck 2 VIN's match");
    $this->assertTrue($getTruck['type'] === $truck['type'], "Truck 2 TYPES match");
    $this->assertTrue($getTruck['makeId'] == $truck['makeId'], "Truck 2 MAKE ID's match"); //From GET returns a int number, used for creation uses string
    $this->assertTrue($getTruck['axle'] === $truck['axle'], "Truck 2 AXLE's match");

    $test['truck2'] = $getTruck;
    return $test;
  }

 /**
   *
   * @depends testGetSecondTruck 
   */
  public function testGetTwoTrucksList($test) {

    $ticket = $test['ticket'];

    $truck1 = $test['truck1'];
    $truck2 = $test['truck2'];

    // Get all trucks 
    $response = Test::get("equipment/truck", $ticket);
    $getTrucks = Test::assertResponse($this, $response, "Trucks found"); 
    
    $this->assertNotEmpty($getTrucks);

    // Check if two posted trucks matched By id from all trucks
    $truck2Found = false;
   
    foreach($getTrucks as $truck) {
     if ( $truck['id'] === $truck2['id'] ) {
       $truck2Found = true;
      } 
     }

     $truck1Found = false;

     foreach($getTrucks as $truck) {
      if ( $truck['id'] === $truck1['id'] ) {
        $truck1Found = true;
       } 
      }

     $this->assertTrue($truck2Found, 'TruckId ' . $truck2['id']); 
     $this->assertTrue($truck1Found, 'TruckId ' . $truck1['id']); 

    return $test;
  }

  /**
   *
   * @depends testGetSecondTruck 
   */

  public function testGetTrucksList($test) {
    
    $ticket = $test['ticket'];
    
    // Get all trucks
    $response = Test::get("equipment/truck", $ticket);
    $getTrucks = Test::assertResponse($this, $response, "Trucks found"); 
    // echo "trucks found" . PHP_EOL . Test::print($getTrucks); 

    $this->assertNotEmpty($getTrucks);

    return $test;
  }
 
  /**
   * Check counted posted trucks list, currently should be false
   * 
   * @depends testGetTrucksList
   */
   
  public function testCountPostedTrucks($test) {
    $ticket = $test['ticket'];

    // Get all trucks
    $response = Test::get("equipment/truck", $ticket);
    $getTrucks = Test::assertResponse($this, $response, "Trucks found"); 

    $this->assertNotEmpty($getTrucks);

    // Check posted trucks list equals to expected 2, currently should be false
    $this->assertEquals(2, count($getTrucks), 'Two posted trucks counted');

    return $test;

  }

  /**
   * @depends testGetTruck
   */
  public function testUpdateTruck($test) {

    $ticket = $test['ticket'];
    $truck = $test['truck1'];
    // print_r($truck);
    // Update truck properties
    $truck['number'] = "TRK1234";
    $truck['vin'] = '1XKYDP9X2FJ384299'; //
    $truck['type'] = 'str';
    $truck['axle'] = '6x4';
    $truck['model'] = 'X120';
    $truck['year'] = '2010';
    $truck['makeId'] = '11'; 

    $response = Test::put("equipment/truck/" . $truck['id'], $ticket, $truck);
    $updated = Test::assertResponse1($this, $response, "Truck updated");

    $this->assertTrue($updated['number'] === $truck['number'], "Truck number updated " . $updated['number'] ." == ". $truck['number']);
    $this->assertTrue($updated['vin'] === $truck['vin'], "Truck vin updated " . $updated['vin'] ." == ". $truck['vin']); //
    $this->assertTrue($updated['type'] === $truck['type'], "Truck type updated " . $updated['type'] ." == ". $truck['type']);
    $this->assertTrue($updated['axle'] === $truck['axle'], "Truck axle updated " . $updated['axle'] ." == ". $truck['axle']);
    $this->assertTrue($updated['model'] === $truck['model'], "Truck model updated " . $updated['model'] ." == ". $truck['model']);
    $this->assertTrue($updated['year'] === $truck['year'], "Truck year updated " . $updated['year'] ." == ". $truck['year']);
    $this->assertTrue($updated['makeId'] == $truck['makeId'], "Truck makeId updated " . $updated['makeId'] ." == ". $truck['makeId']);

    $test = ['ticket' => $ticket, 'truck' => $truck, 'updated' => $updated];
    // print_r($test['updated']);
    return $test;
  }

  /**
   * @depends testUpdateTruck
   */
  public function testGetUpdatedTruck($test) {
    $ticket = $test['ticket'];
    $updated = $test['updated'];

    $response = Test::get("equipment/truck/" . $updated['id'], $ticket);
    $getTruck = Test::assertResponse1($this, $response, "Truck found");

    //Comparing that updated data matches the one returned from GET
    $this->assertTrue($updated['id'] === $getTruck['id'], "Truck ID's match " . $updated['id'] ." == ". $getTruck['id']);
    $this->assertTrue($updated['vin'] === $getTruck['vin'], "Truck VIN's match " . $updated['vin'] ." == ". $getTruck['vin']);
    $this->assertTrue($updated['number'] === $getTruck['number'], "Truck NUMBERS match " . $updated['number'] ." == ". $getTruck['number']);
    $this->assertTrue($updated['makeId'] === $getTruck['makeId'], "Truck MAKE ID's match " . $updated['makeId'] ." == ". $getTruck['makeId']);
    $this->assertTrue($updated['makeName'] === $getTruck['makeName'], "Truck MAKE NAMES match " . $updated['makeName'] ." == ". $getTruck['makeName']);
    $this->assertTrue($updated['vehicleType'] === $getTruck['vehicleType'], "Truck TYPES match " . $updated['vehicleType'] ." == ". $getTruck['vehicleType']);
    $this->assertTrue($updated['vehicleTypeName'] === $getTruck['vehicleTypeName'], "Truck TYPE NAMES match " . $updated['vehicleTypeName'] ." == ". $getTruck['vehicleTypeName']);
    $this->assertTrue($updated['type'] === $getTruck['type'], "Truck TYPES match " . $updated['type'] ." == ". $getTruck['type']);
    $this->assertTrue($updated['typeName'] === $getTruck['typeName'], "Truck TYPE NAMES match " . $updated['typeName'] ." == ". $getTruck['typeName']);
    $this->assertTrue($updated['duty'] === $getTruck['duty'], "Truck DUTY's match " . $updated['duty'] ." == ". $getTruck['duty']);
    $this->assertTrue($updated['dutyName'] === $getTruck['dutyName'], "Truck DUTY NAMES match " . $updated['dutyName'] ." == ". $getTruck['dutyName']);
    $this->assertTrue($updated['axle'] === $getTruck['axle'], "Truck AXLE's match " . $updated['axle'] ." == ". $getTruck['axle']);

    $test['truck'] = $getTruck;
    return $test;
  }

  /**
   * @depends testGetUpdatedTruck
   */
  public function testDeleteTruck($test) {
    $ticket = $test['ticket'];
    $truck = $test['truck'];

    $response = Test::delete("equipment/truck/" . $truck['id'], $ticket);
    Test::assertResponse($this, $response, "Truck deleted");

    return $test;
  }

  /**
   * @depends testDeleteTruck
   */
  public function testGetDeletedTruck($test) {
    $ticket = $test['ticket'];
    $truck = $test['truck'];

    $response = Test::get("equipment/truck/" . $truck['id'], $ticket);
    $getTruck = Test::assertRequestErrorResponse($this, $response, "Truck not found" . Test::print($response));

    return $test;
  }
}