<?php

namespace App\Services;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\LocalFileDetector;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\Facades\Storage;

class ServiceSelenium
{
    private $capabilities;
    private $driver;

    public function __construct()
    {
        //'http://localhost:4444';
        $seleniumUrl = 'http://selenium:4444'; //$_ENV['SELENIUM_URL'];
        $options = new ChromeOptions();
        // Arguments '--disable-gpu', '--headless=new'
        $options->addArguments(['--start-maximized']);
        $this->capabilities = DesiredCapabilities::chrome();
        $this->capabilities->setCapability(ChromeOptions::CAPABILITY_W3C, $options);
        $this->driver = RemoteWebDriver::create($seleniumUrl, $this->capabilities);
    }

    public function accessPage()
    {
        $this->driver->get($_ENV['ACCESS_PAGE']);
        $tblTags = $this->driver->findElement(WebDriverBy::id('mytable'))->findElements(WebDriverBy::cssSelector('tr:not(:has(th))'));

        $data = [];
        foreach ($tblTags as $tblTag) {
            $cols = $tblTag->findElements(WebDriverBy::cssSelector('td'));
            $row = [
                'name' => $cols[0]->getText(),
                'amount' => $cols[1]->getText()
            ];
            array_push($data, $row);
        }

        return $data;
    }

}
