AppLike Challenge
=================

# Running

In the first time docker will build and download all necessary images, this can take some minutes.

```
docker-compose up -d
```

In the first time that you run you need to create database schema, so type:
```
docker-compose exec php bin/console doctrine:schema:update --force
```

Now, the server is running on port 8000, to test access your browser http://localhost:8000 or type:
```
curl http://localhost:8000
```

## Adding new records to database

To add, type:
```
curl http://localhost:8000/people -d name="Guilherme Santos" -d birthday="1988-08-12"
```

## Listing records

To list all records type:
```
curl http://localhost:8000/people
```

## Getting record

To get just one specific record by id, type:
```
curl http://localhost:8000/people/<RECORD_ID>
```

## Send record to RabbitMQ

To send a record already added to rabbitmq, just type:
```
curl -XPOST http://localhost:8000/people/<RECORD_ID>/send
```

## Generating dummies records

To generate a bunch (100 by default) of records, type:
```
curl -XPOST http://localhost:8000/people/dummies # 100 by default
curl http://localhost:8000/people/dummies -d total=<TOTAL_RECORDS>
```

## Reading from RabbitMQ

To read from RabbitMQ you can type the follow command. It'll block forever waiting for messages
```
docker-compose exec php bin/console rabbitmq:read person
```

With you want to read a specific number of message, try:
```
docker-compose exec php bin/console rabbitmq:read -m <NUMBER_MESSAGES> person
```
