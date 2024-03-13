<?php
$keys = [
    'openweathermap' => '21cc003fb684d8f02f4fefabc56c390f',
    'weatherapi' => '7a326c5729784e82a24203959241203',
    'visualcrossing' => 'T6L97K3N5DGYNUCM3SGTFYWH7',
    'weatherbit' => '7c829ef599ea44f0a10b761881eb4727',
    'accuweather' => ''
];

function getOpenWeatherMapForecast($city, $key) {
    $url = "http://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$key}&units=metric";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    return $data['main']['temp'];
}

function getAccuWeatherForecast($city, $key) {
    // Получаем ключ локации для города
    $locationUrl = "http://dataservice.accuweather.com/locations/v1/cities/search?apikey={$key}&q={$city}";
    $locationResponse = file_get_contents($locationUrl);
    $locationData = json_decode($locationResponse, true);
    $locationKey = $locationData[0]['Key'];

    // Используем ключ локации для получения прогноза погоды
    $forecastUrl = "http://dataservice.accuweather.com/forecasts/v1/daily/1day/{$locationKey}?apikey={$key}&metric=true";
    $forecastResponse = file_get_contents($forecastUrl);
    $forecastData = json_decode($forecastResponse, true);
    $minTemp = $forecastData['DailyForecasts'][0]['Temperature']['Minimum']['Value'];
    $maxTemp = $forecastData['DailyForecasts'][0]['Temperature']['Maximum']['Value'];
    return ($minTemp + $maxTemp) / 2; // Возвращает среднюю температуру
}

function getWeatherApiForecast($city, $key) {
    $url = "http://api.weatherapi.com/v1/forecast.json?key={$key}&q={$city}&days=1";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    return $data['forecast']['forecastday'][0]['day']['avgtemp_c'];
}

function getVisualCrossingForecast($city, $key) {
    $url = "https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/{$city}?unitGroup=metric&key={$key}&contentType=json";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    return $data['days'][0]['temp'];
}

function getWeatherbitForecast($city, $key) {
    $url = "https://api.weatherbit.io/v2.0/current?city={$city}&key={$key}&include=minutely";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    return $data['data'][0]['temp'];
}

$city = 'Orsha';

$forecasts = [];
$forecasts[] = getOpenWeatherMapForecast($city, $keys['openweathermap']);
$forecasts[] = getWeatherApiForecast($city, $keys['weatherapi']);
$forecasts[] = getVisualCrossingForecast($city, $keys['visualcrossing']);
$forecasts[] = getWeatherbitForecast($city, $keys['weatherbit']);
$forecasts[] = getAccuWeatherForecast($city, $keys['accuweather']);

print_r($forecasts);

$averageTemp = array_sum($forecasts) / count($forecasts);
echo "Усреднённый прогноз температуры для города {$city} на завтра: {$averageTemp}°C";
?>
