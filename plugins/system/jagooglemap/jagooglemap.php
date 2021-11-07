<?php
/**
 * ------------------------------------------------------------------------
 * JA System Google Map plugin
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
/**
 *
 * JA GOOGLE MAP PLUGIN SYSTEM CLASS
 * @author JoomlArt
 *
 */
class plgSystemJagooglemap extends JPlugin
{
    protected $_plgCodeNew = "#{jamap(.*?)}\s*{/jamap}#i";
    protected $_plgCode = "#{jamap(.*?)}#i";
    protected $mapSetting = array();
    protected $mapId = null;


    /**
     *
     * Construct JA Googla Map
     * @param object $subject
     * @param object $config
     */
    function __construct(&$subject, $config)
    {
        $mainframe = JFactory::getApplication();
        parent::__construct($subject, $config);

        $this->plugin = JPluginHelper::getPlugin('system', 'jagooglemap');
        $this->plgParams = new JRegistry();
        $this->plgParams->loadString($this->plugin->params);
    }

    function onBeforeRender()
    {
        JHtml::_('jquery.framework');
        JHtml::_('behavior.core');
    }

    /**
     *
     * Process data after render
     * @return string
     */
    function onAfterRender()
    {
        $app = JFactory::getApplication();

        if ($app->isClient('administrator')) {
            return;
        }
        $body = JFactory::getApplication()->getBody();

        $plgParams = $this->plgParams;
        $disable_map = $plgParams->get('disable_map', 0);

        if ($disable_map) {
            $body = $this->removeCode($body);
            JFactory::getApplication()->setBody($body);
            return;
        }

        if (!preg_match($this->_plgCodeNew, $body) && !preg_match($this->_plgCode, $body)) {
            return;
        }
        
        $body = $this->stylesheet($this->plugin, $body);

        //ignore short-code that placed in text/editor field
        $pattern = '#value\s*=\s*"[^"]*?{jamap.*?}(\s*{/jamap})?[^"]*?"#';
        $pattern2 = '#<textarea[^>]*?>[\s\S]*?{jamap.*?}(\s*{/jamap})?[\s\S]*?</textarea>#';
        $body = preg_replace_callback($pattern, array($this, 'escapeMap'), $body);
        $body = preg_replace_callback($pattern2, array($this, 'escapeMap'), $body);

        //generate map
        $body = preg_replace_callback($this->_plgCodeNew, array($this, 'genMap'), $body);
        $body = preg_replace_callback($this->_plgCode, array($this, 'genMap'), $body);

        //restore short codes
        $body = str_replace(array('{[jamap]', '{/[jamap]}'), array('{jamap', '{/jamap}'), $body);

        JFactory::getApplication()->setBody($body);
    }

    function escapeMap($matches) {
        return str_replace(array('{jamap', '{/jamap}'), array('{[jamap]', '{/[jamap]}'), $matches[0]);
    }
    
    function genMap($matches) {
    	static $mapid = 0;
    	$mapid++;
    	
        $this->mapId = $mapid;
    	$this->mapSetting = $this->parseAttributes($matches[0]);
        $output = $this->loadLayout($this->plugin, 'default');
        return $output;
    }
    
    /**
     * @ref JUtility::parseAttributes
     */
    protected function parseAttributes($string)
    {
        $attr = array();
        $retarray = array();

        $string = preg_replace("/\\\\'/", '__QUOTE__', $string);
        // Let's grab all the key/value pairs using a regular expression
        preg_match_all("/([\w:-]+)[\s]?=[\s]?'([^']*)'/i", $string, $attr);

        if (is_array($attr))
        {
            $numPairs = count($attr[1]);

            for ($i = 0; $i < $numPairs; $i++)
            {
                $retarray[$attr[1][$i]] = str_replace('__QUOTE__', "\'", $attr[2][$i]);
            }
        }

        return $retarray;
    }


    /**
     *
     * Remove map code tag
     * @param string $content
     * @return string
     */
    function removeCode($content)
    {
        return preg_replace($this->_plgCodeNew, '', $content);
        return preg_replace($this->_plgCode, '', $content);
    }


    /**
     *
     * Get layout for display
     * @param object $plugin
     * @param string $layout
     * @return string
     */
    function getLayoutPath($plugin, $layout = 'default')
    {

        $app = JFactory::getApplication();

        // Build the template and base path for the layout
        $tPath = JPATH_BASE . '/templates/' . $app->getTemplate() . '/html/' . $plugin->name . '/' . $layout . '.php';
        $bPath = JPATH_BASE . '/plugins/' . $plugin->type . '/' . $plugin->name . '/tmpl/' . $layout . '.php';
        // If the template has a layout override use it
        if (file_exists($tPath)) {
            return $tPath;
        } elseif (file_exists($bPath)) {
            return $bPath;
        }
        return '';
    }


    /**
     *
     * Load content into layout
     * @param object $plugin
     * @param string $layout
     * @return string
     */
    function loadLayout($plugin, $layout = 'default')
    {
        $layout_path = $this->getLayoutPath($plugin, $layout);
        if ($layout_path) {
            ob_start();
            require $layout_path;
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }
        return '';
    }


    /**
     *
     * Set style for map display
     * @param object $plugin
     * @param string $bodyString
     * @return string
     */
    function stylesheet($plugin, $bodyString)
    {
        $assets_url = JUri::base(true) . '/plugins/' . $plugin->type . '/' . $plugin->name . '/';
        $headtag = array();
        $headtag[] = '<link href="' . $assets_url . 'assets/style.css?v=1" type="text/css" rel="stylesheet" />';
        $headtag[] = '<script src="' . $assets_url . 'assets/markcluster.js" type="text/javascript" ></script>';
        $headtag[] = '<script src="' . $assets_url . 'assets/script.js?v=1" type="text/javascript" ></script>';

        $api_key = $this->params->get('api_key', '');

        $map_js = '//maps.googleapis.com/maps/api/js?key=' . $api_key;
        $headtag[] = '<script src="' . $map_js . '" type="text/javascript" ></script>';

        $bodyString = str_replace('</head>', "\t" . implode("\n", $headtag) . "\n</head>", $bodyString);
        return $bodyString;
    }
}