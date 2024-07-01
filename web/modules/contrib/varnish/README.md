CONTENTS OF THIS FILE
=====================

 * Introduction
 * Requirements
 * Installation
 * Configuration
 * Maintainers

INTRODUCTION
------------

This module provides integration between your Drupal site and Varnish cache, an advanced and very fast reverse-proxy system. Varnish is a HTTP accelerator that sits in front of your application web-servers and handles serving static files and anonymous page-views for your site.
Varnish is incredibly fast and efficient, but like all caching systems, it needs help to perform invalidations.

This module provides integration with Varnish via it's socket-based administration interface, which allows Drupal to dynamically invalidate cache entries, and also lets you query the Varnish admin interface for status, etc.


REQUIREMENTS
------------

This module requires the PHP sockets extension. If you are unable to enable that, then the Varnish purge module provides an approach using a HTTP client for invalidation.


INSTALLATION
------------

  * Install as you would normally install a contributed Drupal module. See:
  https://www.drupal.org/node/1897420 for further information.



CONFIGURATION
-------------

* Go to Home >> Administration >> Configuration >> Development >> Varnish (/admin/config/development/varnish).

Here you can select your Varnish version and configure options for:

- Varnish Control Terminal: Set this to the server IP or hostname that varnish
  runs on (e.g. 127.0.0.1:6082). This must be configured for Drupal to talk to
  Varnish. Separate multiple servers with spaces.
- Varnish Control Key: if you have established a secret key for control terminal
  access, please put it here.
- Varnish connection timeout: If Varnish is running on a different server, you
  may need to increase this value.
- Varnish Cache Clearing: What kind of cache clearing Varnish should utilize.
  The Drupal default will clear all page caches on node updates and cache flush
  events. None will allow pages to persist for their full max-age; use this if you
  want to write your own cache-clearing logic.
- Varnish ban type: Select the type of varnish ban you wish to use. Ban lurker
  support requires you to add beresp.http.x-url and beresp.http.x-host entries to
  the response in vcl_fetch.


MAINTAINERS
-----------

* Colin Campbell (deadbeef) - (https://www.drupal.org/u/deadbeef)
* Mikke Schiren (MiSc) - (https://www.drupal.org/u/misc)
* JeremyFrench - (https://www.drupal.org/u/jeremyfrench)
* Josh Koenig (joshk) - (https://www.drupal.org/u/joshk)
* Fabian Sorqvist (fabsor) - (https://www.drupal.org/u/fabsor)
* Steven Jones - (https://www.drupal.org/u/steven-jones)


This project is supported by

* Wunder Group (http://drupal.org/wunder-group)
