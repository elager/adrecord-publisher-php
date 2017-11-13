<?php
/**
 * adrecord-publisher-php
 *
 * PHP library for the Adrecord Publisher API v1.
 * 
 * @author Elias Lager
 * @version 0.1
 * @package adrecord-publisher-php
 * @link https://api.adrecord.com/publisher/docs.html
 * @license MIT License
 */


/**
 * Configuration
 *
 * Please update the configuration to fit your environment.
 *
 * apikey:  generate your API key at https://www.adrecord.com/
 * apiurl:  URL to the API. You should not need to change this.
 * decode_json:  set to true to decode the returned JSON into an associated
 *      array, which is convenient when using PHP to parse the data. If set to
 *      false, the unmodified JSON is returned.
 */
$conf = array(
    'apikey'        => '',
    'apiurl'        => 'https://api.adrecord.com/v1/',
    'decode_json'   => true,
);


/**
 * List all channels
 *
 * List all channels associated with your account.
 *
 * @return array  with channel list
 */
function channels_list() {
    global $conf;

    $path   = 'channels';
    $params = array();

    return curl_open($path, $params);
}


/**
 * Create new channel
 *
 * @param string $type  type of channel (values: website, adwords or facebook)
 * @param string $name  name of channel
 * @param string $url  URL of channel
 *
 * @return array  with result
 */
function channels_new($type, $name, $url) {
    global $conf;

    $path   = 'channels/new';
    $params = array(
        'type'  => $type,
        'name'  => $name,
        'url'   => $url
    );

    return curl_open($path, $params);
}


/**
 * View channel details
 *
 * @param int $id  channel id
 *
 * @return array  with channel details
 */
function channels_details($id) {
    global $conf;

    $path   = 'channels/' . $id;
    $params = array();

    return curl_open($path, $params);
}


/**
 * Edit a channel
 *
 * Change the name of a specific channel.
 *
 * @param int $id  channel id
 * @param string $name  new channel name
 *
 * @return array  with result
 */
function channels_edit($id, $newname) {
    global $conf;

    $path   = 'channels/' . $id . '/edit';
    $params = array(
        'name' => $newname
    );

    return curl_open($path, $params);
}


/**
 * Delete a channel
 *
 * @param int $id  channel id
 *
 * @return array  with result
 */
function channels_delete($id) {
    global $conf;

    $path   = 'channels/' . $id . '/delete';
    $params = array();

    return curl_open($path, $params);
}


/**
 * List available programs
 *
 * @return array  with result
 */
function programs_list() {
    global $conf;

    $path   = 'programs';
    $params = array();

    return curl_open($path, $params);
}


/**
 * View program details
 *
 * @param int $id  channel id
 *
 * @return array  with program details
 */
function programs_details($id) {
    global $conf;

    $path   = 'programs/' . $id;
    $params = array();

    return curl_open($path, $params);
}


/**
 * List transactions
 *
 * List the transactions on your account. You can also specify the channelID
 * and/or programID to filter the result. If you want to list the transactions
 * of a specific program and all channels, specify 0 as the channelID. You can
 * also specify the start and stop date.
 *
 * @param string $start  start date (YYYY-MM-DD)
 * @param string $stop  stop date (YYYY-MM-DD)
 * @param int $channel_id  channel id
 * @param int $program_id  program id
 * 
 * @return array  with result
 */
function transactions_list($start = false, $stop = false, $channel_id = false, $program_id = false) {
    global $conf;

    // If $channel_id is not set but $program_id is, $channel_id is set to '0'
    // which will list all transactions from that channel (as defined by the
    // API documentation).
    if(!$channel_id && $program_id)
        $channel_id = '0';

    // Add the '/' as this is later added onto the URL path. We need to compare
    // $channel_id using '!== false' since 0 (zero) itself is a false value.
    $channel_id = ($channel_id !== false) ? '/' . $channel_id : NULL;
    $program_id = ($program_id) ? '/' . $program_id : NULL;

    // Build $path
    $path   = 'transactions' . $channel_id . $program_id;
    $params = array();

    // Add the start and stop parameters if they have been set.
    if($start)
        $params['start'] = $start;

    if($stop)
        $params['stop'] = $stop;

    return curl_open($path, $params);
}


/**
 * Execute a cURL session and decode returned JSON data
 *
 * Some cURL options and the API key are set and the cURL request is sent. The
 * retrieved data is decoded from JSON into an associated array and returned.
 *
 * @param string $path  the URL, minus the configured apiurl, to send the request to.
 * @param array $params  parameters to send in the request.
 *
 * @return resource  a cURL session is returned.
 */
function curl_open($path, $params) {
    global $conf;

    // Add the API key into the array
    $params['apikey'] = $conf['apikey'];

    // Initiate a cURL session and set options
    $session = curl_init();
    curl_setopt($session, CURLOPT_HEADER, false);
    curl_setopt($session, CURLOPT_URL, $conf['apiurl'] . $path);
    curl_setopt($session, CURLOPT_POSTFIELDS, $params);
    curl_setopt($session, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

    // Execute cURL session
    $data = curl_exec($session);

    // Close cURL session
    curl_close($session);

    // Decode returned JSON data into an associated array.
    $data = json_decode($data, true);

    // Return array with data
    return $data;
}
