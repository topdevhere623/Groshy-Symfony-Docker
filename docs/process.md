### Prerequisites 
 - Install docker-compose and git locally
 - Make sure your work email is added to the jira and bitbucket, if not communicate the blocker
 - Make sure you can open main Kanban dashboard: https://etepia.atlassian.net/jira/software/projects/GROSH/boards/4.
 - Make sure you can checkout the main repo: git@bitbucket.org:wizardz/groshy.git

### Installation
 - Close the repo git@bitbucket.org:wizardz/groshy.git
 - Start docker compose, it takes time to build some containers first time
```
docker-compose up -d
```
- URLs:
 - `http://localhost/` - website
 - `http://localhost/api` - API documentation
 - `http://localhost:8026/` - phpMyAdmin
 - `http://localhost:9201/` - MailHog
 - `http://localhost:8080/dashboard/#/` - traffik dashboard

### Process
 - Make sure you have last version of the code and database. Before working on something run
```
git checkout master && git pull --rebase
``` 
 - Update container and database
```
docker-compose exec groshy_php composer install
docker-compose exec groshy_php sh bin/reset-dev.sh
``` 
 - Create new branch for the ticket, it should start from the ticket key, eg "PRON-1-add-default-values"
```
git checkout -b PRON-1-add-default-values
```
 - Fix all code style issues. Ping me if you've used a better schema in the past and we can improve here
```
docker-compose exec groshy_php ./vendor/bin/ecs --fix
```
 - Commit you code. Every timeframe longer than 2 hours should have at least one commit. But do not commit every 10 min, it creates unnecessary noise
 - If you are 2x from original estimation, commit your current state, STOP your time tracker and send me a message.
 - Push your code to the server
```
git push -u origin PRON-1-add-default-values
```
 - Create PR, include ticket number in the PR. All functionality should be tested before creating the pull request
 - Move ticket to the "WAITING FOR CODE REVIEW" state, do not change ticket owner. At the end we want to run stats for engineers.
 - Add time spent to the ticket (click ... in the top right corner and choose Log Work)
 - Ideally it should only be 1-2 tickets in the "IN PROGRESS" state unless there is a serious blocker
 - After PR is approved merge the PR and move ticket to the "DONE" state
 -- Usually PRs have few comments and do not require long back and fourth
 -- For all approved PRs please address all additional comments and merge
 -- All "nit" comments are optional, use your judgement to define we need to implement them