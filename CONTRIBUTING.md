# Contributing

## Local development environment

The plugin ships with a [`wp-env`](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/) configuration, so you can spin up a local WordPress site with the plugin already installed and activated. It requires a running [Docker](https://www.docker.com/) installation.

### Setup

```bash
composer install
npm install
npm run build
npm run env:start
```

The site is then available at <http://localhost:8888> (admin credentials: `admin` / `password`). The plugin will not load until `composer install` and `npm run build` have been run, since it checks for `build/index.js` and `vendor/autoload.php` and shows an admin notice otherwise.

While developing, run the asset watcher in a second terminal:

```bash
npm start
```

### Useful commands

| Command | Description |
| --- | --- |
| `npm run env:start` | Start the environment |
| `npm run env:stop` | Stop the environment |
| `npm run env:logs` | Tail the container logs |
| `npm run env:cli <args>` | Run a WP-CLI command, e.g. `npm run env:cli plugin list` |
| `npm run env:clean` | Reset the databases of both environments |
| `npm run env:destroy` | Remove the environment including all its data |

If the default ports `8888` (development) and `8889` (tests) are already in use on your machine, create a `.wp-env.override.json` file (it is ignored by Git) and set a different `port` and `testsPort` there.

### Mailchimp API key

To see any data in the block, you need to enter a Mailchimp API key in the block settings in the editor.
