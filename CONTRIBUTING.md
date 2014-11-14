# Contributing
Mink is an open source, community-driven project. If you'd like to contribute, feel free to do this, but remember to follow these few simple rules:

## Submitting an issues
- __Driver-related__ issues must be reported in the corresponding driver repository
- A reproducible example is required for every bug report, otherwise it will most probably be __closed without warning__
- If you are going to make a big, substantial change, let's discuss it first

## Working with Pull Requests
1. Create your feature addition or a bug fix branch based on `master` branch in your repository's fork.
2. Make necessary changes, but __don't mix__ code reformatting with code changes on topic.
3. Add tests for those changes (please look into `tests/` folder for some examples). This is important so we don't break it in a future version unintentionally.
4. Commit your code, but do not mess with `CHANGES.md`.
5. Squash your commits by topic to preserve a clean and readable log.
6. Create Pull Request.

# Running tests
Make sure that you don't break anything with your changes by running:

```bash
$> vendor/bin/phpunit
```
