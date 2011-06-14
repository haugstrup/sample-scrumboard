# Podio API Sample: Virtual Scrumboard
This is a simple example of how you can build specialized visual interfaces on top of Podio. It creates a virtual scrumboard based on Podio. You control all your sprints inside Podio and use the virtual scrumboard to get an overview of the state for your current sprint.

This allows all team members to stay on the same page even if they are not in the same physical location. Perfect for split teams or just for when you are working from home. The virtual scrumboard also uses the reporting widgets to calculate the sum of hours left in the current sprint and tells you if the team is on track or not.

When you open the scrumboard you will see a quick overview of all the features to be implemented. A bullet graph shows the current progress and you can glance at how many cards are done. Clicking on a feature shows a traditional scrumboard for that feature. You can use drag and drop to move story cards from state to state and work progresses and any changes will of course be reflected on Podio. You can even attach responsible developers so you can see on the scrumboard who is responsible for a story card.

This PHP app was built using the [Limonade](http://www.limonade-php.net/) micro-framework and the [Podio PHP client](https://github.com/podio/podio-php).

# Creating your Podio apps
You will need three apps to run the scrumboard: Sprints (sets the daterange for a sprint), Stories (for each feature to be implemented) and Story Items (for each task inside a feature). You can name the apps and fields anything, but they should contain the following fields as a minimum:

## Sprints app
* Date field: Use it to set a start and end time for each sprint
* State field with two allowed values ("Active" and "Inactive"). Use it to mark the current active sprint

## Stories app
* App field: Set it to reference the sprint that the story is contained in
* Contact field: Set it to mark who is the Product Owner for the story

## Story Items app
* App field: Set it to reference the story that each story item is a part of
* Duration field: Use it to record the time estimate (in hours) for the story item. Used to generate bullet graphs of the current progress.
* Duration field: Use it to record the time left (in hours) for the story item. Used to generate bullet graphs of the current progress.
* State field: Create it with 4 allowed values ("Not started", "Dev started", "Dev done", "QA done" and "PO done"). This controls which column the item is shown in on the scrumboard. You can create other states if these don't fit you. The scrumboard should adapt accordingly
* Contact field: Set the responsible developer for each story item

# Setting up the scrumboard
* Add some content to your apps
* Copy config.php.example to config.php and add your credentials for Podio and add the appropriate IDs from your Podio apps. Nb. You can find field IDs by inspecting the data attributes when viewing the item create/edit form.
* Run index.php in your browser