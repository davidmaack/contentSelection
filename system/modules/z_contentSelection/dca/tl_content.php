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
 * Lists 
 */
$GLOBALS['TL_DCA']['tl_content']['list']['sorting']['child_record_callback'] = array('tl_content_contentSelection', 'childRecordCallback');

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
    $arrPalettes[] = '{contentSelection_legend},contentSelection';

    $GLOBALS['TL_DCA']['tl_content']['palettes'][$key] = implode(";", $arrPalettes);
}

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['contentSelection'] = array
    (
    'label' => &$GLOBALS['TL_LANG']['tl_content']['contentSelection'],
    'exclude' => true,
    'inputType' => 'multiColumnWizard',
    'eval' => array
        (
        'columnFields' => array
            (
            'cs_client_os' => array
                (
                'label' => &$GLOBALS['TL_LANG']['tl_content']['cs_client_os'],
                'exclude' => true,
                'inputType' => 'select',
                'options_callback' => array('AgentSelection', 'getClientOs'),
                'eval' => array(
                    'style' => 'width:158px',
                    'chosen' => true,
                    'includeBlankOption' => true
                )
            ),
            'cs_client_browser' => array
                (
                'label' => &$GLOBALS['TL_LANG']['tl_content']['cs_client_browser'],
                'exclude' => true,
                'inputType' => 'select',
                'options_callback' => array('AgentSelection', 'getClientBrowser'),
                'eval' => array(
                    'style' => 'width:158px',
                    'chosen' => true,
                    'includeBlankOption' => true
                )
            ),
            'cs_client_browser_operation' => array
                (
                'label' => &$GLOBALS['TL_LANG']['tl_content']['cs_client_browser_operation'],
                'inputType' => 'select',
                'options' => array(
                    'lt' => '<',
                    'lte' => '<=',
                    'gte' => '>=',
                    'gt' => '>'
                ),
                'eval' => array(
                    'style' => 'width:70px',
                    'chosen' => true,
                    'includeBlankOption' => true
                )
            ),
            'cs_client_browser_version' => array
                (
                'label' => &$GLOBALS['TL_LANG']['tl_content']['cs_client_browser_version'],
                'inputType' => 'text',
                'eval' => array(
                    'style' => 'width:70px'
                )
            ),
            'cs_client_is_mobile' => array
                (
                'label' => &$GLOBALS['TL_LANG']['tl_content']['cs_client_is_mobile'],
                'exclude' => true,
                'inputType' => 'select',
                'options' => array(
                    '1' => $GLOBALS['TL_LANG']['MSC']['yes'],
                    '2' => $GLOBALS['TL_LANG']['MSC']['no']
                ),
                'eval' => array(
                    'includeBlankOption' => true,
                    'style' => 'width:50px',
                    'chosen' => true
                )
            ),
            'cs_client_is_invert' => array
                (
                'label' => &$GLOBALS['TL_LANG']['tl_content']['cs_client_is_invert'],
                'exclude' => true,
                'inputType' => 'checkbox',
                'eval' => array(
                    'style' => 'min-width:30px'
                )
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
     * Return the new label for the content element
     * 
     * @param array $arrRow
     * @return string
     */
    public function childRecordCallback($arrRow)
    {
        $arrContentSelection = array();
        if ($arrRow['contentSelection'])
        {
            $arrCs = deserialize($arrRow['contentSelection']);
            if (is_array($arrCs))
            {
                $arrSelector = $arrCs[0];
                $strInvert   = (($arrSelector['cs_client_is_invert']) ? ucfirst($GLOBALS['TL_LANG']['MSC']['hiddenHide']) : ucfirst($GLOBALS['TL_LANG']['MSC']['hiddenShow'])) . ':';
                foreach ($arrSelector as $strConfig => $mixedConfig)
                {
                    switch ($strConfig)
                    {
                        case 'cs_client_os':
                            if ($mixedConfig)
                            {
                                $arrContentSelection[] = ' ' . $mixedConfig;
                            }
                            break;

                        case 'cs_client_browser':
                            if ($mixedConfig)
                            {
                                $arrContentSelection[] = ' ' . $mixedConfig;
                            }
                            break;

                        case 'cs_client_browser_version':
                            if ($mixedConfig)
                            {
                                switch ($arrSelector['cs_client_browser_operation'])
                                {
                                    case 'lt':
                                        $strOperator           = '<';
                                        break;
                                    case 'lte':
                                        $strOperator           = '<=';
                                        break;
                                    case 'gte':
                                        $strOperator           = '>=';
                                        break;
                                    case 'gt':
                                        $strOperator           = '>';
                                        break;
                                    default:
                                        $strOperator           = '';
                                        break;
                                }
                                $arrContentSelection[] = ' ' . $strOperator . ' ' . $mixedConfig;
                            }
                            break;

                        case 'cs_client_is_mobile':
                            if ($mixedConfig != '')
                            {
                                $arrContentSelection[] = ' ' . $GLOBALS['TL_LANG']['tl_content']['cs_client_is_mobile'][0] . ': ' . (($mixedConfig == 1) ? $GLOBALS['TL_LANG']['MSC']['yes'] : $GLOBALS['TL_LANG']['MSC']['no']);
                            }
                            break;
                    }
                }

                if (count($arrContentSelection) > 0)
                {
                    array_unshift($arrContentSelection, $strInvert);
                    array_unshift($arrContentSelection, '(');
                    if (count($arrCs) > 1)
                    {
                        $arrContentSelection[] = ' /... ';
                    }
                    $arrContentSelection[] = ')';
                }
            }
        }

        return vsprintf(
                        '<div class="cte_type %s">%s%s %s</div><div class="limit_height%s">%s</div>', array(
                    $arrRow['invisible'] ? 'unpublished' : 'published',
                    ($GLOBALS['TL_LANG']['CTE'][$arrRow['type']][0] != '') ? $GLOBALS['TL_LANG']['CTE'][$arrRow['type']][0] : '&nbsp;',
                    (($arrRow['type'] == 'alias') ? ' ID ' . $arrRow['cteAlias'] : '') . ($arrRow['protected'] ? ' (' . $GLOBALS['TL_LANG']['MSC']['protected'] . ')' : ($arrRow['guests'] ? ' (' . $GLOBALS['TL_LANG']['MSC']['guests'] . ')' : '')),
                    implode('', $arrContentSelection),
                    (!$GLOBALS['TL_CONFIG']['doNotCollapse'] ? ' h64' : ''),
                    $this->getContentElement($arrRow['id'])
                        )
        );
    }

}

?>