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
					- topic2

```

## Publish messages

```
    <?php
    class testPublish {
    
        protected $producerFactory;

        public function __construct(ProducerFactory $producerFactory)
        {
            $this->producerFactory = $producerFactory;
        }

        public function publishMessage($message){

            $producer = $this->producerFactory->getProducer($producerName,$producerTopic);
            $producer->publish($message);
        }
    }

```

## Consume messages

```
    <?php
    class testConsume {
    
        protected $consumerFactory;

        public function __construct(ConsumerFactory $consumerFactory)
        {
            $this->consumerFactory = $consumerFactory;
        }

        public function consumeMessages($topic)
        {
            
            $consumer = $this->consumerFactory->getConsumer('default','test',$topic);
        
            $consumer->setDebug(false);

            $consumer->addCallback([$this,'doOutputMessage']);
            $consumer->consume();

        }

        public function doOutputMessage($msg)
        {
            echo "{$msg}\n";
        }
    }

```


## Consuming and prodcuing messages trough CLI

You can consume messages from a specified topic and print messages to stdout by running CLI command below.

```
bin/console kafka:consumer test topic1

```

Following command will produce a message

```
bin/console kafka:producer test topic1 message
```
