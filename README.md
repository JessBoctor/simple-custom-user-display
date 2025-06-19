# simple-custom-user-display

**Welcome to the development of a Simple Custom User Display boilerplate plugin!**

This goal of this plugin is to be a lightweight solution to displaying custom information about groups of people which are part of an organization.

This boiler plate plugin uses as much core WordPress functionality as possible. By creating custom user roles, we can make each group member's information its own dataset. This way, the group member information can be easily pulled, sorted, filtered, and displayed on the front end. By using WordPress users, updating the metadata for any one group member is simplified to just editing the user profile.

You can read more about the context here: https://jessboctor.com/2025/06/12/making-a-scud-plugin/

## What this plugin is:
- A boilerplate for you to customize and make your own
- An experiement in using DataViews within a plugin
- An excuse to make something new

## What this plugin is not:
- A WYSIWYG editor for creating new user roles or custom display layouts
- A fully translation ready plugin (sorry, my use case doesn't need it!)

# The to-do list
- [x] Create custom user roles
- [x] Create custom user profile fields
- [x] Make it possible to save the user metadata
- [x] Create custom user taxonomies
- [ ] Query the user tables for the group member data
- [ ] Display the user information in a DataView list
- [ ] Make certain metadata available to be filterable within the DataView list
