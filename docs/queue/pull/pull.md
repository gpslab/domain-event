Pull queue
==========

Queues are designed to distribute the load and delay publishing of events or transfer their publishing to separate
processes.

Pull is a [FIFO](https://en.wikipedia.org/wiki/FIFO_(computing_and_electronics)) queue. Pull queue is
designed to explicitly pull events from the queue. You can do this on a timer through
[cron](https://en.wikipedia.org/wiki/Cron).

The implementation of such a queue is very simple, but it has a number of shortcomings:

* Delays the execution of events due to the timer;
* Calling to the queue wasted due to the absence of messages in the queue;
* Increase network activity;
* Load increase.

To solve these problems, we recommend using a [Subscribe](../subscribe/subscribe.md) queue.

You can use one of the existing queues:

* [Memory queue](memory.md)
* [Predis queue](predis.md)
