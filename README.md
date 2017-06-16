# DDD Workshops

[![Build Status](https://travis-ci.org/tswiackiewicz/ddd-workshops.png?branch=master)](https://travis-ci.org/tswiackiewicz/ddd-workshops)

Sample application for Domain Driven Design workshops 

The AwesomeApp project uses CQRS approach. Each branch represents different persistence strategy:
  * master - via repository at application service level 
  * event-driven - with event handler
  * event-sourcing - entities are reconstituted from (previously stored) event stream 

It purposely avoids the use of an ORM or known frameworks. The point is to show the idea - pure PHP code implementation.

To keep the example simple it persists CQRS write and read model in single thread.  

Shared Kernel contains domain model shared between read and write model. 

Finally the src-ddd/ directory provides a number of reusable components. This could be considered as kind of "DDD framework" that can easily be shared between projects. This is not a recommendation, but it did work well and save a considerable amount of work while producing the samples.


## License

MIT


