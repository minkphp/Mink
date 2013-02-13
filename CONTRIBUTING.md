Contributing
------------

Mink is an open source, community-driven project. If you'd like to contribute, feel free to do this, but remember to follow this few simple rules:

- Make your feature addition or bug fix,
- __Always__ as base for your changes use `develop` branch (all new development happens here, `master` branch is for releases & hotfixes only),
- Add tests for those changes (please look into `tests/` folder for some examples). This is important so we don't break it in a future version unintentionally,
- Commit your code, but do not mess with `CHANGES.md`,
- __Remember__: when you create Pull Request, always select `develop` branch as target, otherwise it will be closed.

Running tests
-------------

Make sure that you don't break anything with your changes by running:

```bash
$> phpunit
```

Behat integration and translated languages
------------------------------------------

Behat integration altogether with translations have moved into separate
project called `MinkExtension`. It's an extension to Behat 2.4. This will
lead to much faster release cycles as `MinkExtension` doesn't have actual
releases - any accepted PR about language translation or new step definitions
will immediately go into live.
