<?php

class VehicleManagementTest extends UpdateTestCase {

  public static function setUpBeforeClass() {
    parent::setUpBeforeClass();
    Test::setData();
  }

  public function testCreateTrailer() {
    $manager = TestData::EMPLOYEES[TestData::MANAGER1000];
    $ticket = Test::postLogin($this, $manager['email'], $manager['password']);

    $post = [
      'id' => null,
      'vin' => '1TTTXP21X0J3XXXX1',
      'number' => 'TRL2120',
      'type' => 'dvan',
      'makeId' => '12',
      'axle' => '2',
      'trailerLength' => '53',
    ];

    $response = Test::post("equipment/trailer", $ticket, $post);
    $trailer = Test::assertResponse1($this, $response, "Trailer created");

    // return ['ticket' => $ticket, 'id' => $trailer['id'], 'trailer' => $post];
    $test = ['ticket' => $ticket, 'id' => $trailer['id'], 'trailer' => $post]; //???
    return $test;
  }

  /**
   *
   * @depends testCreateTrailer
   */
  public function testGetTrailer($test) {

    $ticket = $test['ticket'];
    $id = $test['id'];
    $trailer = $test['trailer'];

    $response = Test::get("equipment/trailer/$id", $ticket);
    $getTrailer = Test::assertResponse1($this, $response, "Trailer found");

    //Comparing that get truck trailer data is same as what was posted
    $this->assertTrue($trailer['vin'] === $getTrailer['vin'], "Trailer VIN's match " . $trailer['vin'] ." == ". $getTrailer['vin']);
    $this->assertTrue($trailer['number'] === $getTrailer['number'], "Trailer NUMBERS match " . $trailer['number'] ." == ". $getTrailer['number']);
    $this->assertTrue($trailer['type'] === $getTrailer['type'], "Trailer TYPES match " . $trailer['type'] ." == ". $getTrailer['type']);
    $this->assertTrue($trailer['makeId'] == $getTrailer['makeId'], "Trailer MAKE ID's match " . $trailer['makeId'] ." == ". $getTrailer['makeId']); //== not === because different types?
    $this->assertTrue($trailer['axle'] === $getTrailer['axle'], "Trailer AXLE's match " . $trailer['axle'] ." == ". $getTrailer['axle']);
    $this->assertTrue($trailer['trailerLength'] == $getTrailer['trailerLength'], "Trailer LENGHTS match " . $trailer['trailerLength'] ." == ". $getTrailer['trailerLength']); //Also == because different types

    // return ['ticket' => $ticket, 'trailer' => $response['result']['data']];
    // $test['trailer'] = $response['result']['data'];
    $test['trailer'] = $getTrailer;
    return $test;
  }

  /**
   *
   * @depends testGetTrailer
   */
  public function testSaveTrailer($test) {

    $ticket = $test['ticket'];
    $trailer = $test['trailer'];

    $response = Test::put("equipment/trailer/" . $trailer['id'], $ticket, $trailer);
    Test::assertResponse1($this, $response, "Trailer saved");

    // return ['ticket' => $ticket];
    return $test;
  }

  /**
   *
   * @depends testGetTrailer
   */
  public function testCreateSecondTrailer($test) {

    $ticket = $test['ticket'];
    $trailer1 = $test['trailer'];

    $post = [
       'id' => null,
       'vin' => '1TTTXP200XJXXXXX1',
       'number' => 'TRL2001',
       'type' => 'reef',
       'makeId' => '12',
       'axle' => '2',
       'trailerLength' => '48',
    ];
 
    $response = Test::post("equipment/trailer", $ticket, $post);
    $trailer2 = Test::assertResponse1($this, $response, "Trailer created");
 
   $test = ['ticket' => $ticket, 'id' => $trailer2['id'], 'trailer2' => $post, 'trailer1' => $trailer1];
   return $test;
 
  }
 
