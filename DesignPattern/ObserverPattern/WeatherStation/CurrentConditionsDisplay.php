<?php
/**
 * 布告板。
 */

namespace ObserverPattern\WeatherStation;

class CurrentConditionsDisplay implements Observer, DisplayElement
{
    /**@param Subject $weatherData  气象数据实例。*/
    private $weatherData;
    /**@param float $temperature 温度。*/
    private $temperature;
    /**@param float $humidity 湿度。*/
    private $humidity;
    /**@param float $pressure 气压。*/
    private $pressure;

    public function __construct(WeatherData $weatherData)
    {
        $this->weatherData = $weatherData;
        $weatherData->registerObserver($this);
    }

    /**
     * 更新观察者信息。
     *
     * @param float $temp     温度。
     * @param float $humidity 湿度。
     * @param float $pressure 气压。
     */
    public function update($temp, $humidity, $pressure)
    {
        $this->temperature = $temp;
        $this->humidity = $humidity;
        $this->pressure = $pressure;
        $this->display();
    }

    /**
     * 显示。
     */
    public function display()
    {
        echo "Current coonditions: {$this->temperature}F degree and {$this->humidity}% humidity and {$this->pressure}P pressure\n";
    }
}