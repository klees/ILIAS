<?php

/**
 * Class ilDclTextInputGUI
 * @author Stefan Wanzenried <sw@studer-raimann.ch>
 */
class ilDclTextInputGUI extends ilTextInputGUI
{
    public function setValueByArray(array $a_values) : void
    {
        parent::setValueByArray($a_values);
        foreach ($this->getSubItems() as $item) {
            $item->setValueByArray($a_values);
        }
    }

    public function checkInput() : bool
    {
        // validate regex
        if ($this->getPostVar() == 'prop_' . ilDclBaseFieldModel::PROP_REGEX && $_POST[$this->getPostVar()]) {
            $regex = $_POST[$this->getPostVar()];
            if (substr($regex, 0, 1) != "/") {
                $regex = "/" . $regex;
            }
            if (substr($regex, -1) != "/") {
                $regex .= "/";
            }
            try {
                preg_match($regex, '');
            } catch (Exception $e) {
                global $DIC;
                $lng = $DIC['lng'];
                $this->setAlert($lng->txt('msg_input_does_not_match_regexp'));

                return false;
            }
        }

        return parent::checkInput();
    }
}