   /**
    *
    * @depends testCreateSecondTrailer
    */
   public function testGetSecondTrailer($test) {
 
     $ticket = $test['ticket'];
     $id = $test['id'];
     $trailer2 = $test['trailer2'];
 
     $response = Test::get("equipment/trailer/$id", $ticket);
     $getTrailer = Test::assertResponse1($this, $response, "Trailer found");
 
     //Comparing that get trailer data is same as what was posted
     $this->assertTrue($trailer2['vin'] === $getTrailer['vin'], "Trailer VIN's match " . $trailer2['vin'] ." == ". $getTrailer['vin']);
     $this->assertTrue($trailer2['number'] === $getTrailer['number'], "Trailer NUMBERS match " . $trailer2['number'] ." == ". $getTrailer['number']);
     $this->assertTrue($trailer2['type'] === $getTrailer['type'], "Trailer TYPES match " . $trailer2['type'] ." == ". $getTrailer['type']);
     $this->assertTrue($trailer2['makeId'] == $getTrailer['makeId'], "Trailer MAKE ID's match " . $trailer2['makeId'] ." == ". $getTrailer['makeId']); //== not === because different types?
     $this->assertTrue($trailer2['axle'] === $getTrailer['axle'], "Trailer AXLE's match " . $trailer2['axle'] ." == ". $getTrailer['axle']);
     $this->assertTrue($trailer2['trailerLength'] == $getTrailer['trailerLength'], "Trailer LENGHTS match " . $trailer2['trailerLength'] ." == ". $getTrailer['trailerLength']); //Also == because different types
 
     // return ['ticket' => $ticket, 'trailer' => $response['result']['data']];
     // $test['trailer2'] = $response['result']['data'];
     $test['trailer2'] =  $getTrailer;
     return $test;
   }
 
  /**
    *
    * @depends testGetSecondTrailer
    */
   public function testGetTwoTrailersList($test) {
 
    $ticket = $test['ticket'];
    
    $trailer1 = $test['trailer1'];
    $trailer2 = $test['trailer2'];
 
    // Get all trailers
    $response = Test::get("equipment/trailer", $ticket);
    $getTrailers = Test::assertResponse($this, $response, "Trailers found");

    $this->assertNotEmpty($getTrailers);

    // Check if two posted trailers matched By id from all trailers
    $trailer2Found = false;
  
    foreach($getTrailers as $trailer) {
    if ( $trailer['id'] === $trailer2['id'] ) {
      $trailer2Found = true;
      } 
    }

    $trailer1Found = false;

    foreach($getTrailers as $trailer) {
      if ( $trailer['id'] === $trailer1['id'] ) {
        $trailer1Found = true;
      } 
    }

    $this->assertTrue($trailer2Found, 'TrailerId ' . $trailer2['id']); 
    $this->assertTrue($trailer1Found, 'TrailerId ' . $trailer1['id']); 
 
    return $test;
   }

   /**
    *
    * @depends testGetSecondTrailer
    */

    public function testGetTrailersList($test) {
 
      $ticket = $test['ticket'];
      
      // Get all trailers
      $response = Test::get("equipment/trailer", $ticket);
      $getTrailers = Test::assertResponse($this, $response, "Trailers found");
  
      $this->assertNotEmpty($getTrailers);
  
      return $test;
     }

    /**
   * Check counted posted trailers list, currently should be false
   * 
   * @depends testGetTrailersList
   */

    public function testCountPostedTrailers($test) {
      $ticket = $test['ticket'];
  
      // Get all trailers
      $response = Test::get("equipment/trailer", $ticket);
      $getTrailers = Test::assertResponse($this, $response, "Trailers found"); 
  
      $this->assertNotEmpty($getTrailers);
  
      // Check posted trailers list equals to expected 2, currently should be false
      $this->assertEquals(2, count($getTrailers), 'Two posted trailers counted');
  
      return $test;
    }

