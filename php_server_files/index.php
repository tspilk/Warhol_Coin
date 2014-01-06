<?php
 
function get_data($url)
{
    $ch = curl_init();
    $timeout = 0;
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

//SET YOUR FORECAST.IO API KEY HERE
$api_key='YOUR API HERE';


$payload = json_decode(file_get_contents('php://input'), true);
/*
if(!$payload) 
{
 $payload = json_decode('{"1": 411157,"2": -960238,"3": "us"}', true); // Use this to set a 'default' location
}
*/
$payload[1] = $payload[1] / 10000;
$payload[2] = $payload[2] / 10000;

$forecast_url='https://api.forecast.io/forecast/'.$api_key.'/'.$payload[1].','.$payload[2].'?units='.$payload[3].'&exclude=hourly,minutely,alerts';
$litecoin_url='https://btc-e.com/api/2/ltc_usd/ticker';
$bitcoin_url='https://api.bitcoinaverage.com/ticker/USD';

$forecast = json_decode(get_data($forecast_url));
$litecoin = json_decode(get_data($litecoin_url));
$bitcoin = json_decode(get_data($bitcoin_url));
 

if(!$forecast) {
    die();
}      
$response = array();
$icons = array(
    'clear-day' => 0,
    'clear-night' => 1,
    'rain' => 2,
    'snow' => 3,
    'sleet' => 4,
    'wind' => 5,
    'fog' => 6,
    'cloudy' => 7,
    'partly-cloudy-day' => 8,
    'partly-cloudy-night' => 9
);
$ltc_price = $litecoin->ticker->last;
$btc_price = $bitcoin->last; //{'24h_avg'}; // 24h_avg starts with a number, requires being enclosed in {' '}
$sunset = $forecast->daily->data[0]->sunsetTime;
$sunset_h = date('H', $sunset);
$sunset_m = date('i', $sunset);

$round_m = intval($sunset_m);

while ($round_m < 10)
{
  if ($round_m < 5)
  {
    $round_m = 59;
    $sunset_h = $sunset_h -1;
  }
  elseif ($round_m >=5)
  {
    $round_m = 10;
    }
}

$command = 'python find_timezone.py --lat '.$payload[1].' --lon '.$payload[2];
$timezone = exec($command);

date_default_timezone_set($timezone);
$timestamp = 'Updated: '.date('H:i');


$icon_id = $icons[$forecast->currently->icon];
$response[1] = array('b', $icon_id);
$response[2] = array('s', round($forecast->currently->temperature));
$response[3] = array('s', round($forecast->daily->data[0]->temperatureMax));
$response[4] = array('s', round($forecast->daily->data[0]->temperatureMin));
$response[5] = array('s', intval($ltc_price));
$response[6] = array('s', intval($btc_price));
$response[7] = $timestamp;
print json_encode($response);
