GitHub AutoMerge Action
=======================

[![Continuous Integration](https://github.com/nucleos/auto-merge-action/workflows/Continuous%20Integration/badge.svg?event=push)](https://github.com/nucleos/auto-merge-action/actions?query=workflow%3A"Continuous+Integration"+event%3Apush)
[![Code Coverage](https://codecov.io/gh/nucleos/auto-merge-action/graph/badge.svg)](https://codecov.io/gh/nucleos/auto-merge-action)
[![Type Coverage](https://shepherd.dev/github/nucleos/auto-merge-action/coverage.svg)](https://shepherd.dev/github/nucleos/auto-merge-action)

This GitHub action will scan all open pull requests in the current project and merge them.

The pull request needs to be mergeable (no conflicts), got a green build and contains a label (default: `automerge`).
If the pull request contains an ignore label (default: `wip`), the pull request will be skipped.

After a succesul merge, the label is removed.

The action is designed to run asynchronously (e.g. every hour, once a day), so it can take a few minutes after the pull request got merged.

## Usage

You can create a new workflow that runs every day at 10 AM.

You should not use a very short interval otherwise you will reach the GitHub API limit.

```yaml
# .github/workflows/automatic-merge.yml
name: "Automatic Merge"

on:
  schedule:
    - cron:  '0 * * * *'

jobs:
  merge:
    name: "Merge Pull Requests"
    runs-on: ubuntu-latest

    steps:
    - name: "Merge"
      uses: "nucleos/auto-merge-action@1"
      env:
        "GITHUB_TOKEN": ${{ secrets.GITHUB_TOKEN }}
```
