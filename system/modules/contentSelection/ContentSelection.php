<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  MEN AT WORK 2012
 * @package    contentSelection
 * @license    GNU/GPL 2
 * @filesource
 */

/**
 * Class ContentSelection
 */
class ContentSelection extends Backend
{

    /**
     * Initialize the object
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Check if the content element has permission for current configurations
     * 
     * @param DB_Mysql_Result $objRow
     * @param string $strBuffer
     * @return string $strBuffer
     */
    public function getContentElementWithPermission($objRow, $strBuffer)
    {
        if ($objRow->contentSelector != '')
        {
            $arrCs = deserialize($objRow->contentSelector);
            $objUa = $this->Environment->agent;
            
            foreach ($arrCs as $key => $arrSelector)
            {
                $arrCs[$key]['cs_client_os']        = ($arrSelector['cs_client_os'] != '') ? $this->getOs($arrSelector['cs_client_os']) : false;
                $arrCs[$key]['cs_client_browser']   = ($arrSelector['cs_client_browser'] != '') ? $this->getBrowser($arrSelector['cs_client_browser']) : false;
                $arrCs[$key]['cs_client_is_mobile'] = ($arrSelector['cs_client_is_mobile'] != '') ? true : false;
                $arrCs[$key]['cs_client_is_invert'] = ($arrSelector['cs_client_is_invert'] != '') ? true : false;
            }

            $blnGlobalPermisson = false;
            foreach ($arrCs as $key => $arrSelector)
            {
                $blnPermisson = true;
                foreach ($arrSelector as $strConfig => $mixedConfig)
                {
                    switch ($strConfig)
                    {
                        case 'cs_client_os':
                            if (!$this->checkOsPermission($mixedConfig, $objUa))
                                $blnPermisson = false;
                            break;

                        case 'cs_client_browser':
                            if (!$this->checkBrowserPermission($mixedConfig, $objUa))
                                $blnPermisson = false;
                            break;

                        case 'cs_client_browser_version':
                            if (!$this->checkBrowserVersionPermission($mixedConfig, $objUa))
                                $blnPermisson = false;
                            break;

                        case 'cs_client_is_mobile':
                            if (!$this->checkMobilPermission($mixedConfig, $objUa))
                                $blnPermisson = false;
                            break;

                        case 'cs_client_is_invert':
                            if ($mixedConfig)
                            {
                                $blnPermisson = ($blnPermisson) ? false : true;
                            }                            
                            break;
                    }
                }

                if (!$blnGlobalPermisson && $blnPermisson)
                {
                    $blnGlobalPermisson = true;
                }
            }

            if ($blnGlobalPermisson === false)
            {
                return;
            }
            else
            {
                return $strBuffer;
            }
        }

        return $strBuffer;
    }

    // Helper ------------------------------------------------------------------

    /**
     * Check if the operation system has permission
     * 
     * @param mixed $mixedConfig
     * @param stdClass $objUa
     * @return boolean 
     */
    private function checkOsPermission($mixedConfig, $objUa)
    {
        $arrIOs = array('iPad', 'iPhone', 'iPod');
        
        if ($mixedConfig)
        {            
            if ($mixedConfig['config']['os'] == $objUa->os)
            {                
                if (in_array($mixedConfig['value'], $arrIOs))
                {
                    if (strpos($objUa->string, $mixedConfig['value']) !== false)
                    {
                        return true;
                    }
                }
                else
                {
                    return true;
                }
            }
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Check if the browser has permission
     * 
     * @param type $mixedConfig
     * @param stdClass $objUa
     * @return boolean
     */
    private function checkBrowserPermission($mixedConfig, $objUa)
    {
        if ($mixedConfig)
        {
            if ($mixedConfig['config']['browser'] == $objUa->browser)
            {
                return true;
            }
            return false;
        }
        
        return true;
    }

    /**
     * Check if the browser version has permission
     * 
     * @param type $mixedConfig
     * @param stdClass $objUa
     * @return boolean
     */
    private function checkBrowserVersionPermission($mixedConfig, $objUa)
    {
        if (strlen($mixedConfig) > 0)
        {
            if ($mixedConfig == $objUa->version)
            {
                return true;
            }
            return false;
        }
        
        return true;
    }

    /**
     * Check if is mobil and has permission
     * 
     * @param type $mixedConfig
     * @param stdClass $objUa
     * @return boolean
     */
    private function checkMobilPermission($mixedConfig, $objUa)
    {
        if ($mixedConfig)
        {
            if($mixedConfig != $objUa->mobile)
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the config operation system for given filter
     * 
     * @param string $strFilter
     * @return array|null
     */
    private function getOs($strFilter)
    {
        foreach ($GLOBALS['TL_CONFIG']['os'] as $strLabel => $arrOs)
        {
            if ($strFilter == standardize($strLabel))
            {
                return array('value'  => $strLabel, 'config' => $GLOBALS['TL_CONFIG']['os'][$strLabel]);
            }
        }

        return null;
    }

    /**
     * Get the config browser for given filter
     * 
     * @param string $strFilter
     * @return array|null 
     */
    private function getBrowser($strFilter)
    {
        foreach ($GLOBALS['TL_CONFIG']['browser'] as $strLabel => $arrBrowser)
        {
            if ($strFilter == $arrBrowser['browser'])
            {
                return array('value'  => $strLabel, 'config' => $GLOBALS['TL_CONFIG']['browser'][$strLabel]);
            }
        }

        return null;
    }

}

?>