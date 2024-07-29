
# Mailbox indicates unprocessed e-mail, no "new" to be seen

* Possible reason: duplicate Message-Id


# Unable to access mailbox content. Reason: cannot read - connection closed

Most likely, the IMAP options are not correctly specified.
They should at least include:

```
imap
ssl
```

# HTML e-mails are processed as plain text (Exchange servers - on premises)

Solution provided by Dejan Skubic for Exchange (on premises) editions:

Run these commands in PowerShell:
``` 
Set-CASMailbox -Identity 'mailboxusername' -ImapUseProtocolDefaults $false
Set-CASMailbox -Identity 'mailboxusername' -ImapMessagesRetrievalMimeFormat HtmlOnly
 ```
 

