<?php

namespace WeatherStation;

/**
 * 观察者接口。
 */
interface Observer
{
    /**
     * 更新观察者信息（所有观察者都必须实现update方法，以实现观察者接口）。
     *
     * @param float $temp     温度。
     * @param float $humidity 湿度。
     * @param float $pressure 气压。
     */
    public function update($temp, $humidity, $pressure);
}