<?php
/**
 *  气象数据类。
 */

namespace WeatherStation;


class WeatherData implements Subject
{
    /**@param array $observers  已注册的观察者。*/
    private $observers;
    /**@param float $temperature 温度。*/
    private $temperature;
    /**@param float $humidity 湿度。*/
    private $humidity;
    /**@param float $pressure 气压。*/
    private $pressure;

    /**
     * 注册观察者。
     *
     * @param Observer $observer 待注册的观察者。
     */
    public function registerObserver(Observer $observer)
    {
        $this->observers[] = $observer;
    }

    /**
     * 移除观察者。
     *
     * @param Observer $observer 待移除的观察者。
     */
    public function removeObserver(Observer $observer)
    {
        $id = array_search($observer, $this->observers);
        if ($id) {
            unset($this->observers[$id]);
        }
    }

    /**
     * 通知所有注册的观察者。
     */
    public function notifyObservers()
    {
        foreach ($this->observers as $observer) {
            $observer->update($this->temperature, $this->humidity, $this->pressure);
        }
    }

    public function measurementsChanged()
    {
        $this->notifyObservers();
    }

    public function setMeasurements($temperature, $humidity, $pressure)
    {
        $this->temperature = $temperature;
        $this->humidity = $humidity;
        $this->pressure = $pressure;
        $this->measurementsChanged();
    }
}