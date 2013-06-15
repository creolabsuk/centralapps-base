<?php
namespace CentralApps\Base;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->application = new Application($this->getMock('\CentralApps\Base\Containers\AbstractContainer'), __DIR__.'/test');
	}

	public function testCanDetectCliExecutionContext()
	{
		$this->assertEquals('cli', $this->application->getExecutionContext());
	}

	public function testCanGetContainerFromApplication()
	{
		$this->assertInstanceOf('\CentralApps\Base\Containers\AbstractContainer', $this->application->getContainer());
	}

	public function testCanGetApplicationRootFolder()
	{
		$this->assertEquals(__DIR__.'/test', $this->application->getApplicationRootFolder());
		$this->application = new Application($this->getMock('\CentralApps\Base\Containers\AbstractContainer'));
		$this->assertEquals(str_replace('unit/', '', __DIR__.'/'), $this->application->getApplicationRootFolder());
	}

	public function testCanOverrideConfigurationKey()
	{
		//
	}

	/**
	 * @covers CentralApps\Base\Application::boot
	 */
	public function testBootSequence()
	{
		//
	}

	public function testCanRegisterInvokableFunctions()
	{
		$this->application->registerInvokableFunction('testFunction', function(){ return 'test';});
		$this->assertEquals('test', $this->application->testFunction());
		$this->setExpectedException('\RuntimeException');
		$this->application->fakeFunction();
	}

	/**
	 * @covers CentralApps\Base\Application::registerServiceProvider
	 */
	public function testCanRegisterServiceProviders()
	{
		$service_provider = $this->getMock('\CentralApps\Base\ServiceProviders\ServiceProviderInterface');
		$service_provider->expects($this->once())->method('register');
		$service_provider->expects($this->once())->method('getBootPriority')->will($this->returnValue(1));
		$this->application->registerServiceProvider($service_provider);
	}
}