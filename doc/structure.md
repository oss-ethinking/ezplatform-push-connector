## Bundle Structure

We have the `bundle` and `lib` folder to separate the symfony standard from our business logic

### bundle

This is a pure symfony bundle structure, where we can define views, configurations and some standards.


### lib

We will try to separate eZ Platform and the push service code in this folder.

#### operations example

Interact with eZ Platform Repository (e.g user, content etc), those classes will be saved in the `Repository` folder

Backend extension in `UI` folder

for the push service we will have two axis of integration

- Client Requests
- Model classes to interact between the Client and the different views

