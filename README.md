# moodle-block_catalogue
Supports Moodle 2.7 up to 3.3.1 (at least).

Plug-in for Moodle, the well-known Learning Management System. Provides a visual and central place for a teacher to access everything he can use in his course (activities, reports, blocks, …) Frequently used items can be marked as favorites for quick access. Unless the teacher wants to delete or move an activity or block, he no longer needs to switch to editing mode.
Students can also use the Catalogue to access the tools they're allowed to.

No need, for users, to learn a first method to add an activity, then a different one to add a block, then a third one to use enrolment methods, and so on, searching the four corners of the UI... With Catalogue, it's the same method for all of these tools and they all can be found at the same place.

A manager interface is included. There, managers can :
-	Edit the descriptions.
-	Edit the documentation links.
-	Hide or show items to the teachers. We don't recommend you hide too many tools. Better let the teachers choose which ones are their favorites. But if you want to hide a tool and test it before showing it to the teachers, that's possible.

9 lists of tools are available. The site administrator can choose which ones of these lists will be displayed in the block, and in which order.

- Activities : divided in 3 categories (Exercises, Collaboration, Other)
- Blocks : 3 categories (Monitor learners, Communicate with learners, Other)
- Custom labels : Requires the plug-in "mod_customlabels", by Valery Fremaux. 3 categories (Pedagogical elements, Structural elements, Other elements)
- Enrolments : 2 categories (Users and groups, Enrolment methods)
- Grades : 3 categories (Grade settings, Grade reports, "Outcomes, competencies, badges")
- Modules : Activities and Resources together. 4 categories (Resources, Exercises, Collaboration, Other).
Of course, you're not supposed to use "Modules" and "Activities" at the same time on the same site. 
- Reports : 1 category
- Resources : 1 category

The 9th list is now deprecated and will be replaced in a future version. Therefore, its icon hasn't been updated in version 2.0.
Please avoid using this list :
- Sections : 1 category (Manage sections without switching to editing mode)

Authors :
- Brice Errandonea <brice.errandonea@u-cergy.fr> (development, general idea, project lead)
- Caroline Pers <caroline.pers@u-cergy.fr> (external communication, user experience refinments, feature requests, beta-test)
- Salma El-mrabah <salma.el-mrabah@u-cergy.fr> (development, version 1.0)
- Nirina Andriamanantenasoa <nirina.andriamanantenasoa@u-cergy.fr> (element icons and descriptions, versions 1.x)
- Baptiste Bail <baptiste.bail@u-cergy.fr> (list icons, version 2.0)
- Noa Randriamalaka <noa.randriamalaka@u-cergy.fr> (SEFIAP director)

 SEFIAP
 Université de Cergy-Pontoise
 33, boulevard du Port
 95011 Cergy-Pontoise cedex
 FRANCE
 https://sefiap.u-cergy.fr/

What's new in version 2.0 ?
- Course map : Navigate throughout your course.
- When you're inside an activity or resource, you can immediatly browse to the next (or previous) one by clicking an arrow, without a detour through the course main page.
- When you add an activity, resource or custom label, you can choose the exact place where you want to put it. Not necessarily at the end of a section.
- New list icons. One main color for each list.
- Subcategories are now displayed in columns rather than in rows.
- If editing mode is active, you can remove favorites directly from the block.
- More responsive design.
- Fixed a bug that prevented to find modules' description strings under some configurations.
- Support for Moodle 3.3

