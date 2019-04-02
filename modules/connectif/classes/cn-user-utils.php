<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    Connectif
 * @copyright Copyright (c) 2017 Connectif
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

class CnUserUtils
{

    const SUBSCRIBE = 'subscribed';
    const UNSUBSCRIBE = 'unsubscribed';

    //TODO get all customer info
    public static function getUserDetails(Context $context)
    {
        $userDetails = array();

        if (isset($context->customer) && $context->customer->id) {
            $userDetails['email'] = $context->customer->email;
            $userDetails['firstName'] = $context->customer->firstname;
            $userDetails['lastName'] = $context->customer->lastname;

            $customerSql = 'SELECT * FROM ' . _DB_PREFIX_ . 'customer WHERE email="' . $context->customer->email . '"';
            $customer = Db::getInstance()->ExecuteS($customerSql);

            if(array_key_exists ('birthday' ,$customer[0])) {
                $birthday = $customer[0]['birthday'];
                $dateTimeBirthday = new DateTime($birthday);
                if($birthday > 0) {
                    $userDetails['birthday'] = $dateTimeBirthday->format(DateTime::ISO8601);
                }
            }

            $userDetails['client_email_status'] = '';
            if (is_array($customer) && count($customer) > 0) {
                $clientEmailStatus =  $customer[0]["newsletter"] == 0 ? self::UNSUBSCRIBE : self::SUBSCRIBE;
                $userDetails['client_email_status'] = $clientEmailStatus;
            }

            $orderTable = _DB_PREFIX_ . 'orders';
            $addressTable = _DB_PREFIX_ . 'address';
            $addressSql = 'SELECT ' . $addressTable . '.*'
                . ' FROM ' . $addressTable . ', ' . $orderTable
                . ' WHERE ' . $addressTable . '.id_address= ' . $orderTable . '.id_address_delivery'
                . ' and ' . $orderTable . '.id_customer=' . $context->customer->id
                . ' and ' . $addressTable . '.deleted=0'
                . ' ORDER BY ' . $orderTable . '.date_add DESC';
            $addressResults = Db::getInstance()->ExecuteS($addressSql);

            if (!$addressResults || count($addressResults) === 0) {
                $addressSql = 'SELECT * FROM ' . $addressTable
                    . ' WHERE id_customer=' . $context->customer->id
                    . ' and deleted=0'
                    . ' ORDER BY date_add DESC';
                $addressResults = Db::getInstance()->ExecuteS($addressSql);
            }


            $address = sizeof($addressResults) > 0 ? $addressResults[0]: array();

            $userDetails['extendedProperties'] = array();
            $activeFields = json_decode(Configuration::get('CN_ACTIVE_REGISTER_FIELDS'));
            if (!Configuration::get('CN_ACTIVE_REGISTER_FIELDS')) {
                $activeFields = array();
            }

            foreach ($activeFields as $key => $value) {
                if (array_key_exists($value->Field, $customer[0])) {
                    $extProp = array('id' => $value->Field, 'value' => $customer[0][$value->Field]);
                    array_push($userDetails['extendedProperties'], $extProp);
                }
            }

            $activeFieldsAddress = json_decode(Configuration::get('CN_ACTIVE_REGISTER_FIELDS_ADDR'));
            if (!Configuration::get('CN_ACTIVE_REGISTER_FIELDS_ADDR')) {
                $activeFieldsAddress = array();
            }

            foreach ($activeFieldsAddress as $key => $value) {
                if (array_key_exists($value->Field, $address)) {
                    $extProp = array('id' => $value->Field, 'value' => $address[$value->Field]);
                    array_push($userDetails['extendedProperties'], $extProp);
                }
            }
            // Add country and state
            $isCountryFieldActive = CnUtils::findPropertyInArray($activeFieldsAddress, 'Field', 'country');
            $isStateFieldActive = CnUtils::findPropertyInArray($activeFieldsAddress, 'Field', 'state');
            if ($isCountryFieldActive && array_key_exists("id_country", $address)) {
                $country = Country::getNameById($context->customer->id_lang, $address["id_country"]);
                array_push($userDetails['extendedProperties'], array('id' => "country", 'value' => $country));
            }
            if ($isStateFieldActive && array_key_exists("id_state", $address)) {
                $state = State::getNameById($address["id_state"]);
                array_push($userDetails['extendedProperties'], array('id' => "state", 'value' => $state));
            }
        }

        return $userDetails;
    }

    public static function getAvailableRegisterFieldsCustomer()
    {
        $customerFields = Db::getInstance()->ExecuteS('SHOW COLUMNS FROM ' . _DB_PREFIX_ . 'customer');
        $hiddenFields = array(
            "id_lang",
            "id_customer",
            "id_shop_group",
            "id_shop",
            "id_default_group",
            "id_risk",
            "birthday",
            "email",
            "firstname",
            "lastname",
            "siret",
            "ape",
            "passwd",
            "last_passwd_gen",
            "outstanding_allow_amount",
            "show_public_prices",
            "max_payment_days",
            "secure_key",
            "date_add",
            "date_upd",
            "active",
            "is_guest",
            "deleted",
        );
        foreach ($customerFields as $key => $field) {
            if (in_array($field["Field"], $hiddenFields)) {
                unset($customerFields[$key]);
            }
        }
        return $customerFields;
    }

    public static function getAvailableRegisterFieldsAddress()
    {
        $addressFields = Db::getInstance()->ExecuteS('SHOW COLUMNS FROM ' . _DB_PREFIX_ . 'address');
        $addressHiddenFields = array(
            "id_country",
            "id_state",
            "id_address",
            "id_customer",
            "id_manufacturer",
            "id_supplier",
            "id_warehouse",
            "alias",
            "company",
            "lastname",
            "firstname",
            "vat_number",
            "date_add",
            "date_upd",
            "active",
            "deleted",
        );
        foreach ($addressFields as $key => $field) {
            if (in_array($field["Field"], $addressHiddenFields)) {
                unset($addressFields[$key]);
            }
        }
        $addressAddedFields = array(
            array('Field' => "country"),
            array('Field' => "state")
        );
        return array_merge($addressFields, $addressAddedFields);
    }
}
