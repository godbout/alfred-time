[![Codacy Badge](https://api.codacy.com/project/badge/Grade/b36ee46f72194137a165d6311d450919)](https://www.codacy.com/app/godbout/alfred-time?utm_source=github.com&utm_medium=referral&utm_content=godbout/alfred-time&utm_campaign=badger)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/godbout/alfred-time/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/godbout/alfred-time/?branch=master)
[![StyleCI](https://styleci.io/repos/83111813/shield?branch=master)](https://styleci.io/repos/83111813)
[![Build Status](https://www.travis-ci.org/godbout/alfred-time.svg?branch=master)](https://www.travis-ci.org/godbout/alfred-time)

(Code is working on php5.5 but travis fails due to some of the tests tools requiring php7.0. Will figure out later how to handle that)

# Alfred-Time

Basic workflow to start and stop timers for [Toggl](https://toggl.com/) and/or [Harvest](https://www.getharvest.com/) services through [Alfred](http://alfredapp.com/).

# Why

I prefer using Toggl for tracking time, I like their dashboard and reports better. But I use Harvest for invoices. So instead of handling both services separately (😴), I did the workflow so that I can control both services in one shot.

# Current features

* Generate and open default workflow config file at first start
* Launch edit of workflow config file through Alfred if needed
* Start timer for Toggl and/or Harvest (defined in config) with default project/task/tag
* Start timer for Toggl with choice of project and tag (currently only for Toggl, and only one single tag choosable)
* Stop current timer for Toggl and/or Harvest (defined in config)
* Undo last timer for Toggl and Harvest (if a current timer is running, will stop it and delete it. It no timer is running, delete the last ran timer)
* Delete a recent timer (currently only for Toggl)
* Continue a recent timer: actually start a new timer but with the same info (description, project, tag) (currently only for Toggl)
* Sync project and tag data to local cache (currently only for Toggl)

# How to use

## Features

* First start, generate and edit config file

![config](https://github.com/godbout/alfred-time/blob/master/resources/screenshots/time-set.gif)

* Edit config file when needed

![edit](https://github.com/godbout/alfred-time/blob/master/resources/screenshots/time-edit.gif)

* Start timer with default project and tag (with cmd modifier)

![start-default](https://github.com/godbout/alfred-time/blob/master/resources/screenshots/time-start-default.gif)

* Start timer with choice of project and tag

![start](https://github.com/godbout/alfred-time/blob/master/resources/screenshots/time-start.gif)

* Stop timer

![stop](https://github.com/godbout/alfred-time/blob/master/resources/screenshots/time-stop.gif)

* Undo last timer

![undo](https://github.com/godbout/alfred-time/blob/master/resources/screenshots/time-undo.gif)

* Delete a recent timer

![delete](https://github.com/godbout/alfred-time/blob/master/resources/screenshots/time-delete.gif)

* Continue a recent timer

![continue](https://github.com/godbout/alfred-time/blob/master/resources/screenshots/time-continue.gif)

* Sync projects and tags to local cache (automatic on first time, then you can resync manually)

![sync](https://github.com/godbout/alfred-time/blob/master/resources/screenshots/time-sync.gif)

## Config file

The config file contains 3 sections:

1. "workflow": you don't need to touch this, this is used as a cache by the workflow
2. "toggl": this is where you enter your Toggl info:
  * "is_active": true if you want to use Toggl, false otherwise
  * "api_token": your API Token to connect to Toggl (found in Toggl account settings)
  * "default_project_id": ID of the default project for your Toggl timers (if you want one)
  * "default_tags": list of default tags for your Toggl timers (if you want any)
3. "harvest": this is were you enter your Harvest info:
  * "is_active": true if you want to use Harvest, false otherwise
  * "api_token": this is a base64 encode of your Harvest credentials. You can encode online here: [base64 encode](https://www.base64encode.org/). Type "YOUR_HARVEST_USERNAME:YOUR_HARVEST_PASSWORD", encode, and paste the result in the config file.
  * "domain": your Harvest domain (https://DOMAIN.harvestapp.com/welcome)
  * "default_project_id": ID of the default project for your Harvet timers (if you want one)
  * "default_task_id": Id of the default task for your Harvet timers (if you want one)

# Todo

* Allow choice of project and task for Harvest
* See reports directly in Alfred
* See info about current running timer (project, tags/tasks, running time, etc...)
* Add better tests suite

# Contribute

* Feel free to let me know if something doesn't work, if you think something could be done better, or just fork and pull request.

# Download

* Workflow is downlodable on the [Release page](https://github.com/godbout/alfred-time/releases)
