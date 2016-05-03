<?php

namespace Application\Sonata\AdminBundle\Model;

/**
 * Used for copping objects
 *
 * Interface CopyableEntityInterface
 * @package Application\Sonata\AdminBundle\Model
 */
interface CopyableEntityInterface
{
	/**
	 * Choose a property, which will be used to make a copied object different by adding prefix
	 * @return mixed
	 */
	public function getMarkCopyPropertyName();
}
