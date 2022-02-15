<?php

namespace WeatherStation;

/**
 * 主题接口。
 */
interface Subject
{
    /**
     * 注册观察者。
     *
     * @param Observer $observer 待注册的观察者。
     */
    public function registerObserver(Observer $observer);

    /**
     * 移除观察者。
     *
     * @param Observer $observer 待移除的观察者。
     */
    public function removeObserver(Observer $observer);

    /**
     * 通知所有注册的观察者。
     */
    public function notifyObservers();
}