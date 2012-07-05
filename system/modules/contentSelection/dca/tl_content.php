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
 * Lists 
 */
//$GLOBALS['TL_DCA']['tl_content']['list']['sorting']['child_record_callback'] = array('tl_content_contentSelection', 'childRecordCallback');

/**
 * Palettes
 */
foreach ($GLOBALS['TL_DCA']['tl_content']['palettes'] as $key => $row)
{
    if ($key == '__selector__')
    {
        continue;
    }

    $arrPalettes = explode(";", $row);
    foreach ($arrPalettes as $strKey => $strPalett)
    {
        if (stristr($strPalett, 'expert_legend'))
        {
            $arrPalettes[$strKey] = $strPalett . ',contentSelector';
        }
    }

    $GLOBALS['TL_DCA']['tl_content']['palettes'][$key] = implode(";", $arrPalettes);
}

/**
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['contentSelector'] = array
    (
    'label'     => &$GLOBALS['TL_LANG']['tl_content']['contentSelector'],
    'exclude'   => true,
    'inputType' => 'multiColumnWizard',
    'eval'      => array
        (
        'columnFields' => array
            (
            'cs_client_os' => array
                (
                'label'            => &$GLOBALS['TL_LANG']['tl_content']['cs_client_os'],
                'exclude'          => true,
                'inputType'        => 'select',
                'options_callback' => array('tl_content_contentSelection', 'getCsClientOs'),
                'eval' => array(
                    'style'              => 'width:158px',
                    'includeBlankOption' => true,
                    'chosen'             => true
                )
            ),
            'cs_client_browser'  => array
                (
                'label'            => &$GLOBALS['TL_LANG']['tl_content']['cs_client_browser'],
                'exclude'          => true,
                'inputType'        => 'select',
                'options_callback' => array('tl_content_contentSelection', 'getCsClientBrowser'),
                'eval' => array(
                    'style'                     => 'width:158px',
                    'includeBlankOption'        => true,
                    'chosen'                    => true
                )
            ),
            'cs_client_browser_version' => array
                (
                'label'     => &$GLOBALS['TL_LANG']['tl_content']['cs_client_browser_version'],
                'inputType' => 'text',
                'eval'      => array('style'               => 'width:160px')
            ),
            'cs_client_is_mobile' => array
                (
                'label'     => &$GLOBALS['TL_LANG']['tl_content']['cs_client_is_mobile'],
                'exclude'   => true,
                'inputType' => 'checkbox',
                'eval'      => array('style'               => 'width:40px')
            ),
            'cs_client_is_invert' => array
                (
                'label'     => &$GLOBALS['TL_LANG']['tl_content']['cs_client_is_invert'],
                'exclude'   => true,
                'inputType' => 'checkbox',
                'eval'      => array('style' => 'width:40px')
            )
        )
    )
);

/**
 * Class tl_content_contentSelection
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @copyright  MEN AT WORK 2012
 * @package    contentSelection
 * @license    GNU/GPL 2
 * @filesource
 */
class tl_content_contentSelection extends Controller
{

    /**
     * Return option array for operation systems
     * 
     * @return array
     */
    public function getCsClientOs()
    {
        $arrOptions = array();

        foreach ($GLOBALS['TL_CONFIG']['os'] as $strLabel => $arrOs)
        {
            $arrOptions[standardize($strLabel)] = $strLabel;
        }

        return $arrOptions;
    }

    /**
     * Return browser array for operation systems
     * 
     * @return array
     */
    public function getCsClientBrowser()
    {
        $arrOptions = array();

        foreach ($GLOBALS['TL_CONFIG']['browser'] as $strLabel => $arrBrowser)
        {
            $arrOptions[$arrBrowser['browser']] = $strLabel;
        }

        return $arrOptions;
    }
    
    /**
     * Return the new label for the content element
     * 
     * @param array $arrRow
     * @return string
     */
    public function childRecordCallback($arrRow)
    {
        // TODO set output
    }

}

?>