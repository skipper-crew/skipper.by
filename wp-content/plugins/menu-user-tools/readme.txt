=== Menu User Tools ===
Author: Luke Wiedmer
Contributors: modyours
Donate link: http://modyours.com
Tags: Menu, Login, Logout, Register, Profile
Requires at least: 3.1.4
Tested up to: 3.2.1
Stable tag: 1.0 Beta

I coded this plugin in short for myself and my website alone. I came to realize that
there wasn't any such plugin that does something like this.

== Description ==

This plugin allows you to add the conditional Login/Logout, and Register/My Profile links to any 
custom WordPress menu you've created. You can add them selectively, and/or 
to more than one menu if necessary. (Requires more code, which I won't provide -yet.)

I haven't coded an 'admin backend' for this plugin yet, it's simply the core functions as of now.
I coded this for my own website http://webmasterguy.com in which I needed such a plugin.

To edit which menu it selects through the plugins IF statement, open 'Menu User Tools.php' and find this code:

** if($args->theme_location == 'top-menu') **

Change top-menu to the menu slug of the menu you would like to have Menu User Tools hook onto.
You must have all the menus registered in your themes functions.php

You can easily have Menu User Tools hook onto all menus, simply by removing the above IF statement in the plugin file.

If you want more functionality out of this plugin you're going to have to code it yourself for now.

== Installation ==

1. Upload `Menu User Tools` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. If you're using 'top-menu' as the menu you would like this plugin to hook onto then this
   plugin won't require any editing, otherwise you may have to change up some things.

Optional: Change the name of your menus, and the registered name in your themes 'functions.php' file