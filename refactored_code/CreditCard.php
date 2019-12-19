<?php


class CreditCard
{
    private $number = "";
    private $error = "";

    public function set($number = NULL)
    {
        if (is_null($number)) {
            $this->number = NULL;
            $this->error = 'ERROR_NOT_SET';

            return 'ERROR_NOT_SET';
        }

        $this->number = $number;
        return $this->IsValid();
    }

    // ************************************************************************
    // Description: retrieve the current card number. the number is returned
    //              unformatted suitable for use with submission to payment and
    //              authorization gateways.
    //
    // Parameters:  none
    //
    // Returns:     card number
    // ************************************************************************
    function get()
    {
        return $this->number;
    }

    private function checkLength($length, $category)
    {
        if ($category == 0) {
            return (($length == 13) || ($length == 16));
        } elseif ($category == 1) {
            return (($length == 16) || ($length == 18) || ($length == 19));
        } elseif ($category == 2) {
            return ($length == 16);
        } elseif ($category == 3) {
            return ($length == 15);
        } elseif ($category == 4) {
            return ($length == 14);
        }

        return 1;
    }

    private function isValid()
    {
        $value = '';
        $lencat = 0;
        // clear current object values ... can't set anything until
        // init copy buffer
        for ($i = 0; $i < strlen($this->number); $i++) {                // check input
            $c = $this->number[$i];                                   // grab a character
            if (ctype_digit($c)) {                                // is a digit?
                $value .= $c;                                   // yes, save it
            } elseif (!ctype_space($c) && !ctype_punct($c)) {
                $this->error = 'ERROR_INVALID_CHAR';
                break;
            }
        }
        /**
         *  Visa = 4XXX - XXXX - XXXX - XXXX
         * MasterCard = 5[1-5]XX - XXXX - XXXX - XXXX
         * Discover = 6011 - XXXX - XXXX - XXXX
         * Amex = 3[4,7]X - XXXX - XXXX - XXXX
         * Diners = 3[0,6,8] - XXXX - XXXX - XXXX
         * Any Bankcard = 5610 - XXXX - XXXX - XXXX
         * JCB =  [3088|3096|3112|3158|3337|3528] - XXXX - XXXX - XXXX
         * Enroute = [2014|2149] - XXXX - XXXX - XXX
         * Switch = [4903|4911|4936|5641|6333|6759|6334|6767] - XXXX - XXXX - XXXX
         */
        $this->number = $value;
        if ($this->number[0] == '4') {
            $lencat = 2;
        } elseif ($this->number[0] == '5') {
            $lencat = 2;
        } elseif ($this->number[0] == '3') {
            $lencat = 4;
        } elseif ($this->number[0] == '2') {
            $lencat = 3;
        }

        if (!$this->checkLength(strlen($this->number), $lencat)) {
            $this->error = 'ERROR_INVALID_LENGTH';
        } else {
            $this->error = true;
        }
        return $this->error;

    }
}