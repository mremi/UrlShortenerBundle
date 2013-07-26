MremiUrlShortenerBundle
=======================

[![Build Status](https://api.travis-ci.org/mremi/UrlShortenerBundle.png?branch=master)](https://travis-ci.org/mremi/UrlShortenerBundle)
[![Total Downloads](https://poser.pugx.org/mremi/url-shortener-bundle/downloads.png)](https://packagist.org/packages/mremi/url-shortener-bundle)
[![Latest Stable Version](https://poser.pugx.org/mremi/url-shortener-bundle/v/stable.png)](https://packagist.org/packages/mremi/url-shortener-bundle)

This bundle implements the UrlShortener library for Symfony2.

## Prerequisites

This version of the bundle requires Symfony 2.1+.

**Basic Docs**

* [Installation](#installation)
* [Chain providers](#chain-providers)
* [Custom provider](#custom-provider)

<a name="installation"></a>

## Installation

Installation is a quick 3 step process:

1. Download MremiUrlShortenerBundle using composer
2. Enable the Bundle
3. Configure the MremiUrlShortenerBundle

### Step 1: Download MremiUrlShortenerBundle using composer

Add MremiUrlShortenerBundle in your composer.json:

```js
{
    "require": {
        "mremi/url-shortener-bundle": "dev-master"
    }
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ php composer.phar update mremi/url-shortener-bundle
```

Composer will install the bundle to your project's `vendor/mremi` directory.

### Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Mremi\UrlShortenerBundle\MremiUrlShortenerBundle(),
    );
}
```

### Step 3: Configure the MremiUrlShortenerBundle

Fow now, you just have to configure your Bit.ly username and password.

```yaml
# app/config/config.yml
mremi_url_shortener:
    bitly:
        enabled:  true
        username: your_bitly_username
        password: your_bitly_password

    google:
        enabled: true
        api_key: your_api_key
```

<a name="chain-providers"></a>

## Chain providers

One service allow you to shorten/expand URL, to use like this:

```php
<?php

$chainProvider = $container->get('mremi_url_shortener.chain_provider');

$shortened = $chainProvider->getProvider('bitly')->shorten('http://www.google.com');

$expanded = $chainProvider->getProvider('google')->expand('http://goo.gl/fbsS');
```

<a name="custom-provider"></a>

## Custom provider

You can add your own provider to the chain providers:

1. Create a service which implements `\Mremi\UrlShortener\Provider\UrlShortenerProviderInterface`
2. Add the tag `mremi_url_shortener.provider`

``` xml
<?xml version="1.0" ?>

<container xmlns="http://symfony.com/schema/dic/services"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

    <services>
        <service id="acme.custom_provider" class="Acme\YourBundle\Provider\CustomProvider">
            <tag name="mremi_url_shortener.provider" />
        </service>
    </services>
</container>
```
