# moodle-block_catalogue
Plug-in for Moodle, the well-known Learning Management System. Provides a visual and central place for a teacher to access everything he can use in his course (activities, reports, blocks, …) Frequently used items can be marked as favorites for quick access. Unless the teacher wants to delete or move an activity or block, he no longer needs to switch to editing mode.
Students can also use the Catalogue to access the tools they're allowed to.

No need, for users, to learn a first method to add an activity, then a different one to add a block, then a third one to use enrolment methods, and so on, searching the four corners of the UI... With Catalogue, it's the same method for all of these tools and they all can be found at the same place.

A manager interface is included. There, managers can :
-	Edit the descriptions.
-	Edit the documentation links.
-	Hide or show items to the teachers. We don't recommend you hide too many tools. Better let the teachers choose which ones are their favorites. But if you want to hide a tool and test it before showing it to the teachers, that's possible.

Authors : Brice Errandonea <brice.errandonea@u-cergy.fr> (development), Salma El-mrabah <salma.el-mrabah@u-cergy.fr> (development, version 1.0), Caroline Pers (external communication, many ideas and feature requests, version 2.0) <caroline.pers@u-cergy.fr>, Baptiste Bail (list icons, version 2.0) <baptiste.bail@u-cergy.fr>

 Université de Cergy-Pontoise
 33, boulevard du Port
 95011 Cergy-Pontoise cedex
 FRANCE
 https://www.u-cergy.fr
 
Successfully tested on Moodle 2.6.1+, 2.9, 2.9.4, 3.1+, 3.3.1

9 lists of items are available. The site administrator can choose which ones of these tool lists will be displayed in the block, and in which order.

- Activities : divided in 3 categories (Exercises, Collaboration, Other)
- Blocks : 3 categories (Monitor learners, Communicate with learners, Other)
- Custom labels : Requires the plug-in "mod_customlabels", by Valery Fremaux. 3 categories (Pedagogical elements, Structural elements, Other elements).
- Enrolments : 2 categories (Users and groups, Enrolment methods)
- Grades : 3 categories (Grade settings, Grade reports, "Outcomes, competencies, badges")
- Modules : Activities and Resources together. 4 categories (Resources, Exercises, Collaboration, Other)
Of course, you're not supposed to use "Modules" and "Activities" at the same time on the same site. 
- Reports : 1 category
- Resources : 1 category
The 9th one is now deprecated and will be replaced in a future version. Therefore, its icon hasn't been updated in version 2.0.
Please avoid using this list :
- Sections : 1 category (Manage sections without switching to editing mode)

What's new in version 2.0 ?
- Course map : Navigate throughout your course.
- When you're inside an activity or resource, you can immediatly browse to the next (or previous) one by clicking an arrow, without a detour through the course main page.
- When you add an activity, resource or custom label, you can choose the exact place where you want to put it. Not necessarily at the end of a section.
- New list icons. One main color for each list.
- Subcategories are now displayed in columns rather than in rows.
- If editing mode is active, you can remove favorites directly from the block.
- More responsive design.
- Fixed a bug that prevented to find module's description strings under some configurations.
- Support for Moodle 3.3

What's new in version 1.2 ?
- Students can't see lists at all and the few items they can use are all favorites. (new "viewlists" capability)
- If a given user, who has the "viewlists" capability, can only see one element in a given list, this unique element is marked as favorite and the list is displayed only in the manager interface.
- Non-editing teachers can view the block.
- On upgrade, permissions are set to their new default values.
- Bug fix : Under some circumstances, toggling favorites didn't work.
- Bug fix : No more warning when detecting a plugin that's unknown to the Moodle's plugin directory.
- Bug fix : the way CSS and javascript were called could cause troubles with some themes.
- No more french strings in the lang folder. Translations are managed by Moodle's AMOS system.
- The block can now appear on any page of the site, not just course home pages.
- New setting : "Course home pages only". Set it if you want to restore the previous behaviour.
- New setting : a background color for the block (choose one that fits your theme).

What's new in version 1.1 ?
- 2 new available lists : Modules and Sections. Of course, if you use the "Modules" list on your site, you're not supposed to use the "Resources" and "Activities" ones.
- You can change the block's displayed title (replace "Catalogue" by something else).
- All language strings are now gathered in the main lang directory.
- Better capabilities checking.
- Students can now see the block, but only a few lists and elements, depending on their capabilities.
- If a given user (e.g. a student) can only see one element in a given list, this unique element is marked as a favorite and the list is not displayed.
- If your theme customs the mod icons, these customed icons will also be used in the Catalogue.
- Fixed bug : in some cases, the list names in the last row were not displayed in the block.
- On it's section selection screen, element "section_goto" shows you the h1, h2 and h3 titles each section contains, thus helping you choosing the section you want to reach if it's name alone doesn't speak enough (e.g. weekly course format) and providing you with a table of contents for your course.

