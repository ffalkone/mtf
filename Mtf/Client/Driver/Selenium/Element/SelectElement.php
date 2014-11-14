<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mtf\Client\Driver\Selenium\Element;

use Mtf\Client\Element as ElementInterface;
use Mtf\Client\Driver\Selenium\Element;
use Mtf\Client\Element\Locator;

/**
 * Class SelectElement
 * Typified element class for Select elements
 *
 * @api
 */
class SelectElement extends Element
{
    /**
     * Return Wrapped Element.
     * If element was not created before:
     * 1. Context is defined. If context was not passed to constructor - test case (all page) is taken as context
     * 2. Attempt to get selenium element is performed in loop
     * that is terminated if element is found or after timeout set in configuration
     *
     * @param bool $waitForElementPresent
     * @return \PHPUnit_Extensions_Selenium2TestCase_Element_Select
     * @throws \PHPUnit_Extensions_Selenium2TestCase_WebDriverException
     */
    protected function _getWrappedElement($waitForElementPresent = true)
    {
        return $this->_driver->select(parent::_getWrappedElement($waitForElementPresent));
    }

    /**
     * Set the value
     *
     * @param string|array $value
     * @return void
     */
    public function setValue($value)
    {
        $this->_eventManager->dispatchEvent(['set_value'], [__METHOD__, $this->getAbsoluteSelector()]);
        $criteria = new \PHPUnit_Extensions_Selenium2TestCase_ElementCriteria('xpath');
        $criteria->value('.//option[contains(text(), "' . $value . '")]');
        $this->_getWrappedElement()->selectOptionByCriteria($criteria);
    }

    /**
     * Select value in dropdown which has option groups
     *
     * @param string $optionGroup
     * @param string $value
     * @return void
     */
    public function setOptionGroupValue($optionGroup, $value)
    {
        $optionLocator = ".//optgroup[@label='$optionGroup']/option[contains(text(), '$value')]";
        $criteria = new \PHPUnit_Extensions_Selenium2TestCase_ElementCriteria('xpath');
        $criteria->value($optionLocator);
        $this->_getWrappedElement(true)->selectOptionByCriteria($criteria);
    }

    /**
     * Get value of the selected option of the element
     *
     * @return string
     */
    public function getValue()
    {
        $this->_eventManager->dispatchEvent(['get_value'], [(string) $this->_locator]);
        return $this->_getWrappedElement(true)->selectedLabel();
    }

    /**
     * Get label of the selected option of the element
     *
     * @return string
     */
    public function getText()
    {
        return $this->_getWrappedElement(true)->selectedLabel();
    }

    /**
     * Drag'n'drop method is not accessible in this class.
     * Throws exception if used.
     *
     * @param ElementInterface $target
     * @throws \BadMethodCallException
     * @return void
     */
    public function dragAndDrop(ElementInterface $target)
    {
        throw new \BadMethodCallException('Not applicable for this class of elements (SelectElement)');
    }

    /**
     * Send a sequence of key strokes to the active element.
     *
     * @param array $keys
     * @return void
     */
    public function keys(array $keys)
    {
        $mSelect = $this->_getWrappedElement();
        $criteria = new \PHPUnit_Extensions_Selenium2TestCase_ElementCriteria(Locator::SELECTOR_TAG_NAME);
        $criteria->value('option');
        $mSelect->clearSelectedOptions();
        $options = $mSelect->elements($criteria);
        $pattern = '/^' . implode('', $keys) . '[a-z0-9A-Z-]*/';
        foreach ($options as $option) {
            preg_match($pattern, $option->text(), $matches);
            if ($matches) {
                $this->setValue($option->text());
                break;
            }
        }
    }
}
