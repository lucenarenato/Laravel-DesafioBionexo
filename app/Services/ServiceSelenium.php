<?php

namespace App\Services;

use Exception;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\LocalFileDetector;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ServiceSelenium
{
    private $capabilities;
    private $driver;

    public function __construct()
    {
        $seleniumUrl = env('SELENIUM_URL');
        $options = new ChromeOptions();
        // Arguments '--disable-gpu', '--headless=new'
        $options->addArguments(['--start-maximized']);
        $this->capabilities = DesiredCapabilities::chrome();
        $this->capabilities->setCapability(ChromeOptions::CAPABILITY_W3C, $options);
        $this->driver = RemoteWebDriver::create($seleniumUrl, $this->capabilities);
    }

    public function accessPage()
    {
        $access = $this->driver->get(env('ACCESS_PAGE'));
        // buscar id da table "<table id="mytable">" -> nao pegar th
        $tblTags = $access->findElement(WebDriverBy::id('mytable'))
            ->findElements(WebDriverBy::cssSelector('tr:not(:has(th))'));

        $data = [];
        foreach ($tblTags as $tblTag) {
            $cols = $tblTag->findElements(WebDriverBy::cssSelector('td'));
            $col = [
                'name' => $cols[0]->getText(),
                'amount' => $cols[1]->getText()
            ];
            array_push($data, $col);
        }
        $this->driver->quit();
        return $data;
    }

    public function sendFormParamns($params)
    {
        try {
            sleep(3);
            $this->driver->get(env('ACCESS_FORM'));
            $this->driver->findElement(WebDriverBy::name('username'))->sendKeys($params['username']);
            $this->driver->findElement(WebDriverBy::name('password'))->sendKeys($params['password']);
            $this->driver->findElement(WebDriverBy::name('comments'))->clear()->sendKeys($params['comments']);
            sleep(3);
            $this->driver->findElement(WebDriverBy::name('filename'))
                ->setFileDetector(new LocalFileDetector())->sendKeys(Storage::disk('local_s3')->path('laravel.png'));

            $this->driver->findElement(WebDriverBy::xpath('//*[@id="HTMLFormElements"]/table/tbody/tr[5]/td/input[3]'))->click();
            $this->driver->findElement(WebDriverBy::xpath('//*[@id="HTMLFormElements"]/table/tbody/tr[5]/td/input[1]'))->click();
            $this->driver->findElement(WebDriverBy::xpath('//*[@id="HTMLFormElements"]/table/tbody/tr[5]/td/input[2]'))->click();
            // Radio
            $this->driver->findElement(WebDriverBy::xpath('//*[@id="HTMLFormElements"]/table/tbody/tr[6]/td/input[3]'))->click();
            $this->driver->findElement(WebDriverBy::xpath('//*[@id="HTMLFormElements"]/table/tbody/tr[7]/td/select/option[4]'))->click();
            $this->driver->findElement(WebDriverBy::xpath('//*[@id="HTMLFormElements"]/table/tbody/tr[8]/td/select/option[5]'))->click();
            // subimit
            $this->driver->findElement(WebDriverBy::xpath('//*[@id="HTMLFormElements"]/table/tbody/tr[9]/td/input[2]'))->click();
            $this->driver->quit();

            return 'Formulario preenchido com sucesso!';

        } catch(Exception $e) {
            $this->driver->quit();
			$error=$e->getMessage();
			echo 'Error Message: ' . substr($error,0,strpos($error,'Form documentation')) . "\n";
            Log::error('Error Message: ' . substr($error,0,strpos($error,'Form documentation')) . "\n");
            Log::error($error=$e->getMessage());
		}

    }

    public function directLinkDownload()
    {
        try{
            sleep(5);
            $this->driver->get(ENV('DIRECT_DOWNLOAD'));
            sleep(3);
            $this->driver->findElement(WebDriverBy::id('direct-download'))->click();
            sleep(3);
            $this->driver->quit();
            return 'Download feito com sucesso! textfile.txt';
        } catch(Exception $e) {
			$error = $e->getMessage();
            Log::error($error);
            throw $e;
		}

    }

    public function uploadFile(string $fileName)
    {
        try{
            sleep(5);
            $this->driver->get(ENV('UPLOAD_RENAME'));
            sleep(3);
            $this->driver->findElement(WebDriverBy::id('fileinput'))
                ->setFileDetector(new LocalFileDetector())->sendKeys(Storage::disk('local_s3')->path($fileName));
            $this->driver->findElement(WebDriverBy::id('itsafile'))->click();
            $this->driver->findElement(WebDriverBy::name('upload'))->click();
            sleep(2);
            $this->driver->quit();
            return 'Upload file feito com sucesso! Teste TKS.txt';

        } catch(Exception $e) {
			$error = $e->getMessage();
            Log::error($error);
            throw $e;
		}

    }

    public function testeUpload(string $fileName)
    {
        try{
            sleep(5);
            $rename = 'Teste TKS';
            sleep(5);
            $this->driver->get(ENV('DIRECT_DOWNLOAD'));
            sleep(3);
            $href = $this->driver->findElement(WebDriverBy::id('direct-download-a'))->getAttribute('href');
            $url = $this->driver->getCurrentURL();
            $url = parse_url($url, PHP_URL_HOST);
            $this->driver->quit();
            $href = "https://$url$href";
            Log::debug($href);
            file_put_contents("/var/www/html/storage/local_S3/$href", file_get_contents($href));
            sleep(2);
            $this->driver->quit();
            return 'Upload file feito com sucesso! Teste TKS.txt';

        } catch(Exception $e) {
			$error = $e->getMessage();
            Log::error($error);
            throw $e;
		}

    }

}
