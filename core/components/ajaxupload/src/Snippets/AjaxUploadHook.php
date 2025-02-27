<?php
/**
 * Abstract AjaxUpload Hook
 *
 * @package ajaxupload
 * @subpackage snippet
 */

namespace TreehillStudio\AjaxUpload\Snippets;

/**
 * Class Hook
 */
abstract class AjaxUploadHook extends Hook
{
    /**
     * Get uid values
     *
     * @param string $uid
     * @return array
     */
    protected function getUidValues($uid)
    {
        $value = $this->hook->getValue($uid);
        switch ($this->getProperty('fieldformat')) {
            case 'json' :
                $values = json_decode($value, true);
                break;
            case 'csv':
            default :
                $values = ($value) ? explode(',', $value) : [];
                break;
        }
        return $values;
    }

    /**
     * Set uid values
     *
     * @param string $uid
     * @param array $values
     */
    protected function setUidValues($uid, $values)
    {
        switch ($this->getProperty('fieldformat')) {
            case 'json' :
                $values = json_encode($values, JSON_UNESCAPED_SLASHES);
                break;
            case 'csv':
            default :
                $values = implode(',', $values);
                break;
        }
        $this->hook->setValue($uid, $values);
    }
}
