<?php

namespace TemplateMethodPattern\CaffeineBeverage;

class Tea extends CaffeineBeverage
{
    public function brew()
    {
        echo "Steepign the tea!\n";
    }

    public function addCondiments()
    {
        echo "Adding Lemon!\n";
    }
}