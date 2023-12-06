
# Mailbox indicates unprocessed e-mail, no "new" to be seen

* Possible reason: duplicate Message-Id


# Unable to access mailbox content. Reason: cannot read - connection closed

Most likely, the IMAP options are not correctly specified.
They should at least include:

```
imap
ssl
```
