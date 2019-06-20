
v0.5.0

- Breaking make Environment::create look for the environment in $_ENV first
- Breaking add addOverride method to the ConfigInterface
- Add new Override\Env override for overriding the config values with environment variables such as those 
from `# export MM_NAME=Bob`
- Add new Override\Args override for overriding the config values with cli arguments such as those 
from `# php my-script.php MM_NAME=Bob`

v0.4.0

- Breaking change to the Environment constructor to allow an environment to be set manually for testing etc.
- Add `Environment::create($root)` factory to replace new `Environment($root)` usage.
- Add __invoke access to Config to allow `$config('db')` access

v0.3.0

- Add support for property replacement in the get method e.g. `$config->get('db.${db.default}');`
- Add support for relative property replacement in the get method e.g. `$config->get('db.${.default}');`

v0.2.0

- Change environment file from .env to .environment to avoid clashes with DotEnv

v0.1.0

- First pre-release of the config
