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
		$widget = Yii::createObject($config);
		self::$stack[] = $widget;
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
				throw new InvalidCallException("Expecting end() of " . get_class($widget) . ", found " . get_called_class());
			}
		} else {
			throw new InvalidCallException("Unexpected " . get_called_class() . '::end() call. A matching begin() is not found.');
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
	    $widget = Yii::createObject($config);
	    $out = $widget->run();
	    return ob_get_clean() . $out;
	}
}