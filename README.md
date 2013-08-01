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
* [Test configured providers](#test-configured-providers)
* [Twig functions](#twig-functions)

<a name="installation"></a>

## Installation

Installation is a quick 5 step process:

1. Download MremiUrlShortenerBundle using composer
2. Enable the Bundle
3. Create your Link class (optional)
4. Configure the MremiUrlShortenerBundle
5. Update your database schema (optional)

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
    link_class:   Mremi\UrlShortener\Model\Link

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

            <argument type="service" id="mremi_url_shortener.link_manager" />
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

<a name="twig-functions"></a>

## Twig functions

You can also simply shorten/expand a URL from a twig file. But It should be
used with caution because it's not HTTP friendly.

``` html+jinja
{# src/Acme/YourBundle/Resources/views/index.html.twig #}

{{ mremi_url_shorten('bitly', 'http://www.google.com') }}
{{ mremi_url_expand('google', 'http://goo.gl/fbsS') }}
```
