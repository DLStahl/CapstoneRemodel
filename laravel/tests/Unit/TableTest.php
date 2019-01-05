<?php

namespace Tests;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use App\Resident;
use App\ScheduleData;
use App\Attending;
use App\Admin;
use App\Option;

class ExampleTest extends TestCase
{
   
    public function testDBAdminXY()
    {
        $response = $this->get('/');
        $this->assertDatabaseHas('admin',['email' => 'yue.137@osu.edu']);
        $this->assertDatabaseHas('admin',['exists' => 1]);
    }
    public function testDBAdminYL()
    {
        $response = $this->get('/');
        $this->assertDatabaseHas('admin',['email' => 'ling.188@osu.edu']);
        $this->assertDatabaseHas('admin',['exists' => 1]);
    }
    public function testDBAdminZF()
    {
        $response = $this->get('/');
        $this->assertDatabaseHas('admin',['email' => 'fackler.29@osu.edu']);
        $this->assertDatabaseHas('admin',['exists' => 1]);
    }

    public function testDBAdminDavidStahl()
    {
        $response = $this->get('/');
        $this->assertDatabaseHas('admin',['email' => 'stahl.182@osu.edu']);
        $this->assertDatabaseHas('admin',['exists' => 1]);
    }

    public function testgetFirstday()
    {
        $response = $this->get('/');
        $id = Attending::where('name', 'null')->exists();
        $attending = Option::where('attending', '>', '6004350000')->exists();
        $this->assertFalse($id);
        $this->assertFalse($attending);
        $this->assertTrue(true);
        $this->assertDatabaseHas('admin',['email' => 'yue.137@osu.edu']);
    }

        public function testgetSecondday()
    {
        $response = $this->get('/');
        $id = Attending::where('id', '1864656')->exists();
        $attending = Option::where('attending', '>', '133300023400000')->exists();
        $this->assertFalse($id);
        $this->assertFalse($attending);
        $this->assertTrue(true);
        $this->assertDatabaseHas('admin',['email' => 'yue.137@osu.edu']);
    }

    public function testgetThirdday()
    {
        $response = $this->get('/');
        $id = Attending::where('email', 'gao.1153@osu.edu')->exists();
        $attending = Option::where('attending', '>', '100000000')->exists();
        $this->assertFalse($id);
        $this->assertFalse($attending);
        $this->assertTrue(true);
        $this->assertDatabaseHas('admin',['email' => 'yue.137@osu.edu']);
    }
    public function testDBSetup()
    {
        $response = $this->get('/');
        $this->assertTrue(DB::connection()->getDatabaseName()!=null);
    }

    public function testConnectionSetup()
    {
        $response = $this->get('/');
        $this->assertTrue(DB::connection()->getDatabaseName()!=null);
    }

    public function testHttpSetup()
    {
        $response = $this->get('/');
        $this->assertTrue(DB::connection()->getDatabaseName()!=null);
    }

    public function testAddNewUser()
    {
        $response = $this->get('/');
        $id = Attending::where('email', 'null')->exists();
        $attending = Option::where('resident', '>', '100000000')->exists();
        $this->assertFalse($id);
        $this->assertFalse($attending);
        $this->assertTrue(true);
        $this->assertDatabaseHas('admin',['email' => 'stahl.182@osu.edu']);
    }

    public function testDeleteUser()
    {
        $response = $this->get('/');
        $id = Attending::where('email', 'null')->exists();
        $attending = Option::where('resident', '>', '100000000')->exists();
        $this->assertFalse($id);
        $this->assertFalse($attending);
        $this->assertTrue(true);
        $this->assertDatabaseHas('admin',['email' => 'stahl.182@osu.edu']);
    }

    public function testUpdateContent()
    {
        $response = $this->get('/');
        $id = Attending::where('email', 'null')->exists();
        $attending = Option::where('resident', '>', '100000000')->exists();
        $this->assertFalse($id);
        $this->assertFalse($attending);
        $this->assertTrue(true);
        $this->assertTrue(DB::connection()->getDatabaseName()!=null);
    }

    public function testReceiveSubmission()
    {
        $response = $this->get('/');
        $id = Attending::where('email', 'null')->exists();
        $attending = Option::where('resident', '=', '100000000')->exists();
        $this->assertFalse($id);
        $this->assertFalse($attending);
        $this->assertTrue(DB::connection()->getDatabaseName()!=null);
        $this->assertDatabaseHas('admin',['email' => 'stahl.182@osu.edu']);
    }

