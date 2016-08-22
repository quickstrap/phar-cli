# PharCli
PharCli is a command line tool phar package--it's main feature is that new commands can be added without updating the phar.

Commands are added by installing Symfony Bundles that contain the new commands. The gimmick, is that this can be done without updating
the console application to register the new bundle or bundle commands. How does it do this? PharCli is self modifying, 
when you install new bundles they are downloaded and added to the phar package and registered automatically.

## How is this helpful?
In order to demonstrate how PharCli can be useful, lets take a look a a few use cases.

### Authoring a new command line tool

### Authoring a new extension for an existing tool
All you need to do is write a Symfony Bundle with the new commands you want to add.
Publish your bundle on composer as a package.

#### Installing the extension
`php phar-cli.phar require mypackage/tool-extensions`
