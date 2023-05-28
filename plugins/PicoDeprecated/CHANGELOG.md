Pico Deprecated Changelog
=========================

**Note:** This changelog only provides technical information about the changes
          introduced with a particular Pico version, and is meant to supplement
          the actual code changes. The information in this changelog are often
          insufficient to understand the implications of larger changes. Please
          refer to both the UPGRADE and NEWS sections of the docs for more
          details.

**Note:** Changes breaking backwards compatibility (BC) are marked with an `!`
          (exclamation mark). This doesn't include changes for which BC is
          preserved by this plugin. If a previously deprecated feature is later
          removed, this change is going to be marked as BC-breaking change.
          Please note that BC-breaking changes are only possible with a new
          major version.

**Note:** Many versions of `PicoDeprecated` include changes which are not
          explicitly mentioned in this changelog. This primarily concerns
          changes in Pico's plugin API. These changes aren't listed separately
          because they are already listed in Pico's changelog. Only functional
          changes and/or BC-breaking changes are listed below.

### Version 2.1.4
Released: 2020-08-29

No changes

### Version 2.1.3
Released: 2020-07-10

No changes

### Version 2.1.2
Released: 2020-04-10

No changes

### Version 2.1.1
Released: 2019-12-31

No changes

### Version 2.1.0
Released: 2019-11-24

No changes

### Version 2.1.0-beta.1
Released: 2019-11-03

```
* [New] Add support for the latest API v3 changes
* [New] Support disabled Twig autoescape prior to API v3
* [New] Re-introduce `theme_url` config variable
* [New] Re-introduce `prev_page`, `base_dir` and `theme_dir` Twig variables
* [New] Support loading additional plugins using API v1 `onPluginsLoaded` event
* [New] Re-introduce Pico v0.9 config constant `CACHE_DIR`
* [New] Add release & build system to test the plugin using PHP_CodeSniffer and
        to automatically create pre-built release packages
* [Changed] Split the plugin's functionality into multiple compatibility
            plugins (two for each API version, for plugins and themes resp.)
            and load the necessary compatibility plugins on demand only; also
            allow 3rd-party plugins to load their own compatibility plugins
```

### Version 2.0.5-beta.1
Released: 2019-01-03

```
* [New] Add `2.0.x-dev` alias for master branch to `composer.json`
```

### Version 2.0.4
Released: 2018-12-17

No changes

### Version 2.0.3
Released: 2018-12-03

No changes

### Version 2.0.2
Released: 2018-08-12

No changes

### Version 2.0.1
Released: 2018-07-29

No changes

### Version 2.0.0
Released: 2018-07-01

No changes

### Version 2.0.0-beta.3
Released: 2018-04-07

No changes

### Version 2.0.0-beta.2
Released: 2018-01-21

```
* [New] Add support for the latest API v2 changes
* [New] ! Add support for themes using the old `.html` file extension for Twig
        templates; however, starting with API v2 plugins might rely on `.twig`
        as file extension, making this a BC-breaking change regardless
```

### Version 2.0.0-beta.1
Released: 2017-11-05

**Note:** Pico's official `PicoDeprecated` plugin was moved to this separate
          repository in preparation for Pico 2.0. Refer to Pico's changelog for
          a list of changes to this plugin before Pico 2.0.

```
* [New] Update plugin to API v2 and add support for all API v1 events
* [New] Keep track of all loaded Pico plugins and distinguish them by the API
        version they use; deprecated events are only triggered on plugins using
        this particular API version (`PicoDeprecated::API_VERSION_*` constants)
* [New] Take care of triggering events on plugins using older API versions;
        this includes not only core events, but also all custom events; as a
        result, old plugin's always depend on `PicoDeprecated` now
* [New] Use a simple event alias table to keep track of unchanged or just
        renamed core events
* [New] Add `rewrite_url` and `is_front_page` Twig variables
* [New] Add support for the `config/config.php` configuration file
* [New] Additionally compare registered meta headers case-insensitive
* [New] Make meta headers on the first level of a page's meta data also
        available using a lowered key (as of Pico 1.0; i.e. `SomeKey: value` is
        now accessible using both `$meta['SomeKey']` and `$meta['somekey']`)
* [New] Add public `PicoDeprecated::triggersApiEvents()` method
* [New] Add public `PicoDeprecated::triggerEvent()` method (and the additional
        `$apiVersion` parameter) as replacement for the previously protected
        method of the same name
* [Fixed] ! Don't overwrite the global `$config` variable if it is defined
* [Fixed] ! Improve re-indexing of pages added by the API v0 event `get_pages`
* [Changed] No longer try to guess whether the plugin needs to be enabled or
            not, rather enable it by default (guessing was pretty error-prone)
* [Changed] ! Use a scope-isolated `require()` to include configuration files
* [Changed] ! Don't pass `$plugins` parameter to API v1 `onPluginsLoaded` event
            by reference anymore; use `Pico::loadPlugin()` instead
* [Changed] ! The API v1 events `onTwigRegistration` and `onMetaHeaders`, as
            well as the API v0 event `before_twig_register` are no longer part
            of Pico's event flow and are triggered just once on demand
* [Changed] Improve PHP class docs
* [Changed] A vast number of small improvements and changes...
* [Removed] ! Remove support for `PicoParsePagesContent` plugin
* [Removed] ! Remove support for `PicoExcerpt` plugin
```
