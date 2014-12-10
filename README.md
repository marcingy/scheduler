# Scheduler [![Build Status](https://travis-ci.org/examiner/scheduler.svg?branch=8.x-1.x)](https://travis-ci.org/examiner/scheduler)

This module allows nodes to be published and unpublished on specified dates.

INSTALLATION
--------------------------------------------------------------------------
1. Copy the scheduler.module to your modules directory
2. Enable module.
3. Visit mysite/update.php and apply the necessary schema updates.
4. Grant users the permission "schedule (un)publishing of nodes" so they can
   set when the nodes they create are to be (un)published.
   
5. Visit admin > settings > content-types and click on any node type and
   check the box "enable scheduled (un)publishing" for this node type
   
6. Repeat for all node types that you want scheduled publishing for

The scheduler will run with Drupal's cron.php, and will (un)publish nodes
timed on or before the time at which cron runs.  If you'd like finer
granularity to scheduler, but don't want to run Drupal's cron more often (due
to its taking too many cycles to run every minute, for example), you can set
up another cron job for the scheduler to run independently.  Scheduler's cron
is at /scheduler/cron; a sample crontab entry to run scheduler every minute
would look like:

* * * * * /usr/bin/wget -O - -q "http://example.com/scheduler/cron"
