WhiteOctoberAdminBundle
=======================

Installation
------------

You have to install this bundle as usual:

> git submodule add git://github.com/whiteoctober/WhiteOctoberAdminBundle.git vendor/bundles/WhiteOctober/AdminBundle

You also have to install [**Pagerfanta**](https://github.com/whiteoctober/Pagerfanta) with its bundle:

> git submodule add git://github.com/whiteoctober/Pagerfanta.git vendor/pagerfanta

> git submodule add git://github.com/whiteoctober/WhiteOctoberPagerfantaBundle.git vendor/bundles/WhiteOctober/PagerfantaBundle

Register namespaces in your `autoload.php`:

``` php
// app/autoload.php
$loader->registerNamespaces(array(
// ...
    'WhiteOctober' => __DIR__.'/../vendor/bundles',
    'Pagerfanta'   => __DIR__.'/../vendor/pagerfanta/src',
    // ...
));
```

Register bundles in your `AppKernel` class:

``` php
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
        new WhiteOctober\AdminBundle\WhiteOctoberAdminBundle(),
    );
}
```

Add these lines to your `app/config/routing.yml` file:

``` yaml
_white_october_admin:
    resource: .
    type: white_october_admin

white_october_admin:
    resource: "@WhiteOctoberAdminBundle/Resources/config/routing/admin.xml"
```

Here you go ! Pretty easy isn't it ?


Configuration
-------------

### Data managers

WhiteOctoberAdminBundle provides three data managers:

* Doctrine ORM
* Doctrine ODM
* Mandango

You have to choose at least one of them. You can add your own.

Edit your `app/config/config.yml` file:

``` yaml
# app/config/config.yml
white_october_admin:
    data_managers:
        doctrine:
            orm:    true
            odm:    false
        mandango:   false
```

Set `true` for each data manager you want to load.

Next, you have to declare your admin classes:

``` yaml
# app/config/config.yml
white_october_admin:
    ...
    admins:
        - { class: My\Bundle\Admin\MyClassAdmin }
```


### Admin classes

An *admin class* must extend `WhiteOctober\AdminBundle\Admin\Admin`.

This bundle provides some *admin* classes for each data manager:

* WhiteOctober\\AdminBundle\\DataManager\\Doctrine\\ORM\\Admin\\DoctrineORMAdmin
* WhiteOctober\\AdminBundle\\DataManager\\Doctrine\\ODM\\Admin\\DoctrineODMAdmin
* WhiteOctober\\AdminBundle\\DataManager\\Mandango\\Admin\\MandangoAdmin

Example:

``` php
<?php

namespace My\Bundle\Admin;

use WhiteOctober\AdminBundle\DataManager\Doctrine\ORM\Admin\DoctrineORMAdmin;

class MyClassAdmin extends DoctrineORMAdmin
{
    protected function configure()
    {

    }
}
```


To finish, you have to define a comportment the `configure()` method.

``` php
protected function configure()
{
    $this
        ->setDataClass('MyBundle\Entity\Article')
        ->addActions(array(
            'doctrine.orm.crud',
        ))
        ->addFields(array(
            'title',
            'content',
            'isActive',
            // ...
        ))
    ;
}
```

You have to set a data class which can be your entity class with the `setDataClass()` method.

You also have to define actions (crud, create, list, edit, update, ...) in the `configure()` method.


### Actions

Each data manager provides a set of actions:

* CRUDActionCollection
* CreateAction
* DeleteAction
* EditAction
* ListAdmin
* NewAction
* UpdateAction

To use one of them, just add it by using the `addAction()` method.

Each action is named like: *xxxxx.action* where *xxxxx* is the data manager (*mandango*, *doctrine.orm* or *doctrine.odm*)
and action is the lowered word before *Action*.


### Fields

You should specify fields by using the `addFields()` method.
