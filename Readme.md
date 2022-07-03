Trail Course Format
============================
Trail format was developed based on the Grid format plugin. He distributes the grids on a trail. 
Some features were designed to meet some aspects of gamification, being able to change the background style
 and transparency for grids. In addition to having the responsibility for mobile equipment.


Required version of Moodle
==========================
This version works with Moodle 4.0 version (Build: 2022041900) and above within the 4.0.0.1 branch until the
next release.

Please ensure that your hardware and software complies with 'Requirements' in 'Installing Moodle' on
'docs.moodle.org/400/en/Installing_Moodle'.

Free Software
=============
The Trail format is 'free' software under the terms of the GNU GPLv3 License, please see 'COPYING.txt'.

You have all the rights granted to you by the GPLv3 license.  If you are unsure about anything, then the
FAQ - http://www.gnu.org/licenses/gpl-faq.html - is a good place to look.

If you reuse any of the code then I kindly ask that you make reference to the format.


Installation
============
1. Ensure you have the version of Moodle as stated above in 'Required version of Moodle'.  This is essential as the
   format relies on underlying core code that is out of my control.
2. Put Moodle in 'Maintenance Mode' (docs.moodle.org/en/admin/setting/maintenancemode) so that there are no
   users using it bar you as the administrator - if you have not already done so.
3. Copy 'trail' to '/course/format/' if you have not already done so.
4. Go back in as an administrator and follow standard the 'plugin' update notification.  If needed, go to
   'Site administration' -> 'Notifications' if this does not happen.
5. Put Moodle out of Maintenance Mode.
6. You may need to check that the permissions within the 'trail' folder are 755 for folders and 644 for files.

Uninstallation
==============
1. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
2. It is recommended but not essential to change all of the courses that use the format to another.  If this is
   not done Moodle will pick the last format in your list of formats to use but display in 'Edit settings' of the
   course the first format in the list.  You can then set the desired format.
3. In '/course/format/' remove the folder 'trail'.
4. In the database, remove the row with the 'plugin' of 'format_trail' and 'name' of 'version' in the 'config_plugins' table
   and drop the 'format_trail_icon' and 'format_trail_summary' tables.
5. Put Moodle out of Maintenance Mode.

