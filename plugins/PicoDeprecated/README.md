Pico Deprecated Plugin
======================

This is the repository of Pico's official `PicoDeprecated` plugin.

Pico is a stupidly simple, blazing fast, flat file CMS. See http://picocms.org/ for more info.

`PicoDeprecated`'s purpose is to maintain backward compatibility to older versions of Pico, by re-introducing characteristics that were removed from Pico's core. It for example triggers old events (like the `before_render` event used before Pico 1.0) and reads config files that were written in PHP (`config/config.php`, used before Pico 2.0).

Please refer to [`picocms/Pico`](https://github.com/picocms/Pico) to get info about how to contribute or getting help.

Install
-------

You usually don't have to install this plugin manually, it's shipped together with [Pico's pre-built release packages](https://github.com/picocms/Pico/releases/latest) and a default dependency of [`picocms/pico-composer`](https://github.com/picocms/pico-composer).

If you're using plugins and themes that are compatible with Pico's latest API version only, you can safely remove `PicoDeprecated` from your Pico installation or disable the plugin (please refer to the "Usage" section below). However, if you're not sure about this, simply leave it as it is - it won't hurt... :wink:

If you use a `composer`-based installation of Pico and want to either remove or install `PicoDeprecated`, simply open a shell on your server and navigate to Pico's install directory (e.g. `/var/www/html`). Run `composer remove picocms/pico-deprecated` to remove `PicoDeprecated`, or run `composer require picocms/pico-deprecated` (via [Packagist.org](https://packagist.org/packages/picocms/pico-deprecated)) to install `PicoDeprecated`.

If you rather use one of Pico's pre-built release packages, it is best to disable `PicoDeprecated` and not to actually remove it. The reason for this is, that `PicoDeprecated` is part of Pico's pre-built release packages, thus it will be automatically re-installed when updating Pico. However, if you really want to remove `PicoDeprecated`, simply delete the `plugins/PicoDeprecated` directory in Pico's install directory (e.g. `/var/www/html`). If you want to install `PicoDeprecated`, you must first create a empty `plugins/PicoDeprecated` directory on your server, [download the version of `PicoDeprecated`](https://github.com/picocms/pico-deprecated/releases) matching the version of your Pico installation and upload all containing files (esp. `PicoDeprecated.php` and the `lib/`, `plugins/` and `vendor/` directories) into said `plugins/PicoDeprecated` directory (resulting in `plugins/PicoDeprecated/PicoDeprecated.php`).

The versioning of `PicoDeprecated` strictly follows the version of Pico's core. You *must not* use a version of `PicoDeprecated` that doesn't match the version of Pico's core (e.g. PicoDeprecated 2.0.1 is *not compatible* with Pico 2.0.0). If you're using a `composer`-based installation of Pico, simply use a version constaint like `^2.0` - `PicoDeprecated` ensures that its version matches Pico's version. Even if you're using one of Pico's pre-built release packages, you don't have to take care of anything - a matching version of `PicoDeprecated` is part of Pico's pre-built release packages anyway.

Usage
-----

You can explicitly disable `PicoDeprecated` by adding `PicoDeprecated.enabled: false` to your `config/config.yml`. If you want to re-enable `PicoDeprecated`, simply remove this line from your `config/config.yml`. `PicoDeprecated` itself has no configuration options, it enables and disables all of its features depending on whether there are plugins and/or themes requiring said characteristics.

`PicoDeprecated`'s functionality is split into various so-called "compatibility plugins". There are compatibility plugins for every old API version (Pico 0.9 and earlier were using API version 0, Pico 1.0 was using API version 1 and Pico 2.0 was using API version 2; the current API version is version 3, used by Pico 2.1), one for plugins and another one for themes. Their purpose is to re-introduce characteristics plugins and themes using said API version might rely on. For example, plugin API compatibility plugins are responsible for simulating old Pico core events (like the `before_render` event used by Pico 0.9 and earlier). Theme API compatibility plugins will e.g. register old Twig variables (like the `is_front_page` Twig variable used by Pico 1.0). If you install a plugin using API version 2, the corresponding `PicoPluginApi2CompatPlugin` will be loaded. All plugin API compatibility plugins also depend on their theme counterpart, thus `PicoThemeApi2CompatPlugin` will be loaded, too. Furthermore all compatibility plugins depend on their respective API successors.

The plugin exposes a simple API to allow other plugins to load their own compatibility plugins. As a plugin developer you may use the `PicoDeprecated::loadCompatPlugin(PicoCompatPluginInterface $compatPlugin)` method to load a custom compatibility plugin. Use `PicoDeprecated::getCompatPlugins()` to return a list of all loaded compatibility plugins. You can furthermore use the `PicoDeprecated::getPlugins(int $apiVersion)` method to return a list of all loaded Pico plugins using a particular API version. If you want to trigger a custom event on plugins using a particular API version only, use `PicoDeprecated::triggerEvent(int $apiVersion, string $eventName, array $parameters = [])`. `PicoDeprecated` furthermore triggers the custom `onPicoDeprecated(PicoDeprecated $picoDeprecated)` event.

Getting Help
------------

Please refer to the ["Getting Help" section](https://github.com/picocms/Pico#getting-help) of our main repository.

Contributing
------------

Please refer to the ["Contributing" section](https://github.com/picocms/Pico#contributing) of our main repository.

By contributing to Pico, you accept and agree to the *Developer Certificate of Origin* for your present and future contributions submitted to Pico. Please refer to the ["Developer Certificate of Origin" section](https://github.com/picocms/Pico/blob/master/CONTRIBUTING.md#developer-certificate-of-origin) in the `CONTRIBUTING.md` of our main repository.
