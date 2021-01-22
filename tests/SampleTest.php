<?php

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\WebDriverPlatform;
use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverBy;

class SampleTest extends TestCase {

    public function testSample() {

        // selenium
        $host = 'http://192.168.33.10:4444/wd/hub';

        $options = new ChromeOptions();
        $options->addArguments(["--headless", "--disable-gpu", "--no-sandbox"]);

        $desiredCapabilities = DesiredCapabilities::chrome();
        $desiredCapabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        $desiredCapabilities->setCapability(WebDriverCapabilityType::PLATFORM, WebDriverPlatform::LINUX);
        
        // chrome ドライバーの起動
        $driver = RemoteWebDriver::create($host, $desiredCapabilities);
        // 指定URLへ遷移 (Google)
        $driver->get('https://www.google.com/');
        // 検索Box
        $element = $driver->findElement(WebDriverBy::name('q'));
        // // 検索Boxにキーワードを入力して
        $element->sendKeys('サンプル');
        // // 検索実行
        $element->submit();

        // 検索結果画面のタイトルが 'セレニウムで自動操作 - Google 検索' になるまで10秒間待機する
        $driver->wait(10)->until(
            WebDriverExpectedCondition::titleIs('サンプル - Google 検索')
        );

        //assert
        $this->assertEquals('サンプル - Google 検索', $driver->getTitle());

        // ブラウザを閉じる
        $driver->close();
    }
}
