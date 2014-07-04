<?php

namespace TokenPost\Widget;

/**
 * Widget is the base class for widgets.
 *
 * @property string $id ID of the widget.
 * @property \yii\web\View $view The view object that can be used to render views or view files. Note that the
 * type of this property differs in getter and setter. See [[getView()]] and [[setView()]] for details.
 * @property string $viewPath The directory containing the view files for this widget. This property is
 * read-only.
 *
 * @author Renato Cassino <renatocassino@gmail.com>
 * @version 1.0
 */
class Widget
{
    /**
     * @var Widget[] the widgets that are currently being rendered (not ended). This property
     * is maintained by [[begin()]] and [[end()]] methods.
     * @internal
     */
    public static $stack = [];
    
    public static $objectConfig = [];

    /**
	 * Begins a widget.
	 * This method creates an instance of the calling class. It will apply the configuration
	 * to the created instance. A matching [[end()]] call should be called later.
	 * @param array $config name-value pairs that will be used to initialize the object properties
	 * @return static the newly created widget instance
	 */
	public static function begin($config = [])
	{
		$config['class'] = get_called_class();
		/** @var Widget $widget */
		$widget = self::createObject($config);
		self::$stack[] = $widget;
		$widget->init();
		return $widget;
	}

    /**
	 * Ends a widget.
	 * Note that the rendering result of the widget is directly echoed out.
	 * @return static the widget instance that is ended.
	 * @throws InvalidCallException if [[begin()]] and [[end()]] calls are not properly nested
	 */
	public static function end()
	{
		if (!empty(self::$stack)) {
			$widget = array_pop(self::$stack);
			if (get_class($widget) === get_called_class()) {
				$widget->run();
				return $widget;
			} else {
				throw new \Exception("Expecting end() of " . get_class($widget) . ", found " . get_called_class());
			}
		} else {
			throw new \Exception("Unexpected " . get_called_class() . '::end() call. A matching begin() is not found.');
		}
	}

	/**
	 * Creates a widget instance and runs it.
	 * The widget rendering result is returned by this method.
	 * @param array $config name-value pairs that will be used to initialize the object properties
	 * @return string the rendering result of the widget.
	 */
	public static function widget($config = [])
	{
	    ob_start();
	    ob_implicit_flush(false);
	    /** @var Widget $widget */
	    $config['class'] = get_called_class();
	    $widget = self::createObject($config['class']);
	    $out = $widget->run();
	    return ob_get_clean() . $out;
	}
	
	/**
	 * Creates a new object using the given configuration.
	 *
	 * The configuration can be either a string or an array.
	 * If a string, it is treated as the *object class*; if an array,
	 * it must contain a `class` element specifying the *object class*, and
	 * the rest of the name-value pairs in the array will be used to initialize
	 * the corresponding object properties.
	 *
	 * The method will pass the given configuration as the last parameter of the constructor,
	 * and any additional parameters to this method will be passed as the rest of the constructor parameters.
	 *
	 * @param string|array $config the configuration. It can be either a string representing the class name
	 * or an array representing the object configuration.
	 * @return mixed the created object
	 */
	public static function createObject($config)
	{
	    static $reflections = [];
	
	    if (is_string($config)) {
	        $class = $config;
	        $config = [];
	    } elseif (isset($config['class'])) {
	        $class = $config['class'];
	        unset($config['class']);
	    } else {
	        throw new \Exception('Object configuration must be an array containing a "class" element.');
	    }

	    $class = new $class;
	    foreach($config as $key => $value) {
	        if(property_exists($class, $key)) {
	            $class->$key = $value;
	        }
	    }

	    return $class;
	}
}