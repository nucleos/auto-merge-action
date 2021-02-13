GitHub AutoMerge Action
=======================

[![Continuous Integration](https://github.com/nucleos/auto-merge-action/workflows/Continuous%20Integration/badge.svg?event=push)](https://github.com/nucleos/auto-merge-action/actions?query=workflow%3A"Continuous+Integration"+event%3Apush)
[![Code Coverage](https://codecov.io/gh/nucleos/auto-merge-action/graph/badge.svg)](https://codecov.io/gh/nucleos/auto-merge-action)
[![Type Coverage](https://shepherd.dev/github/nucleos/auto-merge-action/coverage.svg)](https://shepherd.dev/github/nucleos/auto-merge-action)

## Usage

You can create a new workflow that runs every day at 10 AM.

You should not use a very short interval otherwise you will reach the GitHub API limit.

```yaml
# .github/workflows/auto-merge.yml
on:
  schedule:
    - cron:  '0 * * * *'

name: "Automerge Pull Requests"

jobs:
  merge:
    name: "Merge labeled PRs"
    runs-on: ubuntu-latest

    steps:
    - name: "Automerge Action"
      uses: docker://nucleos/auto-merge-action
      env:
        "GITHUB_TOKEN": ${{ secrets.GITHUB_TOKEN }}
```
