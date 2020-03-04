<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
// Step 001: Include the Http wrapper
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;

class WeatherForecast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // Step 002: defiition of the signature
    protected $signature = 'weather:forecast {location=Berlin}';

    /**
     * The console command description.
     *
     * @var string
     */
    //  Step 003: description of the command
    protected $description = 'weather:forecast
                        {location : the name of the location}
                        ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Step 004 retrieving parameters, location and API key
        $locationName = $this->argument('location');
        $apiKey = env("HERE_API_KEY", "");
        // Step 005: set the base URL for consuming API
        $baseUrl = "https://weather.ls.hereapi.com/weather/1.0/report.json";
        // Step 006: preparing query string for API
        $params = array(
            'product' => 'forecast_7days',
            'apiKey' => $apiKey,
            'name' => $locationName,
            //"language" => "it",
            "metric" => "true"
        );
        // Step 007: composing the absolute URL
        $url = "${baseUrl}?". http_build_query($params);
        // Step 008: Calling API
        $response = Http::get($url);
        // Step 009: check if everything is fine
        if ($response->successful()) {
            // Step 010: retrieving JSON (in array format)
            $j = $response->json();
            // Step 011: access to forecast information (is an array)
            $forecast = $j["forecasts"]["forecastLocation"]["forecast"];
            // Step 012: looping the forecasts
            foreach ($forecast as $key => $value) {
                // Step 013: output of each forecast
                $this->info($value["daySegment"]. " ". $value["description"]. " - Temperature: " . $value["temperature"] . " - Date: ". $value["utcTime"]);
            }
        } else {
            // Step 014: managing some errors
            if ($response->clientError()) {
                $this->error("Error performing request: ".$response->getStatusCode());
            } elseif ($response->serverError()) {
                $this->error("Error from Server: ".$response->getStatusCode());
            } else {
                $this->error("Error! ".$response->getStatusCode());
            }
        }
    }
}
