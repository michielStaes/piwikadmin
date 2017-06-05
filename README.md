# Grav Admin Piwik Analytics Plugin

The **Piwik Admin** Plugin for [Grav CMS](http://github.com/getgrav/grav) allows you to integrate and configure [Piwik Analytics](https://piwik.org/) without the need to touch any code within your Grav site.

### Features
* Access your Piwik data on your Grav Admin Plugin
* Manage multiple dashboards
* Use Token Authentication
* Preload the Piwik Analytics script asynchronously
* Track Visitors across subdomains
* Track Users without Javascript
* Custom Variables support.
* Client Side "DoNotTrack" detection
* Disable tracking Cookies
* Multi-Language Support for the [Grav Administration Panel](https://github.com/getgrav/grav-plugin-admin) - WORK IN PROGRESS

## Installation

Installing the Piwik Admin plugin can be done in one of two ways. The GPM (Grav Package Manager) installation method enables you to quickly and easily install the plugin with a simple terminal command, while the manual method enables you to do so via a zip file.

### GPM Installation (Preferred)

The simplest way to install this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's terminal (also called the command line).  From the root of your Grav install type:

    bin/gpm install piwikadmin

This will install the Google Analytics plugin into your `/user/plugins` directory within Grav. Its files can be found under `/your/site/grav/user/plugins/piwikadmin`.

### Manual Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `piwikadmin`. You can find these files on [GitHub](https://github.com/ricardo118/piwikadmin) or via [GetGrav.org](http://getgrav.org/downloads/plugins).

You should now have all the plugin files under

    /your/site/grav/user/plugins/piwikadmin

> NOTE: This plugin is a modular component for Grav which requires [Grav](http://github.com/getgrav/grav) to operate.

## Configuration

> NOTE: Recommended way of configuration is via The [Grav Administration Panel](https://github.com/getgrav/grav-plugin-admin).


Before configuring this plugin, you should copy the `user/plugins/piwikadmin/piwikadmin.yaml` to `user/config/plugins/piwikadmin.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: disabled
hosted_url: ""
trackingId: 1
token_auth: ""

track_subdomains: false
prepend_domain: false
hide_alias: false
track_disabled_js: false

track_custom_vars: false
do_not_track: false
disable_tracking_cookies: false
```

* `enabled` Global enable/disable the entire plugin

* `hosted_url` The URL where your piwik is hosted on. Use Http:// or Https://. This value is **required**.
* `trackingId` PIWIK Tracking ID. This value is **required**.
* `token_auth` PIWIK Token AUTH. This value is **required**.

* `track_subdomains` Track visitors across all subdomains. So if one visitor visits x.example.com and y.example.com, they will be counted as a unique visitor.
* `prepend_domain` Prepend the site domain to the page title when tracking. So if someone visits the 'About' page on blog.example.com it will be recorded as 'blog / About'. This is the easiest way to get an overview of your traffic by sub-domain.
* `hide_alias` In the "Outlinks" report, hide clicks to known alias URLs of SITE.
* `track_disabled_js` Track users with JavaScript disabled.

* `track_custom_vars` Track custom variables for this visitor. For example, with variable name 'Type' and value 'Customer'. MAXIMUM 5 Variables.
* `do_not_track` Enable client side DoNotTrack detection
* `disable_tracking_cookies`: Disables Tracking Cookies.

## Usage

> NOTE: This plugin assumes you already have piwik hosted somewhere. If you don't have piwik hosted, you can follow this guide to do so [Piwik Install Guide](https://piwik.org/docs/installation-optimization/).


1. Access and Login (use a Super User) to your Piwik Installation E.g. http://yourwebsite.com/piwik
2. Select the **Settings** menu.
3. Select **Settings** on the sidebar menu under **Personal**
4. Find your **API Authentication Token** and take a note of this. You will need it to configure your plugin.
5. Under **Websites** > **Manage** you will see a list of your websites. If you have not yet added your Grav website as a new website, please do so.
6. Find your Grav Site on the list and make a note of the **ID** value, you will need this to configure your plugin.
7. Add **API Authentication Token, ID** to the configuration of this plugin.
8. Add the URL of your installation. In this example, http://yourwebsite.com/piwik to the plugin configuration
