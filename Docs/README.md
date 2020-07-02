# Kafka

Nette extension for Kafla (using Rdkafka)

## Installation

```
composer require izytechab/nette-extension-kafka
```

## Extension registration

config.neon:

```
extensions:
	kafka: Extensions\Kafka\DI\KafkaExtension   
```

## Example configuration

```
services:
	- TestConsumer

kafka:
		brokers: 
			- kafka
		producers:
			test:
				topics: 
					- topic1
					- topic2

		consumers:
			test:
				level: high
				topics: 
					- topic1
					- topic

```

## Publish and consume messages

```
	public function __construct(ProducerFactory $producerFactory)
	{
		$this->producerFactory = $producerFactory;
	}

    public function publishMessage($message){

        $producer = $this->producerFactory->getProducer($producerName,$producerTopic);
        $producer->publish($message);
    }

```
