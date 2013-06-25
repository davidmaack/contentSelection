<?php

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2013 
 * @package    contentSelection
 * @license    GNU/LGPL
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
        if ($objRow->contentSelection == '' || TL_MODE == 'BE')
            return $strBuffer;

        $arrCs = deserialize($objRow->contentSelection);

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