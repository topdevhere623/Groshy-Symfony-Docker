# Testing

### How to test local emails
Docker compose requires image of MailHog and makes it available at [http://localhost:9201/](http://localhost:9201/)
UI shows all messages sent by the system

Current test suite utilizes [DoctrineTestBundle](https://github.com/dmaicher/doctrine-test-bundle) so database changes between different tests are not committed

### How to run all unit tests
```
docker-compose exec groshy_php php bin/phpunit
```

### How to reset test database
```
docker-compose exec groshy_php ./bin/reset-test.sh
```

### Test users
 - user0/user0
 - user1/user1
 - user2/user2
 - user3/user3
 - user4/user4
 - user5/user5
 - user6/user6
 - user7/user7
 - user8/user8
 - user9/user9

### Use cases
1. No assets and liabilities: `user0`
2. REIT with dividends: `user1`
3. REIT with dividends reinvestment: `user2`
4. Always with liabilities: `user4`
5. Negative net value: `user5`
6. Private sponsors: `user1` and `user2`