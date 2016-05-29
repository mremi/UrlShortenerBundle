MremiUrlShortenerBundle
=======================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/ce0d627a-7dbe-49ab-a247-bf21b53b0afe/big.png)](https://insight.sensiolabs.com/projects/ce0d627a-7dbe-49ab-a247-bf21b53b0afe)

[![Build Status](https://api.travis-ci.org/mremi/UrlShortenerBundle.png?branch=master)](https://travis-ci.org/mremi/UrlShortenerBundle)
[![Total Downloads](https://poser.pugx.org/mremi/url-shortener-bundle/downloads.png)](https://packagist.org/packages/mremi/url-shortener-bundle)
[![Latest Stable Version](https://poser.pugx.org/mremi/url-shortener-bundle/v/stable.png)](https://packagist.org/packages/mremi/url-shortener-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mremi/UrlShortenerBundle/badges/quality-score.png?s=a9e232e7ec75d70c038950b2f1aa72b313a31271)](https://scrutinizer-ci.com/g/mremi/UrlShortenerBundle/)
[![Code Coverage](https://scrutinizer-ci.com/g/mremi/UrlShortenerBundle/badges/coverage.png?s=ddfb206093586a764b3fc9459a0cde20c108e547)](https://scrutinizer-ci.com/g/mremi/UrlShortenerBundle/)

This bundle implements the [UrlShortener](https://github.com/mremi/UrlShortener) library for Symfony.

## License

This bundle is available under the [MIT license](Resources/meta/LICENSE).

## Prerequisites

This version of the bundle requires Symfony 2.8, 3.0 or newer.

For compatibility with Symfony 2.7 or earlier, please use 1.0.* versions of
this bundle.

**Basic Docs**

* [Installation](#installation)
* [Chain providers](#chain-providers)
* [Custom provider](#custom-provider)
* [Test configured providers](#test-configured-providers)
* [Retrieve link](#retrieve-link)
* [Twig functions](#twig-functions)
* [Profiler](#profiler)
* [Contribution](#contribution)

<a name="installation"></a>

## Installation

Installation is a quick 5 step process:

1. Download MremiUrlShortenerBundle using composer
2. Enable the Bundle
3. Create your Link class (optional)
4. Configure the MremiUrlShortenerBundle
5. Update your database schema (optional)

### Step 1: Download MremiUrlShortenerBundle using composer

Require `mremi/url-shortener-bundle` via composer:

```bash
php composer.phar require mremi/url-shortener-bundle

```

Note: if you are using Symfony 2.7 or earlier, please require ~1.0.0 version:

```bash
php composer.phar require mremi/url-shortener-bundle:~1.0.0
```

Composer will modify your composer.json file and install the bundle to your
project's `vendor/mremi` directory.

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

### Step 3: Create your Link class (optional)

The goal of this bundle is not to persist some `Link` class to a database,
but you can if you want just by following next instructions.
So if you don't need to do this, you can jump to the next step.

Your first job, then, is to create the `Link` class for your application.
This class can look and act however you want: add any properties or methods you
find useful. This is *your* `Link` class.

The bundle provides base classes which are already mapped for most fields
to make it easier to create your entity. Here is how you use it:

1. Extend the base `Link` class from the ``Entity`` folder
2. Map the `id` field. It must be protected as it is inherited from the parent
   class
3. Add index on `long_url` column: Doctrine does not allow to specify index
   size in the mapping, so you have to write it manually in a migration
   class.

**Note:**

> For now, only Doctrine ORM is handled by this bundle (any PR will be
> appreciated :) ).

``` php
<?php
// src/Acme/UrlShortenerBundle/Entity/Link.php

namespace Acme\UrlShortenerBundle\Entity;

use Mremi\UrlShortenerBundle\Entity\Link as BaseLink;

class Link extends BaseLink
{
    /**
     * @var integer
     */
    protected $id;
}
```

``` xml
<!-- src/Acme/UrlShortenerBundle/Resources/config/doctrine/Link.orm.xml -->
<?xml version="1.0" encoding="UTF-8"?>
<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                  xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                  http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

    <entity name="Acme\UrlShortenerBundle\Entity\Link"
            table="link">

        <id name="id" column="id" type="integer">
            <generator strategy="AUTO" />
        </id>

    </entity>
</doctrine-mapping>
```

``` php
<?php
// app/DoctrineMigrations/VersionYYYYMMDDHHIISS.php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class VersionYYYYMMDDHHIISS extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // customize index size as you want...
        $this->addSql("CREATE INDEX idx_url_shortener_link_long_url ON link (long_url(20))");
    }

    public function down(Schema $schema)
    {
        $this->addSql("DROP INDEX idx_url_shortener_link_long_url ON link;");
    }
```

### Step 4: Configure the MremiUrlShortenerBundle

Fow now, you just have to configure your Bit.ly username and password.

```yaml
# app/config/config.yml
mremi_url_shortener:
    link_class: Mremi\UrlShortener\Model\Link

    providers:
        bitly:
            enabled:             true
            username:            your_bitly_username
            password:            your_bitly_password
            options:
                connect_timeout: 1
                timeout:         1

        google:
            enabled:             true
            api_key:             your_api_key
            options:
                connect_timeout: 1
                timeout:         1
```

### Step 5: Update your database schema (optional)

If you configured the data storage (step 3), you can now update your database
schema.

If you want to first see the create table query:

``` bash
$ app/console doctrine:schema:update --dump-sql
```

Then you can run it:

``` bash
$ app/console doctrine:schema:update --force
```

<a name="chain-providers"></a>

## Chain providers

One service allow you to shorten/expand URL, to use like this:

```php
<?php

$linkManager   = $container->get('mremi_url_shortener.link_manager');
$chainProvider = $container->get('mremi_url_shortener.chain_provider');

$link = $linkManager->create();
$link->setLongUrl('http://www.google.com');

$chainProvider->getProvider('bitly')->shorten($link);

$chainProvider->getProvider('google')->expand($link);
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

<a name="test-configured-providers"></a>

## Test configured providers

You can now test the providers you configured with the following command line:

``` bash
$ app/console mremi:url-shortener:test
```

<a name="retrieve-link"></a>

## Retrieve link

You can retrieve some links using these finders:

```php
<?php

$linkManager = $container->get('mremi_url_shortener.link_manager');

$shortened = $linkManager->findOneByProviderAndShortUrl('bitly', 'http://bit.ly/ze6poY');

$expanded = $linkManager->findOneByProviderAndLongUrl('google', 'http://www.google.com');
```

If you configured the data storage (steps 3 & 5), finders look first in
database ; if the link exists then return it, otherwise an API call will be
done and link will be saved.

Else this will consume an API call.

<a name="twig-functions"></a>

## Twig functions

You can also simply shorten/expand a URL from a twig file. It should be used
with caution if no data storage is configured, because it's not HTTP friendly.

``` html+jinja
{# src/Acme/YourBundle/Resources/views/index.html.twig #}

{{ mremi_url_shorten('bitly', 'http://www.google.com') }}
{{ mremi_url_expand('google', 'http://goo.gl/fbsS') }}
```
<a name="profiler"></a>

## Profiler

If your are in debug mode (see your front controller), you can check in the web
debug toolbar the configured providers and some statistics from the current
HTTP request: number of requests per provider, consumed memory, request
duration...

![Screenshot](https://raw.github.com/mremi/UrlShortenerBundle/master/Resources/doc/images/profiler.png)

<a name="contribution"></a>

## Contribution

Any question or feedback? Open an issue and I will try to reply quickly.

A feature is missing here? Feel free to create a pull request to solve it!

I hope this has been useful and has helped you. If so, share it and recommend
it! :)

[@mremitsme](https://twitter.com/mremitsme)
