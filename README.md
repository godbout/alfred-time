[![Codacy Badge](https://api.codacy.com/project/badge/Grade/b36ee46f72194137a165d6311d450919)](https://www.codacy.com/app/godbout/alfred-time?utm_source=github.com&utm_medium=referral&utm_content=godbout/alfred-time&utm_campaign=badger)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/godbout/alfred-time/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/godbout/alfred-time/?branch=master)
[![StyleCI](https://styleci.io/repos/83111813/shield?branch=master)](https://styleci.io/repos/83111813)
[![Build Status](https://www.travis-ci.org/godbout/alfred-time.svg?branch=master)](https://www.travis-ci.org/godbout/alfred-time)

# Alfred Time

Basic workflow to start and stop timers for [Toggl](https://toggl.com/) and/or [Harvest](https://www.getharvest.com/) services through [Alfred](http://alfredapp.com/).

# Why

I prefer using Toggl for tracking time, I like their dashboard and reports better. But I use Harvest for invoices. So instead of handling both services separately (😴), I did the workflow so that I can control both services in one shot.

# Current features

* Generate and open default workflow config file at first start
* Launch edit of workflow config file through Alfred if needed
* Start timer for Toggl OR Harvest with choice of project and tag (primary service defined in config, only one tag at each time choosable currently)
* Start timer for Toggl AND Harvest with choice of project and tag. In that case, will only show projects and tags that are available for both services
* Stop current timer for Toggl and/or Harvest (defined in config)
* Undo last timer for Toggl and Harvest (if a current timer is running, will stop it and delete it. It no timer is running, delete the last ran timer)
* Delete a recent timer
* Continue a recent timer: actually start a new timer but with the same info (description, project, tag)
* Sync project and tag data to local cache

# How to use

## Features

* First start, generate and edit config file

![config](https://github.com/godbout/alfred-time/blob/master/resources/screenshots/time-set.gif)

* Edit config file when needed

![edit](https://github.com/godbout/alfred-time/blob/master/resources/screenshots/time-edit.gif)

* Start timer for primary service

![start-default](https://github.com/godbout/alfred-time/blob/master/resources/screenshots/time-start-primary_service.gif)

* Start timer for all services (with shift modifier)

![start](https://github.com/godbout/alfred-time/blob/master/resources/screenshots/time-start-all_services.gif)

* Stop timer

![stop](https://github.com/godbout/alfred-time/blob/master/resources/screenshots/time-stop.gif)

* Undo last timer

![undo](https://github.com/godbout/alfred-time/blob/master/resources/screenshots/time-undo.gif)

* Delete a recent timer

![delete](https://github.com/godbout/alfred-time/blob/master/resources/screenshots/time-delete.gif)

* Continue a recent timer (with cmd modifier)

![continue](https://github.com/godbout/alfred-time/blob/master/resources/screenshots/time-continue.gif)

* Sync projects and tags to local cache (automatic on first time, then you can resync manually)

![sync](https://github.com/godbout/alfred-time/blob/master/resources/screenshots/time-sync.gif)

## Config file

The config file contains 3 sections:

1. "timer": the only thing you have to define here is as below:
  * "primary_service": enter the service that you want to use by default, when you don't press any modifier
2. "toggl": this is where you enter your Toggl info:
  * "is_active": true if you want to use Toggl, false otherwise
  * "api_token": your API Token to connect to Toggl (found in Toggl account settings)
3. "harvest": this is were you enter your Harvest info:
  * "is_active": true if you want to use Harvest, false otherwise
  * "api_token": this is a base64 encode of your Harvest credentials. You can encode online here: [base64 encode](https://www.base64encode.org/). Type "YOUR_HARVEST_USERNAME:YOUR_HARVEST_PASSWORD", encode, and paste the result in the config file.
  * "domain": your Harvest domain (the DOMAIN part in your Harvest url: https://DOMAIN.harvestapp.com)

# Todo

* See reports directly in Alfred
* See info about current running timer (project, tags/tasks, running time, etc...)
* Add better tests suite and refactor classes

# Contribute

* Feel free to let me know if something doesn't work, if you think something could be done better, or just fork and pull request.

# Download

* Workflow is downlodable on the [Release page](https://github.com/godbout/alfred-time/releases)

# Alternatives to Alfred Time

* [Alfred Harvest](https://github.com/tinystride/alfred-harvest) by Neil Renicker
* [Alfred Toggl](https://github.com/jason0x43/alfred-toggl) by Jason Cheatham
