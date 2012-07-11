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
     * Check if the content element has permission for current configurations
     * 
     * @param DB_Mysql_Result $objRow
     * @param string $strBuffer
     * @return string $strBuffer
     */
    public function getContentElementWithPermission($objRow, $strBuffer)
    {
        if ($objRow->contentSelector == '' || TL_MODE == 'BE')
            return $strBuffer;

        $arrCs = deserialize($objRow->contentSelector);

        if (!is_array($arrCs))
            return $strBuffer;

        $objUa = $this->Environment->agent;

        $blnGlobalPermisson = false;
        foreach ($arrCs as $arrSelector)
        {
            $arrSelector['cs_client_os'] = ($arrSelector['cs_client_os'] != '') ? array(
                'value' => $arrSelector['cs_client_os'],
                'config' => $GLOBALS['TL_CONFIG']['os'][$arrSelector['cs_client_os']]
                    ) : false;
            $arrSelector['cs_client_browser']   = ($arrSelector['cs_client_browser'] != '') ? $GLOBALS['TL_CONFIG']['browser'][$arrSelector['cs_client_browser']] : false;
            $arrSelector['cs_client_is_mobile'] = (($arrSelector['cs_client_is_mobile'] != '') ? (($arrSelector['cs_client_is_mobile'] == 1) ? true : false) : 'empty');

            $blnPermisson = true;
            foreach ($arrSelector as $strConfig => $mixedConfig)
            {
                switch ($strConfig)
                {
                    case 'cs_client_os':
                        $blnPermisson = ($blnPermisson && AgentSelection::checkOsPermission($mixedConfig, $objUa));
                        break;

                    case 'cs_client_browser':
                        $blnPermisson = ($blnPermisson && ($mixedConfig['browser'] == $objUa->browser || $mixedConfig['browser'] == '')) ? true : false;
                        break;

                    case 'cs_client_browser_version':
                        $blnPermisson = ($blnPermisson && AgentSelection::checkBrowserVerPermission($mixedConfig, $objUa, $arrSelector['cs_client_browser_operation']));
                        break;

                    case 'cs_client_is_mobile':
                        if (strlen($mixedConfig) < 2)
                        {
                            $blnPermisson = ($blnPermisson && $mixedConfig == $objUa->mobile) ? true : false;
                        }
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

}

?>