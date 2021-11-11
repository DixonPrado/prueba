<?php
/**
 * @package         Regular Labs Library
 * @version         21.11.1666
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://regularlabs.com
 * @copyright       Copyright © 2021 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace RegularLabs\Library;

defined('_JEXEC') or die;

/**
 * Class ObjectHelper
 * @package RegularLabs\Library
 */
class ObjectHelper
{
	/**
	 * Change the case of object keys
	 *
	 * @param object $object
	 * @param array  $key_format ('camel',  'dash', 'dot', 'underscore')
	 * @param bool   $to_lowercase
	 *
	 * @return object
	 */
	public static function changeKeyCase($object, $format, $to_lowercase = true)
	{
		return ArrayHelper::applyMethodToKeys(
			[$object, $format, $to_lowercase],
			'\RegularLabs\Library\StringHelper',
			'toCase'
		);
	}

	/**
	 * Deep clone an object
	 *
	 * @param object $object
	 *
	 * @return object
	 */
	public static function clone($object)
	{
		return unserialize(serialize($object));
	}

	/**
	 * Return the value by the object property key
	 * A list of keys can be given. The first one that is not empty will get returned
	 *
	 * @param object       $object
	 * @param string|array $keys
	 *
	 * @return mixed
	 */
	public static function getValue($object, $keys, $default = null)
	{
		$keys = ArrayHelper::toArray($keys);

		foreach ($keys as $key)
		{
			if (empty($object->{$key}))
			{
				continue;
			}

			return $object->{$key};
		}

		return $default;
	}

	/**
	 * Merge 2 objects
	 *
	 * @param object $object1
	 * @param object $object2
	 *
	 * @return object
	 */
	public static function merge($object1, $object2)
	{
		return (object) array_merge((array) $object1, (array) $object2);
	}
}
