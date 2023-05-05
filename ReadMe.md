### Install

    $ git clone https://github.com/yariks0n/MinKitTest.git
    $ cd MinKitTest
    $ make up

### Prepare env
###### migrations, fixtures, keys 
    $ make prepare-env

### Analyse
    $ make phpcs
    $ make phpstan

### Tests
    $ make unit-tests
    $ make api-tests
    