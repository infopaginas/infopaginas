<?php

namespace Oxa\Sonata\AdminBundle\Twig;

use Oxa\Sonata\AdminBundle\Manager\AdminManager;

/**
 * Class AdminExtension
 * @package Oxa\Sonata\AdminBundle\Twig
 */
class AdminExtension extends \Twig_Extension
{
	/**
	 * @var AdminManager
	 */
	private $adminManager;

	/**
	 * AdminExtension constructor.
	 * @param AdminManager $adminManager
	 */
	public function __construct(AdminManager $adminManager)
	{
		$this->adminManager = $adminManager;
	}

	/**
	 * Returns the name of the extension.
	 *
	 * @return string The extension name
	 */
	public function getName()
	{
		return 'admin_extension';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFunctions()
	{
		return [
			'get_object_list' => new \Twig_SimpleFunction('get_object_list', [$this, 'getObjectList'])
		];
	}

	public function getObjectList($entityClass, array $idList)
	{
		return $this->adminManager->getObjectList($entityClass, $idList);
	}
}
