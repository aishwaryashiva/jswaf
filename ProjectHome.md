_First thought: jswaf is NOT a javascript library!_

**Right now, there is a massive redign towards making jswaf much more programmer friendly
and much more flexible than before!**

# What is jswaf? #
jswaf is a highly modular web application container that can host modern AJAX web applications.

## What does jswaf do? ##
  * jswaf brings the web application development model pretty close a conventional desktop application development paradigm.
  * jswaf separates functionality from the UI. It also separates multiple functionalities and converts each aspect into a module. It (kind of) enforces an MVC model.
  * it is highly suited for developing ajax web applications.

## How does jswaf work? ##
jswaf has three primary abstractions: themes, modules and "pages".

  * themes pieces of html/css that decide the UI of the web application.
  * modules are basically chunks of structured object oriented javascript that exhibit particular functionality. they are (somehow) able to place themselves on the front end (in the browser) irrespective of the theme (explained in a moment)
  * a "page" is an application view (often the only view).

## Fundamentals of jswaf ##
  * each module requires some "resources" to exists, and it cannot be "initialized" unless those resources are available.
  * each module produces some "resources". These resources may be configuration items, DOM objects or javascript functions.
  * there is always a module that manages the theme. it produces resources (DOM objects) such as "header", "sidebar", "body", "navigation" etc. These resources are consumed by other modules. This is how modules are able to place themselves on themes automatically.
  * a central module manager serves as the “operating system” of jswaf, mediating communication, delegating control and allocating resources to modules.