  /**
   * @depends testGetTrailer
   */
  public function testUpdateTrailer($test) {

    $ticket = $test['ticket'];
    $trailer = $test['trailer'];
    // print_r($trailer);
    // Update trailer properties
    $trailer['number'] = "TRL1234";
    $trailer['vin'] = '1XKYDP9X2FX384000'; //
    $trailer['type'] = 'reef';
    $trailer['axle'] = '3';
    $trailer['model'] = 'X130';
    $trailer['year'] = '2009';
    $trailer['trailerLength'] = '46';

    $trailer['makeId'] = 10; 

    $response = Test::put("equipment/trailer/" . $trailer['id'], $ticket, $trailer);
    $updated = Test::assertResponse1($this, $response, "Trailer Updated");
    
    // Check if match
    $this->assertTrue($trailer['number'] === $updated['number'], "Trailer NUMBERS match " . $trailer['number'] ." == ". $updated['number']);
    $this->assertTrue($updated['vin'] === $trailer['vin'], "Trailer vin updated " . $updated['vin'] ." == ". $trailer['vin']); //
    $this->assertTrue($updated['type'] === $trailer['type'], "Trailer type updated " . $updated['type'] ." == ". $trailer['type']);
    $this->assertTrue($updated['axle'] === $trailer['axle'], "Trailer axle updated " . $updated['axle'] ." == ". $trailer['axle']);
    $this->assertTrue($updated['model'] === $trailer['model'], "Trailer model updated " . $updated['model'] ." == ". $trailer['model']);
    $this->assertTrue($updated['year'] === $trailer['year'], "Trailer year updated " . $updated['year'] ." == ". $trailer['year']);
    $this->assertTrue($updated['trailerLength'] == $trailer['trailerLength'], "Trailer trailerLength updated " . $updated['trailerLength'] ." == ". $trailer['trailerLength']);
    $this->assertTrue($updated['makeId'] == $trailer['makeId'], "Trailer makeId updated " . $updated['makeId'] ." == ". $trailer['makeId']);

    $test['updated'] = $updated;
    print_r($test['updated']);
    return $test;
  }

  /**
   * @depends testUpdateTrailer
   */
  public function testGetUpdatedTrailer ($test) {
    $ticket = $test['ticket'];
    $updated = $test['updated'];

    $response = Test::get("equipment/trailer/" . $updated['id'], $ticket);
    $getTrailer = Test::assertResponse1($this, $response, "Trailer found");

    //Comparing that get truck trailer data is same as what was posted
    $this->assertTrue($updated['id'] === $getTrailer['id'], "Trailer ID's match " . $updated['id'] ." == ". $getTrailer['id']);
    $this->assertTrue($updated['vin'] === $getTrailer['vin'], "Trailer VIN's match " . $updated['vin'] ." == ". $getTrailer['vin']);
    $this->assertTrue($updated['number'] === $getTrailer['number'], "Trailer NUMBERS match " . $updated['number'] ." == ". $getTrailer['number']);
    $this->assertTrue($updated['makeId'] == $getTrailer['makeId'], "Trailer MAKE ID's match " . $updated['makeId'] ." == ". $getTrailer['makeId']); //== not === because different types?
    $this->assertTrue($updated['makeName'] === $getTrailer['makeName'], "Trailer MAKE NAMES match " . $updated['makeName'] ." == ". $getTrailer['makeName']);
    $this->assertTrue($updated['vehicleType'] === $getTrailer['vehicleType'], "Trailer TYPES match " . $updated['vehicleType'] ." == ". $getTrailer['vehicleType']);
    $this->assertTrue($updated['vehicleTypeName'] === $getTrailer['vehicleTypeName'], "Trailer TYPE NAMES match " . $updated['vehicleTypeName'] ." == ". $getTrailer['vehicleTypeName']);
    $this->assertTrue($updated['type'] === $getTrailer['type'], "Trailer TYPES match " . $updated['type'] ." == ". $getTrailer['type']);
    $this->assertTrue($updated['typeName'] === $getTrailer['typeName'], "Trailer TYPE NAMES match " . $updated['typeName'] ." == ". $getTrailer['typeName']);
    $this->assertTrue($updated['trailerLength'] == $getTrailer['trailerLength'], "Trailer LENGHTS match " . $updated['trailerLength'] ." == ". $getTrailer['trailerLength']); //Also == because different types
    $this->assertTrue($updated['axle'] === $getTrailer['axle'], "Trailer AXLE's match " . $updated['axle'] ." == ". $getTrailer['axle']);

    // return ['ticket' => $ticket, 'trailer' => $getTrailer];
    $test['trailer'] = $getTrailer;
    return $test;
  }

  /**
   * @depends testGetUpdatedTrailer
   */
  public function testDeleteTrailer ($test) {
    $ticket = $test['ticket'];
    $trailer = $test['trailer'];

    $response = Test::delete("equipment/trailer/" . $trailer['id'], $ticket);
    Test::assertResponse($this, $response, "Trailer deleted");

    return $test;
  }

   /**
   * @depends testDeleteTrailer
   */
  public function testGetDeletedTrailer($test) {
    $ticket = $test['ticket'];
    $trailer = $test['trailer'];

    $response = Test::get("equipment/trailer/" . $trailer['id'], $ticket);
    $getTruck = Test::assertRequestErrorResponse($this, $response, "Trailer not found" . Test::print($response));

    return $test;
  }

}


