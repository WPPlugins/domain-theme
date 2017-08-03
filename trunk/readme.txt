=== Plugin Name ===
Contributors: Stephen Carroll
Donate link: http://www.virtuosoft.net/domaintheme
Tags: domain, domains, URL, url, http, SERVER_NAME, interface, host, hosting, address, style, CSS, theme, themes, skin
Requires at least: 2.2
Tested up to: 2.6.1
Stable tag: 1.3

Domain Theme allows you to specify more then one domain name with your WordPress installation and assign individual themes to each one. 

== Description ==

Domain Theme allows you to specify more then one domain name with your WordPress installation and optionally associate a specific theme for each domain. This enables you to present your site under multiple domain names. (i.e. www.mypersonalblog.com and www.myprofessionalblog.com). A specific theme template can be assigned to a specific domain. For instance, you can use this plugin for a personal blog that has a casual theme under one domain and and present the same data under a corporate theme that omits select categories (i.e. family photos, etc.). You will need to modify your DNS settings to point your domain name(s) to the same server to make this happen.  


Known Issues:

* Upgrade users from WordPress 2.6 to WordPress 2.6.1 who used version 1.2 may have incompatible domain data. Please delete and recreate your domain list.

* All-In-One SEO users should delete their "Home title" setting otherwise it will overwrite the blog title settings in Domain Theme.

* Please note that accessing wp-admin pages from any other domain name then your primary domain specified in 'General Settings' will cause the administration panel to report the 'themed' settings.  Just remember to access your 'General Settings', 'Presentation', and 'Domain Theme' admin pages under your primary domain name if you want to make such changes.

* Users before version 2.5 will have a minor UI issue on the Domain Theme control panel's 'delete all' checkbox column. It will not check off all listed items.

* The first item in the Domain Theme list cannot be deleted as it is the settings you have for your primary domain taken from the 'General Settings' and 'Presentation-Theme' page. Clicking it to edit will simply transfer you to the 'General Settings' page. This is on purpose.

* Some users may have issues with their server variables SERVER_NAME versus HTTP_HOST. Simply uncomment the first line of code in the plugin if your additional domain names do not appear to work. See the comment on the first line of code.

== Installation ==

1. Upload `domainTheme.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Modify your DNS settings to point your domain name(s) to the same server.
4. Go to the Domain Theme administration panel to add your domain names (i.e. www.example.com)

== Frequently Asked Questions ==

= Can I specify a different Blog Title and Tagline for each domain name? =

Yes. There are options to enter a Blog Title and Tagline that will override what is specified in 'General Settings' for a given domain name.

= Can I use the same theme for more then one domain name? =

Yes. Use the Add Domain form to specify a single domain name and select the theme to use from theme drop down menu. 

= Can I use a different set of widgets for each domain name? =

No. Widgets are defined at the site level and will appear on all supported themes. However, almost anything is possible from within theme templates and you could just alter a theme template to omit or allow certain widgets manually.

= How can I point my new domain name(s) to my existing site? =

You will need to modify your DNS settings (and possibly server settings). This may require you to contact your ISP to ensure that your site allows multiple domain names per server.

== Screenshots ==

1. The Domain Theme control panel.


