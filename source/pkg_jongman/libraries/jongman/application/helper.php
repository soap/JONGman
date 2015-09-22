<?php
defined('_JEXEC') or die();


/**
 * Projectfork Application Helper Class
 *
*/
abstract class RFApplicationHelper
{
	/**
	 * Holds the Projectfork related components
	 *
	 * @var    array
	 */
	protected static $components;


	/**
	 * URL routing cache
	 *
	 * @var    array
	 */
	protected static $routes;


	/**
	 * Method to get all projectfork related components
	 * (starting with com_pf)
	 *
	 * @return    array
	 */
	public static function getComponents()
	{
		if (is_array(self::$components)) {
			return self::$components;
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('extension_id, element, client_id, enabled, access, protected')
		->from('#__extensions')
		->where($db->qn('type') . ' = ' . $db->quote('component'))
		->where('(' . $db->qn('element') . ' = '  . $db->quote('com_jongman')
				. ' OR ' . $db->qn('element') . ' LIKE ' . $db->quote('com_pf%')
				. ')'
		)
		->order('extension_id ASC');

		$db->setQuery($query);
		$items = (array) $db->loadObjectList();
		$com   = array();

		foreach ($items AS $item)
		{
			$el = $item->element;

			$com[$el] = $item;
		}

		self::$components = $com;

		return self::$components;
	}


	/**
	 * Method to check if a component exists or not
	 *
	 * @param     string     $name    The name of the component
	 *
	 * @return    boolean
	 */
	public static function exists($name)
	{
		$components = self::getComponents();

		if (!array_key_exists($name, $components)) {
			return false;
		}

		return true;
	}


	/**
	 * Method to check if a component is enabled or not
	 *
	 * @param     string    $name    The name of the component
	 *
	 * @return    mixed              Returns True if enabled, False if not, and NULL if not found.
	 */
	public static function enabled($name)
	{
		$components = self::getComponents();

		if (!array_key_exists($name, $components)) {
			return null;
		}

		if ($components[$name]->enabled == '0') {
			return false;
		}

		return true;
	}

	/**
	 * Find menu item link for specific needles
	 * @param string $needles
	 * @param string $com_view
	 * @return NULL
	 */
	public static function itemRoute($needles = null, $com_view = null)
	{
		$app       = JFactory::getApplication();
		$menus     = $app->getMenu('site');
		$com_name  = $app->input->get('option');
		$view_name = null;

		if ($com_view) {
			$parts = explode('.', $com_view);

			if (count($parts) == 2) {
				list($com_name, $view_name) = $parts;
			}
			else {
				$view_name = $com_view;
			}
		}

		// Prepare the reverse lookup array.
		if (!is_array(self::$routes)) {
			self::$routes = array();
		}

		if (!isset(self::$routes[$com_name])) {
			self::$routes[$com_name] = array();

			$component = JComponentHelper::getComponent($com_name);
			// get all menu items of specified component
			$items     = $menus->getItems('component_id', $component->id);

			foreach ($items as $item)
			{
				if (isset($item->query) && isset($item->query['view'])) {
					$view = $item->query['view'];

					if (!isset(self::$routes[$com_name][$view])) {
						self::$routes[$com_name][$view] = array();
					}

					if (isset($item->query['id'])) {
						self::$routes[$com_name][$view][$item->query['id']] = $item->id;
					}
					else {
						self::$routes[$com_name][$view][0] = $item->id;
					}
				}
			}
		}

		if ($needles) {
			foreach ($needles as $view => $ids)
			{
				if (isset(self::$routes[$com_name][$view])) {
					foreach($ids as $id)
					{
						if (isset(self::$routes[$com_name][$view][(int)$id])) {
							return self::$routes[$com_name][$view][(int)$id];
						}
					}
				}
			}
		}
		else {
			$active = $menus->getActive();

			if ($active && $active->component == $com_name) {
				if ($com_view) {
					if (isset(self::$routes[$com_name][$view_name][0])) {
						return self::$routes[$com_name][$view_name][0];
					}
					elseif ($com_view && isset($active->query['view']) && $active->query['view'] != $com_view) {
						return null;
					}
				}

				return $active->id;
			}
			else {
				if ($com_view) {
					if (isset(self::$routes[$com_name][$view_name][0])) {
						return self::$routes[$com_name][$view_name][0];
					}
				}
			}
		}

		return null;
	}
	
	/**
	 * @return string timezone string
	 */
	public static function getUserTimezone($uid = null, $default=null)
	{
		if ($uid === null) {
			$user = JFactory::getUser();
		}else{
			$user = JFactory::getUser($uid);
		}
		
		if ($default === null) {
			$default = JFactory::getConfig()->get('offset', 'UTC');
		}
		
		$timezone = $user->getParam('timezone', $default);
		
		return $timezone;
	}
	
	/**
	 * @return string timezone string
	 */
	public static function getServerTimezone()
	{
		return JFactory::getConfig()->get('offset', 'UTC');
	}
}