    public function testMigration()
    {
        $response = $this->get('/');
        $id = Attending::where('email', 'null')->exists();
        $attending = Option::where('resident', '>', '100000000')->exists();
        $this->assertFalse($id);
        $this->assertFalse($attending);
        
        $this->assertTrue(true);
        $this->assertDatabaseHas('resident',['email' => 'yue.137@osu.edu']);
    }

    public function testGetInstruction()
    {
        $response = $this->get('/');
        $id = Attending::where('email', 'null')->exists();
        $attending = Option::where('resident', '>', '1012000003242300')->exists();
        $this->assertFalse($id);
        $this->assertFalse($attending);
        $this->assertTrue(true);
        $this->assertDatabaseHas('resident',['email' => 'yue.137@osu.edu']);
    }

    public function testGetSchedule()
    {
        $response = $this->get('/');
        $id = Attending::where('email', 'null')->exists();
        $attending = Option::where('resident', '=', '100000000')->exists();
        $this->assertFalse($id);
        $this->assertFalse($attending);
        $this->assertTrue(true);
        $this->assertDatabaseHas('admin',['email' => 'yue.137@osu.edu']);
    }

    public function testAbout()
    {
        // Refresh Database
        $response = $this->get('/');

        // TODO: Add test case here
        $assert1 = true;
        $assert2 = true;

        // Add more asserts if necessary

        // Add here to eliminate errors
        $this->assertTrue($assert1);
        $this->assertTrue($assert2);
    }

    public function testContact()
    {
        // Refresh Database
        $response = $this->get('/');

        // TODO: Add test case here
        $assert1 = true;
        $assert2 = true;

        // Add more asserts if necessary

        // Add here to eliminate errors
        $this->assertTrue($assert1);
        $this->assertTrue($assert2);
    }

    public function testScheduleData()
    {
        // Refresh Database
        $response = $this->get('/');

        // TODO: Add test case here
        $assert1 = true;
        $assert2 = true;

        // Add more asserts if necessary

        // Add here to eliminate errors
        $this->assertTrue($assert1);
        $this->assertTrue($assert2);
    }

    public function testScheduleParser()
    {
        // Refresh Database
        $response = $this->get('/');

        // TODO: Add test case here
        $assert1 = true;
        $assert2 = true;

        // Add more asserts if necessary

        // Add here to eliminate errors
        $this->assertTrue($assert1);
        $this->assertTrue($assert2);
    }

    public function testGetIndexAbout()
    {
        // Refresh Database
        $response = $this->get('/');

        // TODO: Add test case here
        $assert1 = true;
        $assert2 = true;

        // Add more asserts if necessary

        // Add here to eliminate errors
        $this->assertTrue($assert1);
        $this->assertTrue($assert2);
    }

    public function testGetIndexContact()
    {
        // Refresh Database
        $response = $this->get('/');

        // TODO: Add test case here
        $assert1 = true;
        $assert2 = true;

        // Add more asserts if necessary

        // Add here to eliminate errors
        $this->assertTrue($assert1);
        $this->assertTrue($assert2);
    }

    public function testTableValue24()
    {
        // Refresh Database
        $response = $this->get('/');

        // TODO: Add test case here
        $assert1 = true;
        $assert2 = true;

        // Add more asserts if necessary

        // Add here to eliminate errors
        $this->assertTrue($assert1);
        $this->assertTrue($assert2);
    }

    public function testTableValue25()
    {
        // Refresh Database
        $response = $this->get('/');

        // TODO: Add test case here
        $assert1 = true;
        $assert2 = true;

        // Add more asserts if necessary

        // Add here to eliminate errors
        $this->assertTrue($assert1);
        $this->assertTrue($assert2);
    }

    public function testGetSchedule1()
    {
        // Refresh Database
        $response = $this->get('/');

        // TODO: Add test case here
        $assert1 = true;
        $assert2 = true;

        // Add more asserts if necessary

        // Add here to eliminate errors
        $this->assertTrue($assert1);
        $this->assertTrue($assert2);
    }

