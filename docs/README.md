# OpenApi-Docs

This project allows you to build the API documentation.  
Documentation is build using apigen. 


## Requirements

- PHP 5.3.7 or greater.
- Make
- A clone of OpenApi located in ../OpenApi
- git


After cloning the repository make sure you install the git submodules:


```
git submodule init
git submodule update
```

After installing the submodules, you'll need to have a clone of OpenApi as well. 

## Building the documentation

To build the documentation you will need to use make. You can build all the documentation using:

```
make build
```

By default the api-docs assume that ../OpenApi is a git clone of OpenApi.  
Documentation will be output to ./build/api. If you want to change these directories you can use the SOURCE_DIR and BUILD_DIR directories:

```
make build SOURCE_DIR=../cake BUILD_DIR=../api-output
```

Is an example of using custom directories.
