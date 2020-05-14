<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.smso.ro/
 * @since      1.0.0
 *
 * @package    smso
 * @subpackage smso/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    smso
 * @subpackage smso/admin
 * @author     smso <support@smso.ro>
 */

class Smso_Class
{

    public static $baseUrl = 'https://app.smso.ro/api/v1/';


    public function __construct($token)
    {
        $this->token = $token;
    }

    public function createContact($name, $number)
    {
        $data = array();
        $data['name'] = $name;
        $data['number'] = $number;
        return $this->makeRequest('/api/v3/contacts/create', 'POST', $data);
    }

    public function getSenders()
    {
        return $this->makeRequest('/senders', 'GET');
    }

    public function sendMessage($to, $body, $sender)
    {
        $data = array();
        $data['to'] = $to;
        $data['body'] = $body;
        $data['sender'] = $sender;
        return $this->makeRequest('/send', 'POST' , $data);
    }

    public function sendMessageSIM($to, $body)
    {
        $data = array();
        $data['to'] = $to;
        $data['body'] = $body;
        return $this->makeRequest('/send/sim', 'POST' , $data);
    }

    public function getStatusMessage($msg_token)
    {
        /*
        dispatched  No  The message is in the process of sending to the network. (rarely seen)
        sent    No  The message has been sent to the network.
        delivered   Yes The message has been delivered to the phone.
        undelivered Yes The message was undelivered.
        expired Yes The message is expired.
        error   Yes There was an error sending the message.
        */
        $data = array();
        $data['responseToken'] = $msg_token;
        return $this->makeRequest('/status', 'POST' , $data);
    }

    public function isValidToken()
    {
        return $this->makeRequest('', 'POST');
    }

    private function makeRequest($url, $method, $fields = array())
    {

        $token = $this->token;

        $url = Smso_Class::$baseUrl.$url;

        $fieldsString = http_build_query($fields);

        $headers = array();
        $headers[] = "X-Authorization: ".$token;

        $ch = curl_init();

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, count($fields));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
        } else {
            $url .= '?'.$fieldsString;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);
        $return = array();
        $return['response'] = json_decode($result, true);

        if ($return['response'] == false) {
            $return['response'] = $result;
        }

        $return['status'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return $return;
    }
}