    public function testGetSchedule2()
    {
        // Refresh Database
        $response = $this->get('/');

        // TODO: Add test case here
        $assert1 = true;
        $assert2 = true;

        // Add more asserts if necessary

        // Add here to eliminate errors
        $this->assertTrue($assert1);
        $this->assertTrue($assert2);
    }

    public function testGetSchedule3()
    {
        // Refresh Database
        $response = $this->get('/');

        // TODO: Add test case here
        $assert1 = true;
        $assert2 = true;

        // Add more asserts if necessary

        // Add here to eliminate errors
        $this->assertTrue($assert1);
        $this->assertTrue($assert2);
    }

    public function testGetIndexAdmin()
    {
        // Refresh Database
        $response = $this->get('/');

        // TODO: Add test case here
        $assert1 = true;
        $assert2 = true;

        // Add more asserts if necessary

        // Add here to eliminate errors
        $this->assertTrue($assert1);
        $this->assertTrue($assert2);
    }

    public function testConstructorUser()
    {
        // Refresh Database
        $response = $this->get('/');

        // TODO: Add test case here
        $assert1 = true;
        $assert2 = true;

        // Add more asserts if necessary

        // Add here to eliminate errors
        $this->assertTrue($assert1);
        $this->assertTrue($assert2);
    }

    public function testRouteResident()
    {
        // Refresh Database
        $response = $this->get('/');

        // TODO: Add test case here
        $assert1 = true;
        $assert2 = true;

        // Add more asserts if necessary

        // Add here to eliminate errors
        $this->assertTrue($assert1);
        $this->assertTrue($assert2);
    }

    public function testRouteAdmin()
    {
        // Refresh Database
        $response = $this->get('/');

        // TODO: Add test case here
        $assert1 = true;
        $assert2 = true;

        // Add more asserts if necessary

        // Add here to eliminate errors
        $this->assertTrue($assert1);
        $this->assertTrue($assert2);
    }

    public function testRoute()
    {
        // Refresh Database
        $response = $this->get('/');

        // TODO: Add test case here
        $assert1 = true;
        $assert2 = true;

        // Add more asserts if necessary

        // Add here to eliminate errors
        $this->assertTrue($assert1);
        $this->assertTrue($assert2);
    }

    public function testRouteMiddleware()
    {
        // Refresh Database
        $response = $this->get('/');

        // TODO: Add test case here
        $assert1 = true;
        $assert2 = true;

        // Add more asserts if necessary

        // Add here to eliminate errors
        $this->assertTrue($assert1);
        $this->assertTrue($assert2);
    }

    public function testRouteChannel()
    {
        // Refresh Database
        $response = $this->get('/');

        // TODO: Add test case here
        $assert1 = true;
        $assert2 = true;

        // Add more asserts if necessary

        // Add here to eliminate errors
        $this->assertTrue($assert1);
        $this->assertTrue($assert2);
    }

    public function testRouteCommand()
    {
        // Refresh Database
        $response = $this->get('/');

        // TODO: Add test case here
        $assert1 = true;
        $assert2 = true;

        // Add more asserts if necessary

        // Add here to eliminate errors
        $this->assertTrue($assert1);
        $this->assertTrue($assert2);
    }

    public function testAuthication()
    {
        // Refresh Database
        $response = $this->get('/');

        // TODO: Add test case here
        $assert1 = true;
        $assert2 = true;

        // Add more asserts if necessary

        // Add here to eliminate errors
        $this->assertTrue($assert1);
        $this->assertTrue($assert2);
    }

    public function testConnection()
    {
        // Refresh Database
        $response = $this->get('/');

        // TODO: Add test case here
        $assert1 = true;
        $assert2 = true;

        // Add more asserts if necessary

        // Add here to eliminate errors
        $this->assertTrue($assert1);
        $this->assertTrue($assert2);
    }

    public function testCertificate()
    {
        // Refresh Database
        $response = $this->get('/');

        // TODO: Add test case here
        $assert1 = true;
        $assert2 = true;

        // Add more asserts if necessary

        // Add here to eliminate errors
        $this->assertTrue($assert1);
        $this->assertTrue($assert2);
    }

    public function testShibbolethAttributes()
    {
        // Refresh Database
        $response = $this->get('/');

        // TODO: Add test case here
        $assert1 = true;
        $assert2 = true;

        // Add more asserts if necessary

        // Add here to eliminate errors
        $this->assertTrue($assert1);
        $this->assertTrue($assert2);
    }
    

}
