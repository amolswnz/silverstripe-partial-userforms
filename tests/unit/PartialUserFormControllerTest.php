<?php

namespace Firesphere\PartialUserforms\Tests;

use Firesphere\PartialUserforms\Controllers\PartialUserFormController;
use Firesphere\PartialUserforms\Models\PartialFieldSubmission;
use Firesphere\PartialUserforms\Models\PartialFormSubmission;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\Session;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\SapphireTest;

class PartialUserFormControllerTest extends SapphireTest
{
    /**
     * @var PartialUserFormController
     */
    protected $controller;

    public function setUp()
    {
        $this->controller = Injector::inst()->get(PartialUserFormController::class);
        parent::setUp();
    }

    public function testClassExists()
    {
        $this->assertInstanceOf(PartialUserFormController::class, $this->controller);
    }

    public function testSavePartialSubmissionExists()
    {
        $this->assertTrue(method_exists($this->controller, 'savePartialSubmission'));
    }

    public function testSavePartialSubmissionFormCreated()
    {
        $request = new HTTPRequest('GET', '/partialuserform', [], []);
        $session = new Session(['hi' => 'bye']);
        $request->setSession($session);

        $id = $this->controller->savePartialSubmission($request);

        $this->assertInternalType('numeric', $id);

        $form = PartialFormSubmission::get()->byID($id);

        $this->assertInstanceOf(PartialFormSubmission::class, $form);
    }

    public function testSavePartialSubmissionFieldCreated()
    {
        $request = new HTTPRequest('GET', '/partialuserform', [], ['Field1' => 'Value1']);
        $session = new Session(['hi' => 'bye']);
        $request->setSession($session);

        $id = $this->controller->savePartialSubmission($request);

        $fields = PartialFieldSubmission::get()->filter(['ParentID' => $id]);

        $this->assertEquals(1, $fields->count());
    }

    public function testPartialFormSubmissionExists()
    {
        $request = new HTTPRequest('GET', '/partialuserform', [], ['Field1' => 'Value1', 'Field2' => 'Value2']);
        $session = new Session(['hi' => 'bye']);
        $request->setSession($session);

        $id = $this->controller->savePartialSubmission($request);

        $session = $request->getSession();
        $request = new HTTPRequest('GET', '/partialuserform', [], ['Field2' => 'Value2']);
        $request->setSession($session);

        $secondId = $this->controller->savePartialSubmission($request);

        $this->assertEquals($id, $secondId);
    }

    public function testPartialFormSubmissionExistingField()
    {
        $request = new HTTPRequest('GET', '/partialuserform', [],
            ['Field1' => 'Value1', 'Field2' => 'Value2', 'Field3' => 'null']);
        $session = new Session(['hi' => 'bye']);
        $request->setSession($session);

        $id = $this->controller->savePartialSubmission($request);
        $field3 = PartialFormSubmission::get()->byID($id)->PartialFields()->filter(['Name' => 'Field3'])->first();
        $this->assertEquals('null', $field3->Value);
        $session = $request->getSession();
        $request = new HTTPRequest('GET', '/partialuserform', [],
            ['Field1' => 'Value1', 'Field2' => 'Value2', 'Field3' => 'Value3']);
        $request->setSession($session);
        $this->controller->savePartialSubmission($request);

        $field3 = PartialFieldSubmission::get()->byID($field3->ID);
        $this->assertEquals('Value3', $field3->Value);
    }
}
