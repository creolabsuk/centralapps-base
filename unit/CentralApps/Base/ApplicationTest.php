<?php
namespace CentralApps\Base;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->application = new Application($this->getMock('\CentralApps\Base\Containers\AbstractContainer'), __DIR__.'/');
	}

	public function testCanDetectCliExecutionContext()
	{
		$this->assertEquals('cli', $this->application->getExecutionContext());
	}

	/**
	 * @covers CentralApps\Base\Application::boot
	 */
	public function testBootSequence()
	{
		//
	}

	/**
	 * @covers CentralApps\Base\Application::registerServiceProvider
	 */
	public function testCanRegisterServiceProviders()
	{
		$service_provider = $this->getMock('ServiceProviders\ServiceProviderInterface');
		$service_provider->expects($this->once())->method('register');
		$service_provider->expects($this->once())->method('getBootPriority')->will($this->returnValue(1));
		$this->application->registerServiceProvider($service_provider);
	}
}