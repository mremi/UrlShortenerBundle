MremiUrlShortenerBundle
=======================

This bundle implements the UrlShortener library for Symfony2.

**Basic Docs**

* [Installation](#installation)
* [Bit.ly API](#bitly-api)

<a name="installation"></a>

## Installation

### Step 1) Get the bundle and the library

First, grab the UrlShortener library and MremiUrlShortenerBundle. There are two
different ways to do this:

#### Method a) Using composer (symfony 2.1 pattern)

Add on composer.json (see http://getcomposer.org/)

    "require": {
        // ...
        "mremi/url-shortener-bundle": "dev-master"
    }

#### Method b) Using the `deps` file (symfony 2.0 pattern)

Add the following lines to your  `deps` file and then run `php bin/vendors
install`:

```
[UrlShortener]
    git=https://github.com/mremi/UrlShortener
    target=Mremi/UrlShortener

[UrlShortenerBundle]
    git=https://github.com/mremi/UrlShortenerBundle
    target=bundles/Mremi/Bundle/UrlShortenerBundle
```

#### Method c) Using submodules

Run the following commands to bring in the needed libraries as submodules.

```bash
git submodule add https://github.com/mremi/UrlShortener vendor/Mremi/UrlShortener
git submodule add https://github.com/mremi/UrlShortenerBundle vendor/bundles/Mremi/Bundle/UrlShortenerBundle
```

### Step 2) Register the namespaces

If you installed the bundle by composer, use the created autoload.php  (jump to step 3).
Add the following two namespace entries to the `registerNamespaces` call
in your autoloader:

``` php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'Mremi' => array(
        __DIR__.'/../vendor',
        __DIR__.'/../vendor/bundles',
    ),
    // ...
));
```

### Step 3) Register the bundle

To start using the bundle, register it in your Kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Mremi\UrlShortenerBundle\MremiUrlShortenerBundle(),
    );
    // ...
}
```

### Step 4) Configure the bundle

Fow now, you just have to configure your Bit.ly username and password.

```yaml
# app/config/config.yml
mremi_url_shortener:
    bitly:
        username: your_bitly_username
        password: your_bitly_password
```

<a name="bitly-api"></a>

## Bit.ly API

One service allow you to shorten/expand URL, to use like this:

```php
<?php

$shortener = $container->get('mremi_url_shortener.bitly.shortener');

$shortened = $shortener->shorten('http://www.google.com');

$expanded = $shortener->expand('http://bit.ly/13TE0qU');
```
